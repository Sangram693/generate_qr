<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Dealer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    /**
     * Display a listing of all projects with dealer and related beams, poles, and high masts.
     */
    public function index()
    {
        $projects = Project::with(['dealer', 'beams', 'poles', 'highMasts'])->get();

        return response()->json([
            'message' => 'Projects fetched successfully',
            'projects' => $projects
        ], 200);
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'project_name' => 'required|string|max:100',
            'authority' => 'required|string|max:100',
            'opening_date' => 'required|date',
            'location' => 'required|string',
            'tender_no' => 'required|string|max:50|unique:projects',
            'description' => 'required|string',
            'dealer_id' => 'nullable|exists:dealers,id' 
        ]);

        
        $project = Project::create($validatedData);

        return response()->json([
            'message' => 'Project created successfully',
            'project' => $project
        ], 201);
    }

    /**
     * Display the specified project with dealer and related beams, poles, and high masts.
     */
    public function show($id)
    {
        $project = Project::with(['dealer', 'beams', 'poles', 'highMasts'])->find($id);

        if (!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }

        return response()->json([
            'message' => 'Project details fetched successfully',
            'project' => $project
        ], 200);
    }

    /**
     * Update the specified project in storage.
     */
    public function update(Request $request, $id)
    {
        $project = Project::find($id);

        if (!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }

        $validatedData = $request->validate([
            'project_name' => 'sometimes|string|max:100',
            'authority' => 'sometimes|string|max:100',
            'opening_date' => 'sometimes|date',
            'location' => 'sometimes|string',
            'tender_no' => 'sometimes|string|max:50|unique:projects,tender_no,' . $id,
            'description' => 'sometimes|string',
            'dealer_id' => 'nullable|exists:dealers,id'
        ]);


        $project->update($validatedData);

        return response()->json([
            'message' => 'Project updated successfully',
            'project' => $project
        ], 200);
    }

    public function assignComponents(Request $request, $project_id)
{
    $validatedData = $request->validate([
        'beams' => 'nullable|array',
        'beams.*' => 'exists:beams,id',

        'poles' => 'nullable|array',
        'poles.*' => 'exists:poles,id',

        'highMasts' => 'nullable|array',
        'highMasts.*' => 'exists:high_masts,id'
    ]);

    // Find project
    $project = Project::find($project_id);

    if (!$project) {
        return response()->json(['message' => 'Project not found'], 404);
    }

    return response()->json([
        'data' => $validatedData,
        'project' => $project
    ], 200);

    // Assign beams, poles, and high masts to the project
    if (!empty($validatedData['beams'])) {
        foreach ($validatedData['beams'] as $beamId) {
            $beam = Beam::find($beamId);
            if ($beam->project_id !== null) {
                return response()->json(['message' => "Beam ID {$beamId} is already assigned to another project"], 400);
            }
            $beam->project_id = $project->id;
            $beam->save();
        }
    }

    if (!empty($validatedData['poles'])) {
        foreach ($validatedData['poles'] as $poleId) {
            $pole = Pole::find($poleId);
            if ($pole->project_id !== null) {
                return response()->json(['message' => "Pole ID {$poleId} is already assigned to another project"], 400);
            }
            $pole->project_id = $project->id;
            $pole->save();
        }
    }

    if (!empty($validatedData['highMasts'])) {
        foreach ($validatedData['highMasts'] as $highMastId) {
            $highMast = HighMast::find($highMastId);
            if ($highMast->project_id !== null) {
                return response()->json(['message' => "High Mast ID {$highMastId} is already assigned to another project"], 400);
            }
            $highMast->project_id = $project->id;
            $highMast->save();
        }
    }

    

    return response()->json([
        'message' => 'Components assigned to project successfully',
        'project' => $project->load('beams', 'poles', 'highMasts')
    ], 200);
}

    /**
     * Remove the specified project from storage.
     */
    public function destroy($id)
    {
        $project = Project::find($id);

        if (!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }

        $project->delete();

        return response()->json([
            'message' => 'Project deleted successfully'
        ], 200);
    }
}
