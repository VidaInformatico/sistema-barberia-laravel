<?php

namespace App\Livewire\Admin\Purchases;

use App\Models\Purchase;
use App\Models\Barber;
use App\Models\CashRegister;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;

class Edit extends Component
{
    public $searchSupplier = '';
    public $supplierResults = [];
    public $searchProduct = '';
    public $productResults = [];

    public $purchase_number;
    public $selectedSupplier = null;
    public $barber_id = '';
    public $cash_register_id;
    public $use_cash_register = false;
    public $notes;

    public $items = [];
    public $total = 0;
    public $paymentMethods = [];
    public $selectedMethod;
    public $method = 'CONTADO';
    public $payments = [];
    public $purchase;

    public function mount($purchaseId)
    {
        $this->purchase = Purchase::with('items')->findOrFail($purchaseId);
        $this->paymentMethods = PaymentMethod::all();
        $this->purchase_number = $this->purchase->purchase_number;
        $this->selectedSupplier = $this->purchase->supplier ? $this->purchase->supplier->toArray() : null;
        $this->searchSupplier = $this->purchase->supplier ? $this->purchase->supplier->company_name : '';
        $this->total = $this->purchase->total;
        $this->use_cash_register = $this->purchase->use_cash_register;
        $this->barber_id = $this->purchase->barber_id;
        $this->method = $this->purchase->credit > 0 ? 'CREDITO' : 'CONTADO';
        $this->notes = $this->purchase->notes;
        foreach ($this->purchase->payments as $payment) {
            $this->payments[] = [
                'method_id' => (int) $payment->payment_method_id,
                'amount'    => (float) $payment->received_amount,
                'reference' => $payment->reference ?? ''
            ];
            $this->selectedMethod = (int) $payment->payment_method_id;
        }
        foreach ($this->purchase->items as $item) {
            $this->items[] = [
                'product_id' => $item->product_id,
                'name'       => $item->product_name,
                'quantity'   => $item->quantity,
                'price' => (float) $item->price,
                'cost' => $item->cost,
                'itbis_rate' => (float) $item->itbis_rate,
                'is_exempt' => $item->is_exempt,
                'discount_rate' => $item->discount_rate,
                'itbis_amount' => $item->itbis_amount,
                'gross_subtotal' => $item->gross_subtotal,
                'discount_amount' => $item->discount_amount,
                'net_subtotal' => $item->total,
                'total' => $item->is_exempt,
            ];
        }
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

        $this->payments = [
            [
                'method_id' => (int) $this->selectedMethod,
                'amount'    => $this->total,
                'reference' => ''
            ]
        ];
    }

    public function removePaymentRow($index)
    {
        unset($this->payments[$index]);
        $this->payments = array_values($this->payments);
    }

    public function render()
    {
        $barbers = Barber::with('user')->get();
        return view('livewire.admin.purchases.edit', compact('barbers'));
    }

    public function resetPOS()
    {
        $this->reset([
            'selectedSupplier',
            'searchSupplier',
            'use_cash_register',
            'cash_register_id',
            'supplierResults',
            'searchProduct',
            'productResults',
            'barber_id',
            'selectedMethod',
            'method',
            'notes',
            'items',
            'total'
        ]);
        $this->resetValidation();
    }

    public function updatedSearchSupplier()
    {
        if (strlen($this->searchSupplier) < 2) {
            $this->supplierResults = [];
            return;
        }

        $this->supplierResults = Supplier::where('company_name', 'like', '%' . $this->searchSupplier . '%')
            ->orWhere('email', 'like', '%' . $this->searchSupplier . '%')
            ->take(5)
            ->get()
            ->toArray();
    }

    public function selectSupplier($supplier)
    {
        $this->selectedSupplier = $supplier;
        $this->searchSupplier = $supplier['name'];
        $this->supplierResults = [];
    }

    public function removeSupplier()
    {
        $this->selectedSupplier = null;
        $this->searchSupplier = '';
    }

    public function updatedSearchProduct()
    {
        if (strlen($this->searchProduct) < 2) {
            $this->productResults = [];
            return;
        }

        $this->productResults = Product::where('branch_id', Auth::user()->branch_id)->where('name', 'like', '%' . $this->searchProduct . '%')
            ->take(5)
            ->get()
            ->toArray();
    }

    public function addProductToCart($id)
    {
        $product = Product::find($id);
        foreach ($this->items as $index => $cartItem) {
            if ($cartItem['product_id'] == $product->id) {
                $this->items[$index]['quantity']++;
                $this->calculateTotals();
                $this->searchProduct = '';
                $this->productResults = [];
                return;
            }
        }

        $this->items[] = [
            'product_id' => $product->id,
            'name'       => $product->name,
            'price' => (float) $product->price,
            'cost' => $product->cost_price,
            'quantity' => 1,
            'itbis_rate' => (float) $product->tax,
            'is_exempt' => $product->is_exempt,
            'discount_rate' => 0,
            'itbis_amount' => 0,
            'gross_subtotal' => 0,
            'discount_amount' => 0,
            'net_subtotal' => 0,
            'total' => 0,
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

    public function update()
    {
        $this->validate([
            'selectedSupplier' => ['nullable', 'array'],
            'barber_id'        => ['nullable', 'exists:barbers,id'],
            'items'            => ['required', 'array', 'min:1'],
        ], [
            'items.required'   => 'El carrito de compras no puede estar vacío.'
        ]);

        if ($this->method == 'CONTADO' && empty($this->payments)) {
            LivewireAlert::title('Debe agregar al menos un método de pago')->warning()->asToast()->show();
            return;
        }

        if ($this->method == 'CONTADO' && ($this->paid < $this->total)) {
            LivewireAlert::title('El monto pagado es insuficiente')->warning()->asToast()->show();
            return;
        }

        if (empty($this->selectedSupplier['id'])) {
            LivewireAlert::title('El proveedor es requerido para compras a crédito')->warning()->asToast()->show();
            return;
        }

        $oldCashRegisterId = $this->purchase->cash_register_id;

        if ($this->use_cash_register) {
            $cash_register = CashRegister::where('status', 'open')
                ->where('opened_by', Auth::user()->id)
                ->whereDate('opened_at', now()->toDateString())
                ->first();

            if (!$cash_register) {
                LivewireAlert::title('La caja está cerrada')->warning()->asToast()->show();
                return;
            }

            $currentPayment = collect($this->payments)->first();
            $methodId = $currentPayment ? (int)$currentPayment['method_id'] : null;
            $isEfectivo = PaymentMethod::find($methodId)?->is_efectivo ?? false;

            if (!$isEfectivo) {
                LivewireAlert::title('Para usar dinero de caja, el método de pago debe ser Efectivo.')->warning()->asToast()->show();
                return;
            }

            $oldEfectivoAmount = 0;
            foreach ($this->purchase->payments as $oldPayment) {
                $oldIsEfectivo = PaymentMethod::find($oldPayment->payment_method_id)?->is_efectivo ?? false;
                if ($oldIsEfectivo) {
                    $oldEfectivoAmount += $oldPayment->amount;
                }
            }

            $simulatedBalance = ($oldCashRegisterId === $cash_register->id)
                ? $cash_register->current_amount + $oldEfectivoAmount
                : $cash_register->current_amount;

            if ($simulatedBalance < $this->total) {
                LivewireAlert::title('Saldo Insuficiente en Caja (Excederá el límite disponible)')->warning()->asToast()->show();
                return;
            }

            $this->cash_register_id = $cash_register->id;
        }

        try {
            DB::transaction(function () use ($oldCashRegisterId) {

                foreach ($this->purchase->items as $oldItem) {
                    Product::where('id', $oldItem->product_id)
                        ->where('type', 'product')
                        ->increment('stock', $oldItem->quantity);
                }

                if ($oldCashRegisterId) {
                    foreach ($this->purchase->payments as $oldPayment) {
                        $oldIsEfectivo = PaymentMethod::find($oldPayment->payment_method_id)?->is_efectivo ?? false;

                        if ($oldIsEfectivo && $oldPayment->amount > 0) {
                            CashRegister::find($oldCashRegisterId)
                                ->increment('current_amount', $oldPayment->amount);
                        }
                    }
                }

                $this->purchase->items()->delete();
                $this->purchase->payments()->delete();

                $gross_total = 0;
                $tax_total = 0;
                $net_total = 0;
                $discount_total = 0;

                $credit = ($this->method == 'CONTADO') ? 0 : $this->total;
                $change = ($this->method == 'CONTADO') ? ($this->paid - $this->total) : 0;
                $remainingChange = $change;

                $supplier_id = empty($this->selectedSupplier['id']) ? null : $this->selectedSupplier['id'];

                if (!$this->use_cash_register) {
                    $this->cash_register_id = null;
                }

                $this->purchase->update([
                    'supplier_id'      => $supplier_id,
                    'user_id'        => Auth::id(),
                    'cash_register_id' => $this->cash_register_id,
                    'use_cash_register' => $this->use_cash_register,
                    'total'            => $this->total,
                    'credit'           => $credit,
                    'paid_amount'      => $this->paid,
                    'change'           => $change,
                    'notes'            => $this->notes,
                ]);

                foreach ($this->items as $item) {
                    $gross_total += $item['gross_subtotal'];
                    $tax_total += $item['itbis_amount'];
                    $net_total += $item['net_subtotal'];
                    $discount_total += $item['discount_amount'];

                    $this->purchase->items()->create([
                        'product_id'      => $item['product_id'],
                        'product_name'    => $item['name'],
                        'quantity'        => $item['quantity'],
                        'price'           => $item['price'],
                        'itbis_rate'      => $item['itbis_rate'],
                        'itbis_amount'    => $item['itbis_amount'],
                        'gross_subtotal'  => $item['gross_subtotal'],
                        'discount_rate'   => $item['discount_rate'],
                        'discount_amount' => $item['discount_amount'],
                        'net_subtotal'    => $item['net_subtotal'],
                        'total'           => $item['total'],
                        'is_exempt'       => $item['is_exempt']
                    ]);

                    Product::where('id', $item['product_id'])
                        ->where('type', 'product')
                        ->decrement('stock', $item['quantity']);
                }

                foreach ($this->payments as $payment) {
                    $receivedAmount = (float)$payment['amount'];
                    if ($receivedAmount <= 0) continue;

                    $methodId = (int)$payment['method_id'];
                    $method = collect($this->paymentMethods)->first(fn($m) => (int)$m->id === $methodId);

                    $returnedAmount = 0;
                    $realAmount = $receivedAmount;

                    if ($method && (bool)$method->is_efectivo && $remainingChange > 0) {
                        $returnedAmount = min($receivedAmount, $remainingChange);
                        $realAmount = $receivedAmount - $returnedAmount;
                        $remainingChange -= $returnedAmount;
                    }

                    $this->purchase->payments()->create([
                        'payment_method_id' => $methodId,
                        'amount'            => $realAmount,
                        'received_amount'   => $receivedAmount,
                        'returned_amount'   => $returnedAmount,
                        'reference'         => $payment['reference'] ?? null,
                    ]);

                    $isEfectivo = $method?->is_efectivo ?? false;

                    if ($isEfectivo && $realAmount > 0 && $this->use_cash_register && $this->cash_register_id) {
                        CashRegister::find($this->cash_register_id)
                            ->decrement('current_amount', $realAmount);
                    }
                }

                $this->purchase->update([
                    'gross_total'    => $gross_total,
                    'tax_total'      => $tax_total,
                    'net_total'      => $net_total,
                    'discount_total' => $discount_total
                ]);
            });

            LivewireAlert::title('La compra ha sido actualizada correctamente.')->success()->asToast()->show();
            return redirect()->to(route('admin.purchases.index'));
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Error Crítico',
                'text'  => 'Error al consolidar la compra: ' . $e->getMessage(),
                'icon'  => 'error',
            ]);
        }
    }
}
