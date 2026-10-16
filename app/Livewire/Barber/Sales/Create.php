<?php

namespace App\Livewire\Barber\Sales;

use App\Models\Sale;
use App\Models\Barber;
use App\Models\CashRegister;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

class Create extends Component
{
    public $searchClient = '';
    public $searchProduct = '';
    public $productResults = [];

    public $sale_number;
    public $clientId;
    public $selectedClient = null;
    public $cash_register_id;
    public $notes;

    public $items = [];
    public $total = 0;
    public $paymentMethods = [];
    public $selectedMethod;
    public $method = 'CONTADO';
    public $payments = [];

    public function mount()
    {
        $this->cash_register_id = CashRegister::where('status', 'open')
            ->where('opened_by', Auth::user()->id)
            ->whereDate('opened_at', now()->toDateString())
            ->first()?->id;

        if (!$this->cash_register_id) {
            session()->flash('message', 'Debes abrir una caja primero para continuar.');
            return redirect()->to(route('barber.cash.register'));
        }
        $this->paymentMethods = PaymentMethod::all();
        $this->sale_number = 'VEN-' . str_pad((Sale::withTrashed()->max('id') + 1), 6, '0', STR_PAD_LEFT);
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

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function getPaidProperty()
    {
        return collect($this->payments)->sum(fn($p) => (float) ($p['amount'] ?? 0));
    }

    public function getChangeProperty()
    {
        $totalPagadoGeneral = (float)$this->paid;
        $montoADeber = (float)$this->total;

        if ($totalPagadoGeneral > $montoADeber) {
            return $totalPagadoGeneral - $montoADeber;
        }

        return 0;
    }

    public function addPaymentFromSelect()
    {
        if (!$this->selectedMethod) return;

        $exists = collect($this->payments)->pluck('method_id')->contains($this->selectedMethod);
        if ($exists) return;

        $this->payments[] = [
            'method_id' => (int) $this->selectedMethod,
            'amount' => 0,
            'reference' => ''
        ];

        $this->selectedMethod = null;
    }

    public function removePaymentRow($index)
    {
        unset($this->payments[$index]);
        $this->payments = array_values($this->payments);
    }

    public function render()
    {
        $barbers = Barber::with('user')->get();
        return view('livewire.barber.sales.create', compact('barbers'));
    }

    public function resetPOS()
    {
        $this->reset([
            'clientId',
            'selectedClient',
            'searchProduct',
            'productResults',
            'selectedMethod',
            'method',
            'notes',
            'items',
            'total'
        ]);
        $this->resetValidation();
    }

    public function updatedSearchProduct()
    {
        if (strlen($this->searchProduct) < 2) {
            $this->productResults = [];
            return;
        }

        $this->productResults = Product::where('branch_id', Auth::user()->branch_id)
            ->where('name', 'like', '%' . $this->searchProduct . '%')
            ->take(5)
            ->get()
            ->toArray();
    }

    public function addProductToCart($id)
    {
        $product = Product::with('comboItems')->find($id);

        foreach ($this->items as $index => $cartItem) {
            if ($cartItem['product_id'] == $product->id) {
                $this->items[$index]['quantity']++;
                $this->calculateTotals();
                $this->searchProduct = '';
                $this->productResults = [];
                return;
            }
        }

        $comboComponents = $product->type === 'combo'
            ? $product->comboItems->map(fn($item) => [
                'product_id' => $item->id,
                'name'       => $item->name,
                'quantity'   => $item->pivot->quantity
            ])->toArray()
            : [];

        $this->items[] = [
            'product_id'      => $product->id,
            'name'            => $product->name,
            'type'            => $product->type,
            'combo_components' => $comboComponents,
            'quantity'        => 1,
            'price'           => (float) $product->price,
            'cost'            => $product->cost_price,
            'itbis_rate'      => (float) $product->tax,
            'is_exempt'       => $product->is_exempt,
            'discount_rate'   => 0,
            'itbis_amount'    => 0,
            'gross_subtotal'  => 0,
            'discount_amount' => 0,
            'net_subtotal'    => 0,
            'total'           => 0,
        ];

        $this->calculateTotals();
        $this->searchProduct = '';
        $this->productResults = [];
    }

    public function updateQuantity($index, $qty)
    {
        if ($qty < 1) return;
        $this->items[$index]['quantity'] = (int)$qty;
        $this->calculateTotals();
    }

    public function updateDiscount($index, $discount)
    {
        if ($discount < 1) return;
        $this->items[$index]['discount_rate'] = (int)$discount;
        $this->calculateTotals();
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotals();
    }

    public function updatedDiscount()
    {
        $this->calculateTotals();
    }

    private function calculateTotals()
    {
        $this->total = 0;

        foreach ($this->items as &$item) {
            $priceWithTax = (float) $item['price'];
            $rate = (float) $item['itbis_rate'];
            $qty = (float) $item['quantity'];

            $basePrice = $priceWithTax / (1 + ($rate / 100));

            $grossSubtotal = $basePrice * $qty;

            $discountAmount = $grossSubtotal * ($item['discount_rate'] / 100);

            $netSubtotal = $grossSubtotal - $discountAmount;

            $itbisAmount = $netSubtotal * ($rate / 100);

            $totalLine = $netSubtotal + $itbisAmount;

            $item['gross_subtotal'] = round($grossSubtotal, 2);
            $item['discount_amount'] = round($discountAmount, 2);
            $item['net_subtotal'] = round($netSubtotal, 2);
            $item['itbis_amount'] = round($itbisAmount, 2);
            $item['total'] = round($totalLine, 2);

            $this->total += $item['total'];
        }
    }

    protected function validateStockAvailability()
    {
        foreach ($this->items as $item) {
            $itemType = $item['type'] ?? 'product';

            if ($itemType === 'product') {
                $product = Product::find($item['product_id']);

                if ($product && $product->is_inventoriable && $product->stock < $item['quantity']) {
                    $this->alertError("Stock insuficiente para: {$product->name} (Disponible: {$product->stock})");
                    return false;
                }
            } elseif ($itemType === 'combo') {
                if (!empty($item['combo_components'])) {
                    foreach ($item['combo_components'] as $component) {
                        $compProduct = Product::find($component['product_id']);

                        if ($compProduct && $compProduct->is_inventoriable) {
                            $requiredStock = $component['quantity'] * $item['quantity'];

                            if ($compProduct->stock < $requiredStock) {
                                $this->alertError("Stock insuficiente en combo '{$item['name']}' para el componente: {$compProduct->name} (Disponible: {$compProduct->stock})");
                                return false;
                            }
                        }
                    }
                }
            }
        }

        return true;
    }

    protected function alertError($message)
    {
        \Jantinnerezo\LivewireAlert\Facades\LivewireAlert::title('Error de Inventario')
            ->text($message)
            ->error()
            ->asToast()
            ->show();
    }

    public function store()
    {
        try {
            $this->validateStockAvailability();
        } catch (\Exception $e) {
            $this->alertError('error', $e->getMessage());
            return;
        }

        $this->validate([
            'sale_number'    => ['required', 'string', 'unique:sales,sale_number'],
            'clientId' => ['required'],
            'items'          => ['required', 'array', 'min:1'],
        ], [
            'items.required'          => 'El carrito de compras no puede estar vacío.'
        ]);

        if ($this->method == 'CONTADO' && empty($this->payments)) {
            LivewireAlert::title('Debe agregar al menos un método de pago')
                ->warning()
                ->asToast()
                ->show();
            return;
        }

        if ($this->method == 'CONTADO' && ($this->paid < $this->total)) {
            LivewireAlert::title('El monto pagado es insuficiente')
                ->warning()
                ->asToast()
                ->show();
            return;
        }

        if ($this->method == 'CREDITO' && empty($this->clientId)) {
            LivewireAlert::title('El cliente es requerido para ventas a crédito')
                ->warning()
                ->asToast()
                ->show();
            return;
        }

        try {
            DB::transaction(function () {

                $gross_total = 0;
                $tax_total = 0;
                $net_total = 0;
                $discount_total = 0;

                $credit = ($this->method == 'CONTADO') ? 0 : $this->total;

                $change = ($this->method == 'CONTADO') ? ($this->paid - $this->total) : 0;

                $remainingChange = $change;
                $client_id = empty($this->clientId) ? null : $this->clientId;

                $sale = Sale::create([
                    'sale_number'    => $this->sale_number,
                    'client_id'      => $client_id,
                    'barber_id'      => Auth::user()->barber->id,
                    'cash_register_id' => $this->cash_register_id,
                    'branch_id'         => Auth::user()->branch_id,
                    'created_by'     => Auth::id(),
                    'total' => $this->total,
                    'credit' => $credit,
                    'gross_total' => $gross_total,
                    'tax_total' => $tax_total,
                    'net_total' => $net_total,
                    'discount_total' => $discount_total,
                    'paid_amount' => $this->paid,
                    'change' => $change,
                    'notes'          => $this->notes,
                ]);
                foreach ($this->items as $item) {

                    $gross_total += $item['gross_subtotal'];
                    $tax_total += $item['itbis_amount'];
                    $net_total += $item['net_subtotal'];
                    $discount_total += $item['discount_amount'];

                    $saleItem = $sale->items()->create([
                        'product_id'   => $item['product_id'],
                        'product_name' => $item['name'],
                        'quantity'     => $item['quantity'],
                        'price'     => $item['price'],
                        'itbis_rate'      => $item['itbis_rate'],
                        'itbis_amount'    => $item['itbis_amount'],
                        'gross_subtotal'  => $item['gross_subtotal'],
                        'discount_rate'   => $item['discount_rate'],
                        'discount_amount' => $item['discount_amount'],
                        'net_subtotal'    => $item['net_subtotal'],
                        'total'           => $item['total'],
                        'is_exempt'       => $item['is_exempt']
                    ]);

                    if (isset($item['type']) && $item['type'] === 'combo' && !empty($item['combo_components'])) {
                        foreach ($item['combo_components'] as $component) {

                            $unitQuantity = $component['quantity'];
                            $totalQuantity = $unitQuantity * $item['quantity'];

                            $saleItem->comboItems()->create([
                                'product_id'     => $component['product_id'],
                                'product_name'   => $component['name'],
                                'unit_quantity'  => $unitQuantity,
                                'total_quantity' => $totalQuantity,
                            ]);

                            Product::where('id', $component['product_id'])
                                ->where('type', 'product')
                                ->decrement('stock', $totalQuantity);
                        }
                    } else {
                        Product::where('id', $item['product_id'])
                            ->where('type', 'product')
                            ->decrement('stock', $item['quantity']);
                    }
                }

                foreach ($this->payments as $payment) {

                    $receivedAmount = (float)$payment['amount'];

                    if ($receivedAmount <= 0) continue;

                    $methodId = (int)$payment['method_id'];

                    $method = collect($this->paymentMethods)
                        ->first(fn($m) => (int)$m->id === $methodId);

                    $returnedAmount = 0;
                    $realAmount = $receivedAmount;

                    if ($method && (bool)$method->is_efectivo && $remainingChange > 0) {
                        $returnedAmount = min($receivedAmount, $remainingChange);
                        $realAmount = $receivedAmount - $returnedAmount;
                        $remainingChange -= $returnedAmount;
                    }

                    $sale->payments()->create([
                        'payment_method_id' => $methodId,
                        'amount' => $realAmount,
                        'received_amount' => $receivedAmount,
                        'returned_amount' => $returnedAmount,
                        'reference' => $payment['reference'] ?? null,
                    ]);

                    $isEfectivo = PaymentMethod::find($methodId)?->is_efectivo ?? false;

                    if ($isEfectivo && $realAmount > 0) {
                        CashRegister::find($this->cash_register_id)
                            ->increment('current_amount', $realAmount);
                    }
                }

                $sale->update([
                    'gross_total' => $gross_total,
                    'tax_total' => $tax_total,
                    'net_total' => $net_total,
                    'discount_total' => $discount_total
                ]);
            });

            LivewireAlert::title('La venta ha sido guardada y procesada correctamente.')
                ->success()
                ->asToast()
                ->show();

            return redirect()->to(route('barber.sales.index'));
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Error Crítico',
                'text'  => 'Error al consolidar la venta: ' . $e->getMessage(),
                'icon'  => 'error',
            ]);
        }
    }
}
