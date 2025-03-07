<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketType extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
    ];

    public function ticket(){
        return $this->hasMany(Ticket::class, );
    }

    static function findByName(string $title)  {
        return self::whereName($title)->first();
    }
}
