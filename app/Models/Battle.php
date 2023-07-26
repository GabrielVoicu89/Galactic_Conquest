<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Battle extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $fillable = [
        'attacker_id',
        'defender_id',
        'win',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
