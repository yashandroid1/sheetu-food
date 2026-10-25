<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FdPrice extends Model
{
     use HasFactory;

    protected $table = 'fd_price';

    protected $fillable = [
        'brkf',
        'luh',
        'dinr',
    ];

    public $timestamps = true;
    const UPDATED_AT = null;
}