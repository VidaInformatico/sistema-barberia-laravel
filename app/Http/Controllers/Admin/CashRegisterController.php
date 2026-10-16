<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class CashRegisterController extends Controller
{
    public function index()
    {
        return view('admin.boxes.index');
    }
    
    public function movements($id, Request $request)
    {
        $realId = Crypt::decrypt($id);
        $caja = CashRegister::with(['opener', 'sales.payments.method', 'expenses.paymentMethod'])->findOrFail($realId);

        $gastos = $caja->expenses;

        $movimientos = collect()
            ->merge($caja->sales->map(fn($s) => [
                'date' => $s->created_at,
                'day' => $s->created_at->format('Y-m-d'),
                'type' => 'ingreso',
                'concept' => 'Venta realizada',
                'methods' => $s->payments->map(fn($p) => $p->method->name)->implode(', '),
                'amount' => $s->total
            ]))
            ->merge($gastos->map(fn($g) => [
                'date' => $g->expense_date,
                'day' => $g->expense_date->format('Y-m-d'),
                'type' => 'egreso',
                'concept' => $g->concept . ($g->description ? ' - ' . $g->description : ''),
                'methods' => $g->paymentMethod->name ?? 'N/A',
                'amount' => $g->total_amount
            ]))
            ->sortByDesc('date')
            ->groupBy('day');

        $pagosPorMetodo = $caja->sales->flatMap->payments
            ->groupBy(fn($p) => $p->method->name)
            ->map(fn($g) => $g->sum('amount'));

        foreach ($gastos as $gasto) {
            $metodoNombre = $gasto->paymentMethod->name ?? 'N/A';
            $pagosPorMetodo[$metodoNombre] = ($pagosPorMetodo[$metodoNombre] ?? 0) - $gasto->total_amount;
        }

        if ($request->action == 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.boxes.pdf_cash_movements', compact('caja', 'pagosPorMetodo', 'gastos', 'movimientos'))
                ->setPaper('a4', 'portrait');
            return $pdf->stream("Movimientos_Caja_{$caja->id}.pdf");
        }

        return view('admin.boxes.movements', compact('caja', 'pagosPorMetodo', 'gastos', 'movimientos', 'id'));
    }

    public function close($id)
    {
        try {
            $caja = CashRegister::findOrFail($id);

            $caja->update([
                'status' => 'closed',
                'closed_at' => now(),
                'closing_amount' => $caja->current_amount
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Caja cerrada con éxito'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
