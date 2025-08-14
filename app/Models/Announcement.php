<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Announcement extends Model
{
    protected $fillable = ['title', 'description', 'created_by'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::id();
            }
        });
    }
    
    public function users()
    {
        return $this->belongsToMany(User::class, 'announcement_users')
                    ->withPivot('id')
                    ->withTimestamps();
    }
   
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
