<?php
use Laravel\Passport\HasApiTokens;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\Constraint;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens;

    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [
            'ip' => request()->ip(),
            'ua' => request()->userAgent()
        ];
    }
}