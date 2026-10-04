<?php

namespace App\Http\Controllers;

use App\Models\Signatory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SignatoryController extends Controller
{
    /**
     * Add a new person (name, position, signature image) from the quotation builder.
     * The quotation itself is saved as JSON, which cannot carry an image file,
     * so the new person is saved here first and then selected in the dropdown.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255|unique:signatories,name',
            'position'  => 'required|string|max:255',
            'signature' => 'required|image|mimes:png,jpg,jpeg|max:2048',
        ], [
            'name.unique' => 'This person is already in the list. Please choose them from the dropdown.',
        ]);

        $file     = $request->file('signature');
        $fileName = 'sig_' . Str::slug($validated['name']) . '_' . time() . '.' . $file->guessExtension();

        File::ensureDirectoryExists(public_path('images/signatures'));
        $file->move(public_path('images/signatures'), $fileName);

        $signatory = Signatory::create([
            'name'           => $validated['name'],
            'position'       => $validated['position'],
            'signature_path' => 'images/signatures/' . $fileName,
            'created_by'     => Auth::id(),
        ]);

        return response()->json([
            'success'   => true,
            'signatory' => [
                'id'       => $signatory->id,
                'name'     => $signatory->name,
                'position' => $signatory->position,
                'url'      => asset($signatory->signature_path),
            ],
        ]);
    }
}