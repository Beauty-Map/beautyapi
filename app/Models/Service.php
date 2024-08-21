<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'title',
        'is_active',
        'parent_id'
    ];

    public function parent()
    {
        return $this->belongsTo(Service::class);
    }

    public function children()
    {
        return $this->hasMany(Service::class, 'parent_id', 'id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
