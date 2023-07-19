<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Infrastructure extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $fillable = [
        'user_id',
        'type',
        'level',
        'production_hour',
        'construction_cost',
        'finished_at',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
