<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\JwtValidator;

class ValidateToken
{
    public function handle($request, Closure $next)
    {
        $token = $this->getTokenFromRequest($request);
        
        if (!$token) {
            return response()->json(['error' => 'invalid_token'], 401);
        }

        // JWT Validation
        if ($this->isJwt($token)) {
            $validator = new JwtValidator();
            
            if (!$validator->validate($token)) {
                return response()->json(['error' => 'invalid_token'], 401);
            }

            $claims = $validator->getClaims($token);
            
            // Custom claim checks
            if (!$this->validateClaims($claims)) {
                return response()->json(['error' => 'invalid_claims'], 403);
            }
        }
        // Opaque Token Validation
        else {
            $response = Http::asForm()
                ->withHeaders(['Authorization' => 'Bearer '.config('oauth.server_secret')])
                ->post(config('oauth.introspection_url'), [
                    'token' => $token
                ]);

            if (!$response->json()['active']) {
                return response()->json(['error' => 'invalid_token'], 401);
            }
        }

        // Check token revocation
        if ($this->isRevoked($token)) {
            return response()->json(['error' => 'revoked_token'], 401);
        }

        return $next($request);
    }

    private function getTokenFromRequest($request)
    {
        if ($request->bearerToken()) {
            return $request->bearerToken();
        }

        return $request->input('access_token');
    }

    private function isJwt($token): bool
    {
        return count(explode('.', $token)) === 3;
    }

    private function validateClaims(array $claims): bool
    {
        $required = ['iss', 'exp', 'nbf', 'sub', 'aud'];
        foreach ($required as $claim) {
            if (!isset($claims[$claim])) return false;
        }

        return $claims['iss'] === config('app.url') &&
               $claims['aud'] === config('app.client_id');
    }

    private function isRevoked($token): bool
    {
        return \Cache::has('revoked_tokens:'.$token);
    }
}