<?php

namespace App\Http\Controllers;

use App\Models\Drone;
use App\Models\Camera;
use Illuminate\Http\Request;

class DroneEquipmentController extends Controller
{
    public function index()
    {
        $drones = Drone::orderBy('name')->get();
        $cameras = Camera::orderBy('name')->get();
        
        return view('dashboard.drone_equipment.index', compact('drones', 'cameras'));
    }

    public function storeDrone(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'usable_flight_time_min' => 'required|integer|min:1',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        Drone::create($validated);
        
        return back()->with('success', 'Drone added successfully.');
    }

    public function updateDrone(Request $request, Drone $drone)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'usable_flight_time_min' => 'required|integer|min:1',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        $drone->update($validated);
        
        return back()->with('success', 'Drone updated successfully.');
    }

    public function toggleDrone(Drone $drone)
    {
        $drone->update(['is_active' => !$drone->is_active]);
        return back()->with('success', 'Drone status toggled.');
    }

    public function storeCamera(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'sensor_width_mm' => 'required|numeric|gt:0',
            'sensor_height_mm' => 'required|numeric|gt:0',
            'focal_length_mm' => 'required|numeric|gt:0',
            'image_width_px' => 'required|integer|gt:0',
            'image_height_px' => 'required|integer|gt:0',
            'min_photo_interval_sec' => 'nullable|numeric|gt:0',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        Camera::create($validated);
        
        return back()->with('success', 'Camera added successfully.');
    }

    public function updateCamera(Request $request, Camera $camera)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'sensor_width_mm' => 'required|numeric|gt:0',
            'sensor_height_mm' => 'required|numeric|gt:0',
            'focal_length_mm' => 'required|numeric|gt:0',
            'image_width_px' => 'required|integer|gt:0',
            'image_height_px' => 'required|integer|gt:0',
            'min_photo_interval_sec' => 'nullable|numeric|gt:0',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        $camera->update($validated);
        
        return back()->with('success', 'Camera updated successfully.');
    }

    public function toggleCamera(Camera $camera)
    {
        $camera->update(['is_active' => !$camera->is_active]);
        return back()->with('success', 'Camera status toggled.');
    }
}
