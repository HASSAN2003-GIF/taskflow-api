<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    protected $fillable = ['workspace_id', 'name', 'status', 'end_date'];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }
    public function lists()
    {
        return $this->hasMany(TaskList::class);
    }
}