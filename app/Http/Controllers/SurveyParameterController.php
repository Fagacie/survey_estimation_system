<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Http\Requests\StoreSurveyParameterRequest;
use App\Services\Calculation\SurveyParameterService;

class SurveyParameterController extends Controller
{
    protected $parameterService;

    public function __construct(SurveyParameterService $parameterService)
    {
        $this->parameterService = $parameterService;
    }

    public function store(StoreSurveyParameterRequest $request, string $id)
    {
        $project = auth()->user()->projects()->findOrFail($id);
        
        $this->parameterService->saveParameters($project, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Survey parameters saved successfully.'
        ]);
    }
}
