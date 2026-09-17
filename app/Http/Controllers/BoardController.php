<?php

namespace App\Http\Controllers;

use App\Models\Board;
use Illuminate\Http\Request;

class BoardController extends Controller
{
    // Create a new Board inside a specific Workspace
    public function store(Request $request, $workspace_id)
    {
        // 1. Validate the payload
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = $request->user();

        // 2. SECURITY CHECK: Does this user belong to this workspace?
        // We query the user's workspaces to see if the ID matches the URL parameter.
        $isMember = $user->workspaces()->where('workspace_id', $workspace_id)->exists();

        if (! $isMember) {
            return response()->json([
                'message' => 'Forbidden. You do not have access to this workspace.'
            ], 403);
        }

        // 3. Create the board
        $board = Board::create([
            'workspace_id' => $workspace_id,
            'name' => $validated['name'],
            ' status' => 'active',
        ]);

        return response()->json([
            'message' => 'Board created successfully',
            'board' => $board
        ], 201);
    }

    // Fetch a single board and all its nested data
    public function show(Request $request, $board_id)
    {
        // 1. Fetch the board AND eagerly load its lists and tasks
        $board = Board::with(['lists.tasks'])->findOrFail($board_id);

        // 2. SECURITY CHECK
        $user = $request->user();
        $isMember = $user->workspaces()->where('workspace_id', $board->workspace_id)->exists();

        if (! $isMember) {
            return response()->json([
                'message' => 'Forbidden. You do not have access to this board.'
            ], 403);
        }

        return response()->json([
            'board' => $board
        ], 200);
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