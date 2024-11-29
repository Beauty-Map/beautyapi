<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    use HasFactory;

    const STRING_ARRAY = ['tel_number', 'alt_number', 'avatar', 'national_code', 'artist_banner', 'address', 'bio', 'licenses'];

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
            $value = json_decode($this->value, true);
            if ($this->key == 'ton_wallet_address') {
                $value = $this->value;
            }
            if ($this->key == 'work_hours') {
                $value = $value ?? [];
                $value = collect($value)->sortBy('day_index')->values()->all();
            }
            if ($this->key == 'social_media') {
                $value = $value ?? [
                    'telegram' => '',
                    'instagram' => '',
                    'bale' => '',
                    'whatsapp' => '',
                    'eita' => '',
                    'rubika' => '',
                    'web' => '',
                ];
            }
            if ($this->key == 'documents') {
                $value = $value ?? [];
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
            if ($this->key == 'ton_wallet_address') {
                $value = $value ?? '';
            }
            if (in_array($this->key, self::STRING_ARRAY)) {
                $value = $this->value;
            }
            return $value;
        } catch (\Exception $exception) {
            return $this->value;
        }
    }
}
