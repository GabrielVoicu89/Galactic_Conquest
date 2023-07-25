<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ship extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $fillable = [
        'user_id',
        'ship_yard_id',
        'type',
        'construction_cost',
        'energy_consumption',
        'fuel_consumption',
        'finished_at',
        'claimed',
        'attack',
        'defense'

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shipyard()
    {
        return $this->belongsTo(User::class);
    }
}
