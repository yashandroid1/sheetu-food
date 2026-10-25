<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paymentsys extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'paymentsys';

    // Disable Laravel's default timestamps (created_at, updated_at)
    public $timestamps = false;

    // Fields that can be mass-assigned
    protected $fillable = [
        'token',
        'mobile',
        'price',
        'paid',
        'pkg',
        'date_time',
    ];

    // Optionally, you can cast date_time as a datetime object
    protected $casts = [
        'date_time' => 'datetime',
    ];
}
