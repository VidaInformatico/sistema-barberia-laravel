<?php

namespace App\Livewire\Admin\Sales;

use App\Models\Sale;
use App\Models\Barber;
use App\Models\CashRegister;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;

class Edit extends Component
{
    public $searchClient = '';
    public $clientResults = [];
    public $searchProduct = '';
    public $productResults = [];

    public $sale_number;
    public $selectedClient = null;
    public $barber_id = '';
    public $cash_register_id;
    public $notes;

    public $items = [];
    public $total = 0;
    public $paymentMethods = [];
    public $selectedMethod;
    public $method = 'CONTADO';
    public $payments = [];
    public $sale;

    public function mount($saleId)
    {
        $this->cash_register_id = CashRegister::where('status', 'open')
        ->where('opened_by', Auth::user()->id)
            ->whereDate('opened_at', now()->toDateString())
            ->first()?->id;

        if (!$this->cash_register_id) {
            session()->flash('message', 'Debes abrir una caja primero para continuar.');
            return redirect()->to(route('admin.boxes.index'));
        }

        $this->sale = Sale::with(['items.comboItems', 'payments'])->findOrFail($saleId);
        $this->paymentMethods = PaymentMethod::all();
        $this->sale_number = $this->sale->sale_number;
        $this->selectedClient = $this->sale->client ? $this->sale->client->toArray() : null;
        $this->searchClient = $this->sale->client ? $this->sale->client->name : '';
        $this->total = $this->sale->total;
        $this->barber_id = $this->sale->barber_id;
        $this->method = $this->sale->credit > 0 ? 'CREDITO' : 'CONTADO';
        foreach ($this->sale->payments as $payment) {
            $this->payments[] = [
                'method_id' => (int) $payment->payment_method_id,
                'amount'    => (float) $payment->received_amount,
                'reference' => $payment->reference ?? ''
            ];
        }
        foreach ($this->sale->items as $item) {
            $comboItemsFormatted = [];
            if ($item->comboItems && $item->comboItems->count() > 0) {
                foreach ($item->comboItems as $comboItem) {
                    $comboItemsFormatted[] = [
                        'product_id' => $comboItem->product_id,
                        'name' => $comboItem->product_name,
                        'quantity' => $comboItem->unit_quantity
                    ];
                }
            }
            $this->items[] = [
                'product_id' => $item->product_id,
                'name'       => $item->product_name,
                'quantity'   => $item->quantity,
                'type'            => empty($comboItemsFormatted) ? 'product' : 'combo',
                'price' => (float) $item->price,
                'cost' => $item->cost,
                'itbis_rate' => (float) $item->itbis_rate,
                'is_exempt' => $item->is_exempt,
                'discount_rate' => $item->discount_rate,
                'itbis_amount' => $item->itbis_amount,
                'gross_subtotal' => $item->gross_subtotal,
                'discount_amount' => $item->discount_amount,
                'net_subtotal' => $item->total,
                'total' => $item->total,
                'combo_components' => $comboItemsFormatted,
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
        return view('livewire.admin.sales.edit', compact('barbers'));
    }

    protected function validateStockAvailability()
    {
        foreach ($this->items as $item) {
            $itemType = $item['type'] ?? 'product';

            if ($itemType === 'product') {
                $product = Product::find($item['product_id']);

                if ($product && $product->is_inventoriable) {
                    $previousQuantity = 0;
                    foreach ($this->sale->items as $origItem) {
                        if ($origItem->product_id == $item['product_id']) {
                            $previousQuantity += $origItem->quantity;
                        }
                    }

                    $availableStock = $product->stock + $previousQuantity;

                    if ($availableStock < $item['quantity']) {
                        $this->alertError("Stock insuficiente para: {$product->name} (Disponible real: {$availableStock})");
                        return false;
                    }
                }
            } elseif ($itemType === 'combo') {
                if (!empty($item['combo_components'])) {
                    foreach ($item['combo_components'] as $component) {
                        $compProduct = Product::find($component['product_id']);

                        if ($compProduct && $compProduct->is_inventoriable) {
                            $requiredStock = $component['quantity'] * $item['quantity'];

                            $previousRequiredStock = 0;
                            if ($this->sale) {
                                foreach ($this->sale->items as $origItem) {
                                    foreach ($origItem->comboItems as $origComboComp) {
                                        if ($origComboComp->product_id == $component['product_id']) {
                                            $previousRequiredStock += $origComboComp->total_quantity;
                                        }
                                    }
                                }
                            }

                            $availableStock = $compProduct->stock + $previousRequiredStock;

                            if ($availableStock < $requiredStock) {
                                $this->alertError("Stock insuficiente en combo '{$item['name']}' para el componente: {$compProduct->name} (Disponible real: {$availableStock})");
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

    public function resetPOS()
    {
        $this->reset([
            'selectedClient',
            'searchClient',
            'clientResults',
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

    public function updatedSearchClient()
    {
        if (strlen($this->searchClient) < 2) {
            $this->clientResults = [];
            return;
        }

        $this->clientResults = User::where('name', 'like', '%' . $this->searchClient . '%')
            ->orWhere('email', 'like', '%' . $this->searchClient . '%')
            ->take(5)
            ->get()
            ->toArray();
    }

    public function selectClient($client)
    {
        $this->selectedClient = $client;
        $this->searchClient = $client['name'];
        $this->clientResults = [];
    }

    public function removeClient()
    {
        $this->selectedClient = null;
        $this->searchClient = '';
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

    public function update()
    {

        try {
            $this->validateStockAvailability();
        } catch (\Exception $e) {
            $this->alertError('error', $e->getMessage());
            return;
        }

        $this->validate([
            'selectedClient' => ['nullable', 'array'],
            'barber_id'      => ['nullable', 'exists:barbers,id'],
            'items'          => ['required', 'array', 'min:1'],
        ], [
            'items.required'          => 'El carrito de compras no puede estar vacío.'
        ]);

        if ($this->method == 'CONTADO' && empty($this->payments)) {
            LivewireAlert::title('Debe agregar al menos un método de pago')->warning()->asToast()->show();
            return;
        }

        if ($this->method == 'CONTADO' && ($this->paid < $this->total)) {
            LivewireAlert::title('El monto pagado es insuficiente')->warning()->asToast()->show();
            return;
        }

        if ($this->method == 'CREDITO' && empty($this->selectedClient['id'])) {
            LivewireAlert::title('El cliente es requerido para ventas a crédito')->warning()->asToast()->show();
            return;
        }

        try {
            DB::transaction(function () {

                foreach ($this->sale->items as $oldItem) {
                    Product::where('id', $oldItem->product_id)
                        ->where('type', 'product')
                        ->increment('stock', $oldItem->quantity);
                }

                foreach ($this->sale->payments as $oldPayment) {
                    $oldIsEfectivo = PaymentMethod::find($oldPayment->payment_method_id)?->is_efectivo ?? false;

                    if ($oldIsEfectivo && $oldPayment->amount > 0) {
                        CashRegister::find($this->cash_register_id)
                            ->decrement('current_amount', $oldPayment->amount);
                    }
                }

                $this->sale->items()->delete();
                $this->sale->payments()->delete();

                $gross_total = 0;
                $tax_total = 0;
                $net_total = 0;
                $discount_total = 0;

                $credit = ($this->method == 'CONTADO') ? 0 : $this->total;
                $change = ($this->method == 'CONTADO') ? ($this->paid - $this->total) : 0;
                $remainingChange = $change;

                $client_id = empty($this->selectedClient['id']) ? null : $this->selectedClient['id'];
                $barber_id = empty($this->barber_id) ? null : $this->barber_id;

                $this->sale->update([
                    'client_id'      => $client_id,
                    'barber_id'      => $barber_id,
                    'cash_register_id' => $this->cash_register_id,
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

                    $saleItem = $this->sale->items()->create([
                        'product_id'      => $item['product_id'],
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

                    $itemType = $item['type'] ?? 'product';

                    if ($itemType === 'product') {
                        Product::where('id', $item['product_id'])->decrement('stock', $item['quantity']);
                    } elseif ($itemType === 'combo') {
                        if (!empty($item['combo_components'])) {
                            foreach ($item['combo_components'] as $component) {
                                $componentQuantity = $component['quantity'] * $item['quantity'];

                                $compProduct = Product::find($component['product_id']);

                                $saleItem->comboItems()->create([
                                    'product_id' => $component['product_id'],
                                    'product_name' => $compProduct ? $compProduct->name : ($component['name'] ?? ''),
                                    'unit_quantity' => $component['quantity'],
                                    'total_quantity' => $componentQuantity
                                ]);

                                Product::where('id', $component['product_id'])->decrement('stock', $componentQuantity);
                            }
                        }
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

                    $this->sale->payments()->create([
                        'payment_method_id' => $methodId,
                        'amount' => $realAmount,
                        'received_amount' => $receivedAmount,
                        'returned_amount' => $returnedAmount,
                        'reference' => $payment['reference'] ?? null,
                    ]);

                    $isEfectivo = $method?->is_efectivo ?? false;

                    if ($isEfectivo && $realAmount > 0) {
                        CashRegister::find($this->cash_register_id)
                            ->increment('current_amount', $realAmount);
                    }
                }

                $this->sale->update([
                    'gross_total' => $gross_total,
                    'tax_total' => $tax_total,
                    'net_total' => $net_total,
                    'discount_total' => $discount_total
                ]);
            });

            LivewireAlert::title('La venta ha sido actualizada correctamente.')->success()->asToast()->show();
            return redirect()->to(route('admin.sales.index'));

            return redirect()->to(route('admin.sales.index'));
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Error Crítico',
                'text'  => 'Error al consolidar la venta: ' . $e->getMessage(),
                'icon'  => 'error',
            ]);
        }
    }
}
