<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Address extends Model
{
    use HasFactory;

    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'state',
        'location',
        'zip_code',
        'receiver',
        'tel'
    ];
}
