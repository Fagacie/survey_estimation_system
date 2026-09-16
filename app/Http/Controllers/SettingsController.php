<?php

namespace App\Http\Controllers;

use App\Models\CostRate;
use App\Models\CompanySetting;
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
            'base_multiplier' => 'nullable|string',
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
            'base_multiplier' => 'nullable|string',
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

    /**
     * Show the company settings page.
     */
    public function company()
    {
        $settings = CompanySetting::getMany([
            'company_name', 'company_reg_no', 'company_address',
            'company_phone', 'company_email', 'company_logo',
            'payment_bank_name', 'payment_account_name',
            'payment_account_number', 'payment_swift_code',
            'default_prepared_by_name', 'default_prepared_by_title',
            'default_approved_by_name', 'default_approved_by_title',
            'prepared_by_signature', 'approved_by_signature',
        ]);

        return view('settings.company', compact('settings'));
    }

    /**
     * Update company settings.
     */
    public function updateCompany(Request $request)
    {
        $request->validate([
            'company_name'    => 'nullable|string|max:255',
            'company_reg_no'  => 'nullable|string|max:100',
            'company_address' => 'nullable|string',
            'company_phone'   => 'nullable|string|max:50',
            'company_email'   => 'nullable|string|email|max:100',
            'payment_bank_name'      => 'nullable|string|max:255',
            'payment_account_name'   => 'nullable|string|max:255',
            'payment_account_number' => 'nullable|string|max:50',
            'payment_swift_code'     => 'nullable|string|max:20',
            'default_prepared_by_name'  => 'nullable|string|max:100',
            'default_prepared_by_title' => 'nullable|string|max:100',
            'default_approved_by_name'  => 'nullable|string|max:100',
            'default_approved_by_title' => 'nullable|string|max:100',
            'company_logo'             => 'nullable|image|mimes:png,jpg,jpeg|max:1024',
            'prepared_by_signature'    => 'nullable|image|mimes:png,jpg,jpeg|max:512',
            'approved_by_signature'    => 'nullable|image|mimes:png,jpg,jpeg|max:512',
        ]);

        // Save text fields
        $textFields = [
            'company_name', 'company_reg_no', 'company_address',
            'company_phone', 'company_email',
            'payment_bank_name', 'payment_account_name',
            'payment_account_number', 'payment_swift_code',
            'default_prepared_by_name', 'default_prepared_by_title',
            'default_approved_by_name', 'default_approved_by_title',
        ];

        foreach ($textFields as $field) {
            if ($request->has($field)) {
                CompanySetting::set($field, $request->input($field));
            }
        }

        // Handle file uploads
        if ($request->hasFile('company_logo')) {
            $path = $request->file('company_logo')->store('company', 'public');
            CompanySetting::set('company_logo', $path);
        }

        if ($request->hasFile('prepared_by_signature')) {
            $path = $request->file('prepared_by_signature')->store('signatures', 'public');
            CompanySetting::set('prepared_by_signature', $path);
        }

        if ($request->hasFile('approved_by_signature')) {
            $path = $request->file('approved_by_signature')->store('signatures', 'public');
            CompanySetting::set('approved_by_signature', $path);
        }

        return redirect()->route('settings.company')->with('success', 'Company settings updated successfully.');
    }
}

