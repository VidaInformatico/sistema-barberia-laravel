<?php

namespace App\Livewire\Admin\Products;

use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;

class Index extends Component
{
    use WithPagination, WithFileUploads;

    public $product_id = null;
    public $category_id;
    public $brand_id;
    public $name;
    public $description;
    public $sku;
    public $price;
    public $cost_price;
    public $stock = 0;
    public $stock_min = 0;
    public $tax;
    public $image;
    public $existing_image;
    public $is_active = true;
    public $is_inventoriable = 1;

    public $search = '';
    public $perPage = 10;
    public $isOpen = false;
    public $type = 'product';
    public $selectedComboItems = [];

    public $categories = [];
    public $brands = [];

    public function mount()
    {
        $this->categories = ProductCategory::where('type', 'product')->where('is_active', true)->orderBy('name', 'asc')->get();
        $this->brands = Brand::where('is_active', true)->orderBy('name', 'asc')->get();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $products = Product::whereIn('type', ['product', 'combo'])->with('category')
            ->where('branch_id', Auth::user()->branch_id)
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('sku', 'like', '%' . $this->search . '%')
                    ->orWhereHas('category', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->orderBy('name', 'asc')
            ->paginate($this->perPage);

        return view('livewire.admin.products.index', compact('products'));
    }

    #[On('productSelected')]
    public function setProduct($productId)
    {
        if ($this->type === 'combo') {
            $exists = false;
            foreach ($this->selectedComboItems as $key => $item) {
                if ($item['id'] == $productId) {
                    $this->selectedComboItems[$key]['quantity'] += 1;
                    $exists = true;
                    break;
                }
            }

            if (!$exists) {
                $this->selectedComboItems[] = ['id' => $productId, 'quantity' => 1];
            }
        } else {
            $this->product_id = $productId;
        }
    }

    public function removeComboItem($index)
    {
        unset($this->selectedComboItems[$index]);
        $this->selectedComboItems = array_values($this->selectedComboItems); // Reindexar el array
    }

    public function getComboItemsWithDetailsProperty()
    {
        $ids = collect($this->selectedComboItems)->pluck('id');

        $products = \App\Models\Product::whereIn('id', $ids)->get()->keyBy('id');

        return collect($this->selectedComboItems)->map(function ($item) use ($products) {
            return [
                'id' => $item['id'],
                'quantity' => $item['quantity'],
                'name' => $products[$item['id']]->name ?? 'N/A',
                'sku' => $products[$item['id']]->sku ?? 'N/A',
            ];
        });
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->reset([
            'product_id',
            'category_id',
            'brand_id',
            'name',
            'description',
            'sku',
            'price',
            'cost_price',
            'stock',
            'stock_min',
            'is_inventoriable',
            'image',
            'existing_image',
            'is_active'
        ]);
        $this->stock = 0;
        $this->stock_min = 0;
        $this->is_active = true;
        $this->resetValidation();
    }

    public function store()
    {
        $branchId = auth()->user()->branch_id;

        $rules = [
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id' => ['required', 'exists:brands,id'],
            'name'        => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'name')
                    ->ignore($this->product_id)
                    ->where('branch_id', $branchId)
            ],
            'type'        => 'required|in:product,combo',
            'is_inventoriable' => 'boolean',
            'description' => ['nullable', 'string', 'max:500'],
            'sku'         => [
                'required',
                'string',
                'max:100',
                Rule::unique('products', 'sku')
                    ->ignore($this->product_id)
                    ->where('branch_id', $branchId)
            ],
            'price'       => ['required', 'numeric', 'min:0'],
            'cost_price'  => ['required', 'numeric', 'min:0'],
            'tax'         => ['required', 'in:18,16,0,ex'],
            'stock'       => ['required', 'integer', 'min:0'],
            'stock_min'   => ['required', 'integer', 'min:0'],
            'is_active'   => ['boolean'],
            'image'       => ['nullable', 'image', 'max:2048'],
        ];

        $this->validate($rules);

        $isExempt = $this->tax === 'ex';
        $taxValue = $isExempt ? 0 : $this->tax;

        $data = [
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'type'        => $this->type,
            'name'        => $this->name,
            'slug'        => \Illuminate\Support\Str::slug($this->name),
            'is_inventoriable' => $this->is_inventoriable,
            'description' => $this->description,
            'sku'         => $this->sku,
            'price'       => $this->price,
            'cost_price'  => $this->cost_price,
            'stock'       => $this->stock,
            'stock_min'   => $this->stock_min,
            'tax'         => $taxValue,
            'is_exempt'   => $isExempt,
            'is_active'   => $this->is_active,
        ];

        if ($this->image) {
            if ($this->product_id) {
                $oldProduct = Product::find($this->product_id);
                if ($oldProduct && $oldProduct->image) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($oldProduct->image);
                }
            }

            $filename = 'products/' . \Illuminate\Support\Str::uuid() . '.webp';

            $manager = \Intervention\Image\ImageManager::gd();
            $imageConverted = $manager->read($this->image->getRealPath())->toWebp(80);

            \Illuminate\Support\Facades\Storage::disk('public')->put($filename, (string) $imageConverted);

            $data['image'] = $filename;
        }

        $product = Product::updateOrCreate(['id' => $this->product_id], $data);

        if ($this->type === 'combo') {
            $syncData = [];
            foreach ($this->selectedComboItems as $item) {
                $syncData[$item['id']] = ['quantity' => $item['quantity']];
            }
            $product->comboItems()->sync($syncData);
        } else {
            $product->comboItems()->detach();
        }

        $this->dispatch('swal', [
            'title' => $this->product_id ? '¡Actualizado!' : '¡Creado!',
            'text'  => 'El producto ha sido guardado correctamente',
            'icon'  => 'success',
        ]);

        $this->closeModal();
    }

    public function edit($id)
    {
        $product = Product::with('comboItems')->findOrFail($id);

        $this->product_id           = $product->id;
        $this->category_id          = $product->category_id;
        $this->brand_id          = $product->brand_id;
        $this->type                 = $product->type;
        $this->name                 = $product->name;
        $this->is_inventoriable                 = $product->is_inventoriable;
        $this->description          = $product->description;
        $this->sku                  = $product->sku;
        $this->price                = $product->price;
        $this->cost_price           = $product->cost_price;
        $this->stock                = $product->stock;
        $this->tax                  = $product->is_exempt == 'ex' ? 'ex' : $product->tax;
        $this->stock_min            = $product->stock_min;
        $this->existing_image       = $product->image_url;
        $this->is_active            = $product->is_active;
        $this->image                = null;

        $this->selectedComboItems = $product->comboItems->map(function ($item) {
            return [
                'id' => $item->id,
                'quantity' => $item->pivot->quantity,
            ];
        })->toArray();

        $this->openModal();
    }

    public function deleteConfirm($id)
    {
        $this->dispatch('confirm-delete', id: $id);
    }

    #[On('delete-confirmed')]
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        $this->dispatch('swal', [
            'title' => 'Eliminado',
            'text'  => 'Producto eliminado correctamente',
            'icon'  => 'success',
        ]);
    }
}
