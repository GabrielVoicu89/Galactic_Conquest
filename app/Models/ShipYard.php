<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipYard extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $fillable = [
        'user_id',
        'construction_cost',
        'construction_state',
        'finished_at',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ships()
    {
        return $this->hasMany(Ship::class);
    }
}
