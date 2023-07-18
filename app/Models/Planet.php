<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Planet extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $fillable = [
        'name',
        'user_id',
        'position_y',
        'position_x'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
