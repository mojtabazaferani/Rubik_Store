<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Message extends Model
{
    use HasFactory;

    use Notifiable;

    protected $fillable = [
        'name',
        'mobile_number',
        'email',
        'subject',
        'message'
    ];
}
