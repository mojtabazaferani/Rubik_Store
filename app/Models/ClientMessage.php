<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ClientMessage extends Model
{
    use HasFactory;

    use Notifiable;

    protected $fillable = [
        'name',
        'from',
        'to',
        'subject',
        'message'
    ];
}
