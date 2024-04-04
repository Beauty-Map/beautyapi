<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    use HasFactory;

    protected $fillable = [
        'metaable_id',
        'metaable_type',
        'key',
        'value',
    ];

    public function metaabble()
    {
        return $this->morphTo();
    }
}
