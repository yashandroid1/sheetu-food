<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FdDistribute extends Model
{
    // 1️⃣ Specify the table name
    protected $table = 'fd_distributes';

    // 2️⃣ Primary key (optional if 'id')
    protected $primaryKey = 'id';

    // 3️⃣ Allow mass assignment for these fields
    protected $fillable = [
        'token',
        'mobile',
        'brkf',
        'luh',
        'dinr',
        'date',
        'time',
        'amount',
        'name',
    ];

    // 4️⃣ Disable timestamps if not using created_at and updated_at
    public $timestamps = false;

    // 5️⃣ Cast meal fields as boolean and amount as decimal
    protected $casts = [
        'brkf' => 'boolean',
        'luh' => 'boolean',
        'dinr' => 'boolean',
        'amount' => 'decimal:2',
        'date' => 'date',
        'time' => 'string',
    ];
}
