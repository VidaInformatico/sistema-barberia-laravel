<?php

namespace App\Livewire\Admin\Bookings;

use App\Models\Booking;
use App\Models\Barber;
use App\Models\User;
use App\Models\BarberSchedule;
use App\Models\BarberBlockedTime;
use App\Models\Product;
use App\Models\ProductCategory;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

class Create extends Component
{
    public $clientId = '';
    public $barberId = '';
    public $selectedDate = '';
    public $selectedTime = '';
    public $searchService = '';
    public $selectedClient = null;

    public $currentMonth;
    public $currentYear;

    public $selectedCategory = '';
    public $selectedServiceId = null;

    protected function rules()
    {
        return [
            'clientId' => 'required|exists:users,id',
            'barberId' => 'required|exists:barbers,id',
            'selectedDate' => 'required|date|after_or_equal:today',
            'selectedTime' => 'required',
            'selectedServiceId' => 'required',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'clientId' => 'cliente',
            'barberId' => 'barbero',
            'selectedDate' => 'fecha de reserva',
            'selectedTime' => 'horario disponible',
            'selectedServiceId' => 'servicio',
        ];
    }

    public function mount()
    {
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
        $this->selectedDate = now()->format('Y-m-d');
    }

    #[On('clientSelected')]
    public function setClient($clientId)
    {
        $this->clientId = $clientId;
        $this->selectedClient = User::find($clientId);
    }

    public function removeClient()
    {
        $this->clientId = null;
        $this->selectedClient = null;
    }

    public function changeMonth($direction)
    {
        $date = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1);
        if ($direction === 'next') {
            $date->addMonth();
        } else {
            $date->subMonth();
        }

        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
    }

    public function selectDate($day)
    {
        $this->selectedDate = Carbon::createFromDate($this->currentYear, $this->currentMonth, $day)->format('Y-m-d');
        $this->selectedTime = '';
    }

    public function getSelectedServiceProperty()
    {
        if (!$this->selectedServiceId) {
            return null;
        }

        $service = Product::find($this->selectedServiceId);

        // Aplicar precio personalizado si existe
        $customPrice = DB::table('barber_services')
            ->where('barber_id', $this->barberId)
            ->where('product_id', $this->selectedServiceId)
            ->value('custom_price');

        if ($customPrice !== null) {
            $service->price = $customPrice;
        }

        return $service;
    }

    #[Computed()]
    public function totalPriceSum()
    {
        return $this->getSelectedServiceProperty()?->price ?? 0;
    }

    #[Computed()]
    public function totalDurationMinutes()
    {
        return $this->getSelectedServiceProperty()?->duration_minutes ?? 30;
    }

    #[Computed()]
    public function totalDurationFormatted()
    {
        $service = $this->getSelectedServiceProperty();
        $totalMinutes = $service ? $service->duration_minutes : 0;

        if ($totalMinutes === 0) return '0 min';

        $h = intdiv($totalMinutes, 60);
        $m = $totalMinutes % 60;

        if ($h > 0 && $m > 0) return "{$h}h {$m}min";
        if ($h > 0) return "{$h}h";
        return "{$m}min";
    }

    public function getCalendarDaysProperty()
    {
        if (!$this->barberId) return [];

        $firstDayOfMonth = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1);
        $daysInMonth = $firstDayOfMonth->daysInMonth;
        $dayOfWeek = $firstDayOfMonth->dayOfWeekIso;

        $days = [];

        for ($i = 1; $i < $dayOfWeek; $i++) {
            $days[] = ['blank' => true];
        }

        $blockedFullDays = BarberBlockedTime::where('barber_id', $this->barberId)
            ->whereYear('date', $this->currentYear)
            ->whereMonth('date', $this->currentMonth)
            ->whereNull('start_time')
            ->pluck('date')
            ->map(fn($date) => Carbon::parse($date)->day)
            ->toArray();

        $activeSchedules = BarberSchedule::where('barber_id', $this->barberId)
            ->where('is_active', true)
            ->pluck('day_of_week')
            ->toArray();

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $loopDate = Carbon::createFromDate($this->currentYear, $this->currentMonth, $day);
            $dayOfWeekNum = $loopDate->dayOfWeek;

            $isBlocked = in_array($day, $blockedFullDays) || !in_array($dayOfWeekNum, $activeSchedules);

            $days[] = [
                'blank' => false,
                'day' => $day,
                'isPast' => $loopDate->isPast() && !$loopDate->isToday(),
                'isSunday' => $loopDate->isSunday(),
                'isBusy' => $isBlocked,
                'dateString' => $loopDate->format('Y-m-d')
            ];
        }

        return $days;
    }

    public function getAvailableHoursProperty()
    {
        if (!$this->selectedDate || !$this->barberId) return [];

        $carbonDate = Carbon::parse($this->selectedDate);
        $dayOfWeekNum = $carbonDate->dayOfWeek;

        $schedule = BarberSchedule::where('barber_id', $this->barberId)
            ->where('day_of_week', $dayOfWeekNum)
            ->where('is_active', true)
            ->first();

        if (!$schedule) return [];

        $fullDayBlock = BarberBlockedTime::where('barber_id', $this->barberId)
            ->where('date', $this->selectedDate)
            ->whereNull('start_time')
            ->exists();

        if ($fullDayBlock) return [];

        $startTime = Carbon::parse($schedule->start_time);
        $endTime = Carbon::parse($schedule->end_time);
        $slots = [];

        $duration = $this->totalDurationMinutes;

        while ($startTime->clone()->addMinutes($duration)->lte($endTime)) {
            $slots[] = $startTime->format('H:i');
            $startTime->addMinutes($duration);
        }

        $bookedTimes = Booking::where('booking_date', $this->selectedDate)
            ->where('barber_id', $this->barberId)
            ->whereNotIn('status', [Booking::STATUS_CANCELLED])
            ->get(['start_time', 'end_time'])
            ->map(function ($booking) {
                return [
                    'start' => Carbon::parse($booking->start_time)->format('H:i'),
                    'end' => Carbon::parse($booking->end_time)->format('H:i')
                ];
            })->toArray();

        $partialBlocks = BarberBlockedTime::where('barber_id', $this->barberId)
            ->where('date', $this->selectedDate)
            ->whereNotNull('start_time')
            ->get(['start_time', 'end_time'])
            ->map(function ($block) {
                return [
                    'start' => Carbon::parse($block->start_time)->format('H:i'),
                    'end' => Carbon::parse($block->end_time)->format('H:i')
                ];
            })->toArray();

        $allRestrictions = array_merge($bookedTimes, $partialBlocks);
        $isToday = $carbonDate->isToday();
        $now = Carbon::now();
        $hoursList = [];
        foreach ($slots as $slot) {
            $slotStart = Carbon::parse($slot);
            $slotEnd = $slotStart->clone()->addMinutes($duration);
            $isAvailable = true;

            if ($isToday) {
                $fullSlotDateTime = Carbon::parse($this->selectedDate . ' ' . $slot);
                if ($fullSlotDateTime->lte($now)) {
                    $isAvailable = false;
                }
            }

            foreach ($allRestrictions as $restriction) {
                $restStart = Carbon::parse($restriction['start']);
                $restEnd = Carbon::parse($restriction['end']);

                if ($slotStart->lt($restEnd) && $slotEnd->gt($restStart)) {
                    $isAvailable = false;
                    break;
                }
            }

            $hoursList[] = [
                'time' => $slot,
                'formatted' => $slotStart->format('h:i A'),
                'available' => $isAvailable
            ];
        }

        return $hoursList;
    }

    public function storeBooking()
    {
        $this->validate();

        $carbonDate = Carbon::parse($this->selectedDate);
        $dayOfWeekNum = $carbonDate->dayOfWeek;

        $schedule = BarberSchedule::where('barber_id', $this->barberId)
            ->where('day_of_week', $dayOfWeekNum)
            ->where('is_active', true)
            ->first();

        if (!$schedule) {
            $this->addError('selectedTime', 'El barbero no atiende este día.');
            return;
        }

        $serviceObj = $this->getSelectedServiceProperty();

        $slotStart = Carbon::parse($this->selectedTime);
        $slotEnd = $slotStart->clone()->addMinutes((int)$serviceObj->duration_minutes);

        if ($slotStart->lt(Carbon::parse($schedule->start_time)) || $slotEnd->gt(Carbon::parse($schedule->end_time))) {
            $this->addError('selectedTime', 'El horario excede la jornada laboral.');
            return;
        }

        DB::transaction(function () use ($slotStart, $slotEnd, $serviceObj) {

            $exists = Booking::where('booking_date', $this->selectedDate)
                ->where('barber_id', $this->barberId)
                ->whereNotIn('status', [Booking::STATUS_CANCELLED])
                ->where('start_time', '<', $slotEnd->format('H:i:s'))
                ->where('end_time', '>', $slotStart->format('H:i:s'))
                ->lockForUpdate()
                ->exists();

            if ($exists) {
                throw new \Exception('Este horario ya no se encuentra disponible.');
            }

            $isBlocked = BarberBlockedTime::where('barber_id', $this->barberId)
                ->where('date', $this->selectedDate)
                ->where(function ($query) use ($slotStart, $slotEnd) {
                    $query->whereNull('start_time')
                        ->orWhere(function ($q) use ($slotStart, $slotEnd) {
                            $q->where('start_time', '<', $slotEnd->format('H:i:s'))
                                ->where('end_time', '>', $slotStart->format('H:i:s'));
                        });
                })->exists();

            if ($isBlocked) {
                throw new \Exception('Este horario está bloqueado por el barbero.');
            }

            // Crear la reserva
            $booking = Booking::create([
                'client_id' => $this->clientId,
                'barber_id' => $this->barberId,
                'branch_id'         => Auth::user()->branch_id,
                'booking_date' => $this->selectedDate,
                'start_time' => $slotStart->format('H:i:s'),
                'end_time' => $slotEnd->format('H:i:s'),
                'status' => Booking::STATUS_CONFIRMED,
                'total_price' => $serviceObj->price,
                'confirmed_at' => now()
            ]);

            $booking->services()->create([
                'product_id' => $serviceObj->id,
                'service_name' => $serviceObj->name,
                'duration_minutes' => $serviceObj->duration_minutes,
                'price' => $serviceObj->price
            ]);

            //IMPRESION DIRECTA
            $user = User::select('bluetooth_printing', 'bluetooth_print_size', 'direct_pc_printing', 'pc_print_size', 'pc_printer_name')->find(Auth::id());
            $this->dispatch('print-booking', [
                'url' => route('printer.ticket.booking', Crypt::encrypt($booking->id)),
                'bluetooth_printing' => $user->bluetooth_printing,
                'bluetooth_print_size' => $user->bluetooth_print_size,
                'direct_pc_printing' => $user->direct_pc_printing,
                'pc_print_size' => $user->pc_print_size,
                'pc_printer_name' => $user->pc_printer_name
            ]);
        });

        $this->dispatch('swal', [
            'title' => 'Aviso',
            'text'  => '¡Reserva creada exitosamente!',
            'icon'  => 'success',
        ]);
        return $this->redirect(route('admin.bookings.index'), navigate: true);
    }

    public function updatedSelectedServiceId()
    {
        $this->barberId = '';
        $this->selectedTime = '';

        $this->selectedServiceId = (int) $this->selectedServiceId;
    }

    public function updatedSelectedCategory()
    {
        $this->selectedServiceId = '';
        $this->barberId = '';
        $this->selectedTime = '';
        $this->searchService = '';
    }

    public function render()
    {
        $categories = ProductCategory::where('type', 'service')->where('is_active', true)->orderBy('sort_order')->get();

        $availableServices = Product::where('type', 'service')
            ->where('branch_id', Auth::user()->branch_id)
            ->where('is_active', true)
            ->when($this->selectedCategory, function ($query) {
                $query->where('category_id', $this->selectedCategory);
            })
            ->when($this->searchService, function ($query) {
                $query->where('name', 'like', '%' . $this->searchService . '%');
            })
            ->orderBy('sort_order')
            ->take(6)
            ->get();

        $barbers = collect();

        if (!empty($this->selectedServiceId)) {
            $barbers = Barber::with('user', 'services')
                ->whereHas('user', function ($query) {
                    $query->where('branch_id', Auth::user()->branch_id);
                })
                ->whereHas('services', function ($query) {
                    $query->where('products.id', $this->selectedServiceId);
                })
                ->get()
                ->map(function ($barber) {
                    $barber->total_services_price = DB::table('barber_services')
                        ->where('barber_id', $barber->id)
                        ->where('product_id', $this->selectedServiceId)
                        ->value('custom_price') ?? 0;
                    return $barber;
                });
        }

        return view('livewire.admin.bookings.create', [
            'barbers' => $barbers,
            'categories' => $categories,
            'availableServices' => $availableServices,
            'monthName' => Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->translatedFormat('F Y')
        ]);
    }
}
