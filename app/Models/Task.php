<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['task_list_id', 'workspace_id', 'title', 'description', 'position'];

    public function list()
    {
        return $this->belongsTo(TaskList::class, 'task_list_id');
    }
    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }
}