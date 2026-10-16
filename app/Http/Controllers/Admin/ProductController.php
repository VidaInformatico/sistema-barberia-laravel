<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ProductsExport;
use App\Exports\StockMovementsExport;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Product;
use App\Models\StockMovement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function index()
    {
        return view('admin.products.index');
    }

    public function categories()
    {
        return view('admin.products.categories');
    }

    public function brands()
    {
        return view('admin.products.brands');
    }

    public function stockMovements()
    {
        return view('admin.products.stock-movements');
    }

    public function export(Request $request, $type)
    {
        $branchId = Auth::user()->branch_id;
        $search = $request->search;

        $products = Product::whereIn('type', ['product', 'combo'])
            ->with('category')
            ->where('branch_id', $branchId)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('sku', 'like', '%' . $search . '%')
                        ->orWhereHas('category', function ($subQuery) use ($search) {
                            $subQuery->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->orderBy('name', 'asc')
            ->get();

        $company = Company::first();

        if ($type === 'pdf') {
            $pdf = Pdf::loadView('admin.products.pdf', compact('products'));
            return $pdf->stream('reporte-productos.pdf');
        }

        if ($type === 'excel') {
            return Excel::download(new ProductsExport($search), 'reporte-productos.xlsx');
        }

        abort(404);
    }

    public function exportMovement(Request $request, $type)
    {
        $branchId = Auth::user()->branch_id;
        $search = $request->search;
        $filterType = $request->filterType;

        $movements = StockMovement::with(['product', 'user', 'reference'])
            ->where('branch_id', $branchId)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('reason', 'like', '%' . $search . '%')
                        ->orWhere('type', 'like', '%' . $search . '%')
                        ->orWhereHas('product', function ($sub) use ($search) {
                            $sub->where('name', 'like', '%' . $search . '%')
                                ->orWhere('sku', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('user', function ($sub) use ($search) {
                            $sub->where('name', 'like', '%' . $search . '%')
                                ->orWhere('last_name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($filterType, function ($query) use ($filterType) {
                $query->where('type', $filterType);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $company = Company::first();

        if ($type === 'pdf') {
            $pdf = Pdf::loadView('admin.products.movements-pdf', compact('movements', 'company'));
            return $pdf->stream('reporte-movimientos-stock.pdf');
        }

        if ($type === 'excel') {
            return Excel::download(new StockMovementsExport($search, $filterType), 'reporte-movimientos-stock.xlsx');
        }

        abort(404);
    }
}
