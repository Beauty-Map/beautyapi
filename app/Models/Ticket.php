<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

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
