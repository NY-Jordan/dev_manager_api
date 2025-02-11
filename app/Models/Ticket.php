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
}
