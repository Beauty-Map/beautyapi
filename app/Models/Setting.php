<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'own',
        'first',
        'second',
        'third',
        'forth',
    ];

    public function toPercentages()
    {
        return [$this->first, $this->second, $this->third, $this->forth];
    }
}
