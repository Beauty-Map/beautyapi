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
                $value = collect($value)->sortBy('day_index')->values()->all();
            }
            if ($this->key == 'work_on_holidays') {
                $value = $value ?? false;
            }
            if ($this->key == 'is_closed') {
                $value = $value ?? false;
            }
            if ($this->key == 'is_all_day_open') {
                $value = $value ?? false;
            }
            return $value;
        } catch (\Exception $exception) {
            return $this->value;
        }
    }
}
