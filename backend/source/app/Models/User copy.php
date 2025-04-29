<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;

class User extends Authenticatable
{
    // Remove both HasApiTokens and JWTSubject implementations
    // Since you're using Lcobucci directly
    
    protected $fillable = [
        'name', 'email', 'password'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Generates a JWT token for the user
     */
    public function generateToken(Request $request): string
    {
        $config = \Lcobucci\JWT\Configuration::forSymmetricSigner(
            new \Lcobucci\JWT\Signer\Hmac\Sha256(),
            \Lcobucci\JWT\Signer\Key\InMemory::plainText(config('app.key'))
        );

        $now = new \DateTimeImmutable();
        
        return $config->builder()
            ->issuedBy(config('app.url'))
            ->permittedFor(config('app.client_url'))
            ->identifiedBy(uniqid())
            ->issuedAt($now)
            ->expiresAt($now->modify('+1 hour'))
            ->withClaim('uid', $this->id)
            ->withClaim('ip', $request->ip())
            ->withClaim('ua', $request->userAgent())
            ->getToken($config->signer(), $config->signingKey())
            ->toString();
    }
}