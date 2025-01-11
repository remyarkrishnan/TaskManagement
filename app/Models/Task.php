<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['title', 'description','user_id', 'status'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}