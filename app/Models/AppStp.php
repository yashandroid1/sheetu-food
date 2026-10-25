<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppStp extends Model
{
    use HasFactory;

    protected $table = 'app_stp';

    protected $fillable = [
        'version',
        'a_url',
        'support',
        'payment',
        'a_update',
        'payment_high',
        'update_high',
    ];

    protected $casts = [
        'payment' => 'boolean',
        'a_update' => 'boolean',
        'payment_high' => 'boolean',
        'update_high' => 'boolean',
    ];
}
