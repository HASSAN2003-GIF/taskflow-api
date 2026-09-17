<?php

namespace App\Http\Controllers;

use App\Models\TaskList;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request, $list_id)
    {
        // 1. Validate payload
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // 2. Fetch the list (and eager load the board to get the workspace_id)
        $list = TaskList::with('board')->findOrFail($list_id);
        $workspaceId = $list->board->workspace_id;

        // 3. SECURITY CHECK: Does user belong to this workspace?
        $user = $request->user();
        $isMember = $user->workspaces()->where('workspace_id', $workspaceId)->exists();

        if (! $isMember) {
            return response()->json([
                'message' => 'Forbidden. You do not have access to this list.'
            ], 403);
        }

        // 4. Create the Task (Passing the workspace_id for our denormalized architecture)
        $task = $list->tasks()->create([
            'workspace_id' => $workspaceId,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'position' => 0,
        ]);

        return response()->json([
            'message' => 'Task created successfully',
            'task' => $task
        ], 201);
    }

    public function move(Request $request, $task_id)
    {
        $validated = $request->validate([
            'task_list_id' => 'required|exists:task_lists,id',
            'position' => 'required|integer',
        ]);

        // 1. Fetch the task
        $task = \App\Models\Task::findOrFail($task_id);

        // 2. SECURITY CHECK: Ensure the user belongs to the task's workspace
        $user = $request->user();
        $isMember = $user->workspaces()->where('workspace_id', $task->workspace_id)->exists();

        if (! $isMember) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // 3. Update the task's location and order
        $task->update([
            'task_list_id' => $validated['task_list_id'],
            'position' => $validated['position'],
        ]);

        return response()->json([
            'message' => 'Task moved successfully',
            'task' => $task
        ], 200);
    }

    public function destroy(Request $request, $id)
    {
        $task = \App\Models\Task::findOrFail($id);
        
        // Security: Verify the user belongs to the workspace that owns this task
        $hasAccess = $request->user()->workspaces()->where('workspace_id', $task->workspace_id)->exists();
        
        if (!$hasAccess) {
            return response()->json(['message' => 'Unauthorized access to workspace.'], 403);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted successfully'], 200);
    }
}