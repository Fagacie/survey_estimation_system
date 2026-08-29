<?php

namespace App\Http\Controllers;

use App\Models\CostRate;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function costs()
    {
        $rates = CostRate::orderBy('id')->get();
        return view('settings.costs', compact('rates'));
    }

    public function storeCost(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'name' => 'required|string|max:255',
            'unit_type' => 'required|in:Per Day,Lump Sum',
            'base_multiplier' => 'nullable|in:Total Duration,Execution Days,MOB/DEMOB Days,Weather Days',
            'default_rate' => 'required|numeric|min:0',
        ]);

        CostRate::create($request->all());

        return redirect()->route('settings.costs')->with('success', 'Cost rate added successfully.');
    }

    public function updateCost(Request $request, CostRate $costRate)
    {
        $request->validate([
            'category' => 'required|string',
            'name' => 'required|string|max:255',
            'unit_type' => 'required|in:Per Day,Lump Sum',
            'base_multiplier' => 'nullable|in:Total Duration,Execution Days,MOB/DEMOB Days,Weather Days',
            'default_rate' => 'required|numeric|min:0',
        ]);

        $costRate->update($request->all());

        return redirect()->route('settings.costs')->with('success', 'Cost rate updated successfully.');
    }

    public function destroyCost(CostRate $costRate)
    {
        $costRate->delete();
        return redirect()->route('settings.costs')->with('success', 'Cost rate deleted successfully.');
    }
}
