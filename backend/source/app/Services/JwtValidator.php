<?php

namespace App\Services;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Validation\Constraint;

class JwtValidator
{
    protected $config;

    public function __construct()
    {
        $this->config = Configuration::forSymmetricSigner(
            new Sha256(),
            \Lcobucci\JWT\Signer\Key\InMemory::plainText(config('jwt.secret'))
        );
        
        $this->config->setValidationConstraints(
            new Constraint\LooseValidAt(
                new \Lcobucci\Clock\SystemClock(),
                new \DateInterval('PT30S')
            ),
            new Constraint\IssuedBy(config('app.url')),
            new Constraint\PermittedFor(config('app.client_url'))
        );
    }

    public function validate(string $token): bool
    {
        try {
            $parsed = $this->config->parser()->parse($token);
            
            return $this->config->validator()->validate(
                $parsed,
                ...$this->config->validationConstraints()
            );
            
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getClaims(string $token): array
    {
        try {
            return $this->config->parser()->parse($token)->claims()->all();
        } catch (\Exception $e) {
            return [];
        }
    }
}