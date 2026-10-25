<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeekFood extends Model
{
    use HasFactory;

    // Table name (if your table name is week_foods)
    protected $table = 'wk_foods';

    // Primary key
    protected $primaryKey = 'id';

    // Columns allowed for mass assignment
    protected $fillable = [
        'day',
        'brkf',
        'luh',
        'dinr',
    ];

    // Disable timestamps (if table doesn't have created_at & updated_at)
    public $timestamps = false;
}
