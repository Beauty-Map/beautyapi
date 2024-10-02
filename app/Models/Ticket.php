<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    const CREATED_STATUS = 'created';
    const CLOSED_STATUS = 'closed';
    const ANSWERED_STATUS = 'answered';

    protected $fillable = [
        'title',
        'status',
        'description',
        'user_id',
        'subject_id',
        'file',
        'parent_id',
        'is_published',
    ];

    protected $appends = [
        'status_fa',
        'children_desc',
    ];

    public function getStatusFaAttribute() {
        return match ($this->status) {
            self::CREATED_STATUS => 'درانتظار بررسی',
            self::ANSWERED_STATUS => 'پاسخ داده شده',
            self::CLOSED_STATUS => 'بسته شده',
            default => 'رد شده',
        };
    }

    public function getChildrenDescAttribute() {
        return $this->children()->orderByDesc('created_at', 'desc')->get();
    }

    public function children()
    {
        return $this->hasMany(Ticket::class, 'parent_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Ticket::class, 'parent_id', 'id');
    }

    public function subject()
    {
        return $this->belongsTo(TicketSubject::class, 'subject_id', 'id');
    }
}
