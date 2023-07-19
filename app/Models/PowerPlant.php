<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PowerPlant extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $fillable = [
        'user_id',
        'level',
        'construction_cost',
        'finished_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
