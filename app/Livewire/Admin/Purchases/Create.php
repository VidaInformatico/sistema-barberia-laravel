<?php

namespace App\Livewire\Admin\Purchases;

use App\Models\CashRegister;
use App\Models\Expense;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;

class Create extends Component
{
    public $searchSupplier = '';
    public $supplierResults = [];
    public $searchProduct = '';
    public $productResults = [];

    public $purchase_number;
    public $selectedSupplier = null;
    public $use_cash_register = false;
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
        $this->paymentMethods = PaymentMethod::all();
        $this->purchase_number = 'COM-' . str_pad((Purchase::where('branch_id', Auth::user()->branch_id)->withTrashed()->max('id') + 1), 6, '0', STR_PAD_LEFT);
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
        return view('livewire.admin.purchases.create');
    }

    public function resetPOS()
    {
        $this->reset([
            'selectedSupplier',
            'searchSupplier',
            'supplierResults',
            'searchProduct',
            'productResults',
            'use_cash_register',
            'cash_register_id',
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
        $this->searchSupplier = $supplier['company_name'];
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

        $this->productResults = Product::where('branch_id', Auth::user()->branch_id)->where('type', 'product')->where('name', 'like', '%' . $this->searchProduct . '%')
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
            'quantity'   => 1,
            'price' => (float) $product->cost_price,
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

    public function store()
    {
        $this->validate([
            'purchase_number'   => ['required', 'string', 'unique:purchases,purchase_number'],
            'selectedSupplier'  => ['nullable', 'array'],
            'use_cash_register' => ['required', 'boolean'],
            'items'             => ['required', 'array', 'min:1'],
        ], [
            'items.required'    => 'El carrito de compras no puede estar vacío.'
        ]);

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
                LivewireAlert::title('Para usar dinero de caja, el método de pago debe ser Efectivo.')
                    ->warning()->asToast()->show();
                return;
            }

            if ($cash_register->current_amount < $this->total) {
                LivewireAlert::title('Saldo Insuficiente en Caja')->warning()->asToast()->show();
                return;
            }

            $this->cash_register_id = $cash_register->id;
        }

        if ($this->method == 'CONTADO' && empty($this->payments)) {
            LivewireAlert::title('Debe agregar al menos un método de pago')->warning()->asToast()->show();
            return;
        }

        if ($this->method == 'CONTADO' && ($this->paid < $this->total)) {
            LivewireAlert::title('El monto pagado es insuficiente')->warning()->asToast()->show();
            return;
        }

        if (empty($this->selectedSupplier['id'])) {
            LivewireAlert::title('El proveedor es requerido para ventas a crédito')->warning()->asToast()->show();
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

                $supplier_id = empty($this->selectedSupplier['id']) ? null : $this->selectedSupplier['id'];
                $cash_register_id = empty($this->use_cash_register) ? null : $this->cash_register_id;

                $purchase = Purchase::create([
                    'purchase_number'   => $this->purchase_number,
                    'supplier_id'       => $supplier_id,
                    'cash_register_id'  => $cash_register_id,
                    'use_cash_register' => $this->use_cash_register,
                    'branch_id'         => Auth::user()->branch_id,
                    'user_id'        => Auth::id() ?? 1,
                    'total'             => $this->total,
                    'credit'            => $credit,
                    'gross_total'       => $gross_total,
                    'tax_total'         => $tax_total,
                    'net_total'         => $net_total,
                    'discount_total'    => $discount_total,
                    'paid_amount'       => $this->paid,
                    'change'            => $change,
                    'purchase_date'    => now(),
                    'notes'             => $this->notes,
                ]);

                foreach ($this->items as $item) {
                    $gross_total += $item['gross_subtotal'];
                    $tax_total += $item['itbis_amount'];
                    $net_total += $item['net_subtotal'];
                    $discount_total += $item['discount_amount'];

                    $purchase->items()->create([
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
                        ->increment('stock', $item['quantity']);
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

                    $purchase->payments()->create([
                        'payment_method_id' => $methodId,
                        'amount'            => $realAmount,
                        'received_amount'   => $receivedAmount,
                        'returned_amount'   => $returnedAmount,
                        'reference'         => $payment['reference'] ?? null,
                    ]);

                    if ($this->use_cash_register && $realAmount > 0) {
                        CashRegister::find($this->cash_register_id)
                            ->decrement('current_amount', $realAmount);

                        Expense::create([
                            'cash_register_id'  => $this->cash_register_id,
                            'payment_method_id' => $methodId,
                            'user_id'           => Auth::id() ?? 1,
                            'branch_id'         => Auth::user()->branch_id,
                            'amount'            => $realAmount,
                            'itbis'             => 0,
                            'other_taxes'       => 0,
                            'total_amount'      => $realAmount,
                            'concept'           => 'Compra de mercancía / insumos',
                            'description'       => 'Salida de caja automática por la Compra N°: ' . $this->purchase_number,
                            'expense_date'      => now(),
                        ]);
                    }
                }

                $purchase->update([
                    'gross_total'    => $gross_total,
                    'tax_total'      => $tax_total,
                    'net_total'      => $net_total,
                    'discount_total' => $discount_total
                ]);
            });

            LivewireAlert::title('La compra ha sido guardada y procesada correctamente.')
                ->success()->asToast()->show();

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
