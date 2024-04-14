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

    protected $appends = [
        'formatted_value',
    ];

    public function metaabble()
    {
        return $this->morphTo();
    }

    public function getFormattedValueAttribute()
    {
        try {
            $value = json_decode($this->value);
            if ($this->key == 'work_hours') {
                $value = collect($value)->sortBy('day_index')->toArray();
            }
            return $value;
        } catch (\Exception $exception) {
            return $this->value;
        }
    }
}
