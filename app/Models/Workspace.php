<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    protected $fillable = ['name'];

    public function users()
    {
        // Explicitly define the pivot table name 'workspace_user'
        return $this->belongsToMany(User::class, 'workspace_user')
                    ->withPivot('role')
                    ->withTimestamps();
    }
    public function boards()
    {
        return $this->hasMany(Board::class);
    }
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}