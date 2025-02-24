<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];
    public function ticket(){
        return $this->hasMany(Ticket::class, );
    }

    static function findByName(string $name)  {
        return self::whereName($name)->first();
    }
}
