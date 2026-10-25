<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class AdminUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'admin_users';

    protected $fillable = [
        'username',
        'password',
        'name',
        'payment_h',
        'payment_l',
    ];

    protected $hidden = [
        'password',
    ];

    public $timestamps = false;

    protected $casts = [
        'payment_h' => 'boolean',
        'payment_l' => 'boolean',
        'created_at' => 'datetime',
    ];
}
