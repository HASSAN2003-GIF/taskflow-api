<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    // Create a new Workspace
    public function store(Request $request)
    {
        // 1. Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // 2. Identify the currently authenticated user from the Sanctum token
        $user = $request->user();

        // 3. Create the workspace in the database
        $workspace = Workspace::create([
            'name' => $validated['name'],
        ]);

        // 4. Attach the user to the workspace via the pivot table
        // We use the Eloquent relationship we defined earlier, and pass the extra 'role' column data
        $workspace->users()->attach($user->id, ['role' => 'owner']);

        // 5. Return the created workspace to the frontend
        return response()->json([
            'message' => 'Workspace created successfully',
            'workspace' => $workspace,
            'role' => 'owner'
        ], 201);
    }

    public function index(Request $request)
    {
        // Fetch all workspaces this specific user belongs to, including their boards
        $workspaces = $request->user()->workspaces()->with('boards')->get();
        
        return response()->json([
            'workspaces' => $workspaces
        ], 200);
    }
}