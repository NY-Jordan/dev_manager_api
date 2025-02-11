<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'task_id',
        'ticket_status_id',
        'title',
        'description',
        'ticket_type_id'
    ];
    use HasFactory;

    public function ticketType(){
        return $this->belongsTo(TicketType::class, 'ticket_type_id');
    }

    public function task(){
        return $this->hasOne(Task::class, 'task_id');
    }

    public function ticketStatus(){
        return $this->belongsTo(TicketStatus::class, 'ticket_status_id');
    }
}
