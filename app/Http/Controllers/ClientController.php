<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::with('projects')->orderBy('name')->get();
        return view('clients.index', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
        ]);

        Client::create($validated);
        return redirect()->route('clients.index')->with('success', 'Client added successfully.');
    }

    public function destroy(Client $client)
    {
        if ($client->projects()->count() > 0) {
            return redirect()->route('clients.index')->with('error', 'Cannot delete client with active projects.');
        }
        
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Client deleted successfully.');
    }
}
