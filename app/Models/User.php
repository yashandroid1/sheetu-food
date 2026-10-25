<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'name',
        'mobile',
        'email',
        'password',
        'pkg',
        'room',
        'block',
        'status',
    ];

    protected $hidden = ['password'];
        // ✅ Disable automatic timestamps
    public $timestamps = false;
}
