<?php

namespace App\Http\Controllers;

use App\Models\Board;
use Illuminate\Http\Request;

class TaskListController extends Controller
{
    public function store(Request $request, $board_id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'integer' // Allows the frontend to specify column order
        ]);

        // 1. Fetch the board (will automatically throw a 404 error if Board ID doesn't exist)
        $board = Board::findOrFail($board_id);

        // 2. SECURITY CHECK: Does the user belong to the workspace that owns this board?
        $user = $request->user();
        $isMember = $user->workspaces()->where('workspace_id', $board->workspace_id)->exists();

        if (! $isMember) {
            return response()->json([
                'message' => 'Forbidden. You do not have access to this board.'
            ], 403);
        }

        // 3. Create the Task List using Eloquent relationships
        $list = $board->lists()->create([
            'name' => $validated['name'],
            'position' => $validated['position'] ?? 0,
        ]);

        return response()->json([
            'message' => 'Task List created successfully',
            'list' => $list
        ], 201);
    }
}