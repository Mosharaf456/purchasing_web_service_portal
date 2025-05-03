<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Traits\JWTAuthTrait;

class User extends Authenticatable
{
    use HasUuids;
    use JWTAuthTrait;

    protected $fillable = [
        'name', 'email', 'password'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    // public function getToken() {
    //     return $this->token;
    // }

}