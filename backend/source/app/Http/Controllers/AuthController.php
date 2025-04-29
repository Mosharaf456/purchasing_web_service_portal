<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Fixed import
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use App\Helpers\LogHelper;
use Illuminate\Validation\ValidationException; // Fixed import
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function index(Request $request) 
    {
        return response()->json([
            'status' => false,
            'expires_in' => 500
        ]);
    }
    
    public function login(Request $request)
    {
        try {
            LogHelper::logToFile('log', 'Login api called1: ' . print_r(['a'=> 1], 1), 'error');

            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required|min:12',
                '2fa_code' => 'sometimes|numeric'
            ]);

            $user = User::where('email', $validated['email'])->firstOrFail();
            LogHelper::logToFile('log', 'Login api called2: ' . print_r(['a'=> 1], 1), 'error');

            if (!Hash::check($validated['password'], $user->password)) {
                throw ValidationException::withMessages([
                    'password' => ['The provided credentials are incorrect.'],
                ]);
            }

            // MFA Check
            if ($user->mfa_secret && !$this->verifyMFA($user, $validated['2fa_code'] ?? '')) {
                throw ValidationException::withMessages([
                    '2fa_code' => ['Invalid MFA code.'],
                ]);
            }

            $config = Configuration::forSymmetricSigner(
                new Sha256(),
                \Lcobucci\JWT\Signer\Key\InMemory::plainText(config('jwt.secret'))
            );

            $token = $config->builder()
                ->issuedBy(config('app.url'))
                ->permittedFor(config('app.client_url'))
                ->issuedAt(now()->toDateTimeImmutable())
                ->expiresAt(now()->addHours(1)->toDateTimeImmutable())
                ->withClaim('uid', $user->id)
                ->getToken($config->signer(), $config->signingKey());

            return response()->json([
                'access_token' => $token->toString(),
                'expires_in' => 3600
            ]);

        } catch (ValidationException $e) {
            LogHelper::logToFile('log', 'Validation error: ' . json_encode($e->errors()), 'error');
            return response()->json([
                'error' => 'Validation error',
                'messages' => $e->errors()
            ], 422);

        } catch (ModelNotFoundException $e) {
            LogHelper::logToFile('log', 'User not found: ' . $request->email, 'error');
            return response()->json([
                'error' => 'Invalid credentials',
                'message' => 'Email or password is incorrect'
            ], 401); // Don't reveal if user exists

        } catch (QueryException $e) {
            LogHelper::logToFile('log', 'Database error: ' . $e->getMessage(), 'error');

            return response()->json([
                'error' => 'Service unavailable',
                'message' => 'Please try again later'
            ], 503);

        } catch (\Exception $e) {
            LogHelper::logToFile('log', 'System error: ' . $e->getMessage(), 'error');
            return response()->json([
                'error' => 'System error',
                'message' => 'An unexpected error occurred'
            ], 500);
        }catch (\Throwable $e) {  // Catches everything else (PHP 7+)
            LogHelper::logToFile('log', 'CRITICAL: ' . $e->getMessage(), 'error');
            
            return response()->json([
                'error' => 'Server error',
                'message' => 'Please try again later'
            ], 500);
        }
    }

    private function verifyMFA(User $user, string $code): bool
    {
        if (empty($user->mfa_secret)) return true;
        
        $google2fa = new \PragmaRX\Google2FA\Google2FA();
        return $google2fa->verifyKey($user->mfa_secret, $code);
    }
}