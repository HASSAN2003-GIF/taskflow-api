<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\TaskListController;
use App\Http\Controllers\TaskController; 

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// THE SECURITY GATE
Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/workspaces', [WorkspaceController::class, 'store']);
    Route::post('/workspaces/{workspace_id}/boards', [BoardController::class, 'store']);
    
    // This MUST be inside the group!
    Route::post('/boards/{board_id}/lists', [TaskListController::class, 'store']);

    Route::post('/lists/{list_id}/tasks', [TaskController::class, 'store']);

    // Nested Route: Create a board inside a workspace
    Route::post('/workspaces/{workspace_id}/boards', [BoardController::class, 'store']);
    
    // Fetch a single board (Shallow nested)
    Route::get('/boards/{board_id}', [BoardController::class, 'show']);

    // Move a task
    Route::patch('/tasks/{task_id}/move', [TaskController::class, 'move']);

});