<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = [
        'user_id',
        'ore',
        'fuel',
        'energy'
    ];
}
