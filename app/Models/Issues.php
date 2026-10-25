<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issues extends Model
{
     use HasFactory;

    // Actual table name
    protected $table = 'issue_requests';

    // Mass assignable columns
    protected $fillable = [
        'token',
        'mobile',
        'type',
        'description',
        'status',
    ];

    // Timestamps enabled (created_at will be auto handled)
    public $timestamps = false;
}
