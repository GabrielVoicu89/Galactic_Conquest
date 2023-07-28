<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'username',
        'birth_date',
        'victories',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    static public function getEmail($email)
    {
        return User::where('email', '=', $email)->first();
    }

    static public function getToken($remember_token)
    {
        return User::where('remember_token', '=', $remember_token)->first();
    }



    public function planet()
    {
        return $this->hasOne(Planet::class);
    }

    public function resource()
    {
        return $this->hasOne(Resource::class);
    }

    public function infrastuctures()
    {
        return $this->hasMany(Infrastructure::class);
    }

    public function powerplants()
    {
        return $this->hasMany(PowerPlant::class);
    }

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }

    public function shipyards()
    {
        return $this->hasMany(ShipYard::class);
    }

    public function ships()
    {
        return $this->hasMany(Ship::class);
    }

    public function battles()
    {
        return $this->hasMany(Battle::class);
    }
}
