<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\Map\MapService;
use Illuminate\Http\Request;

class MapController extends Controller
{
    protected $mapService;

    public function __construct(MapService $mapService)
    {
        $this->mapService = $mapService;
    }

    /**
     * Handle the saving of all map data (boundaries, lines, generation settings)
     */
    public function save(Request $request, string $id)
    {
        $project = auth()->user()->projects()->findOrFail($id);
        
        $request->validate([
            'boundaries' => 'nullable|array',
            'lines' => 'nullable|array',
            'adcp_markers' => 'nullable|array',
            'generation_settings' => 'nullable|array',
            'is_generated' => 'nullable|boolean',
        ]);

        $stats = $this->mapService->processMapData($project, $request->all());

        return response()->json([
            'success' => true,
            'message' => 'Map data saved successfully.',
            'statistics' => $stats
        ]);
    }
}
