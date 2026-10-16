<?php

namespace App\Http\Controllers;

use App\Models\BookingPolicy;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function policies()
    {
        $policies = BookingPolicy::all();
        return view('landing.policies', compact('policies'));
    }

    public function updateBranch(Request $request)
    {
        $request->validate(['branch_id' => 'required|exists:branches,id']);

        $user = auth()->user();
        $user->branch_id = $request->branch_id;
        $user->save();

        return response()->json(['success' => true, 'message' => 'Sucursal actualizada']);
    }
}
