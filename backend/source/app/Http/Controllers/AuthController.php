<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;  // rest api


use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Token\RegisteredClaims;
use Lcobucci\Clock\SystemClock;
use DateTimeImmutable;

use App\Helpers\JwtHelper;
use App\Models\User; 

use App\Helpers\LogHelper;
use App\Traits\JWTAuthTrait;
use Illuminate\Container\Attributes\Log;

class AuthController extends Controller
{
    use JWTAuthTrait;
    
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
            LogHelper::logToFile('log', 'Login api called........ ', 'error');

            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required|min:3',
            ]);

            LogHelper::logToFile('log', 'Login api called $validated: ' . print_r($validated, 1), 'error');

            $user = User::where('email', $validated['email'])->first();
            
      
            // LogHelper::logToFile('log', 'Login api called $user: ' . print_r($user, 1), 'error');

           

            if (!$user) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }
           
            // Accessing user values
            $userId = $user->id;
            $userEmail = $user->email;
            $userPasswordHash = $user->password; // hashed password

            LogHelper::logToFile('log', 'Login api called $userId: ' . print_r($userId, 1), 'error');
            LogHelper::logToFile('log', 'Login api called $userEmail: ' . print_r($userEmail, 1), 'error');
            LogHelper::logToFile('log', 'Login api called $userPasswordHash: ' . print_r($userPasswordHash, 1), 'error');
            // Check if the password is correct
            if (!Hash::check($validated['password'], $user->password)) {
                return response()->json(['error' => 'The provided credentials are incorrect.'], 422);
            }


            
            
            
            // $now = new DateTimeImmutable();
            // $config = JwtHelper::getJwtConfiguration();
            // $token = $config->builder()
            //     ->issuedBy('purchasing-server') // Replace with your app name
            //     ->permittedFor('purchasing-server-web') // Replace with your app name
            //     ->identifiedBy(env('JWT_SECRET')) // Replace with your app name
            //     ->issuedAt($now)
            //     ->canOnlyBeUsedAfter($now)
            //     ->expiresAt($now->modify('+1 hour'))
            //     ->relatedTo((string)$userId)
            //     ->getToken($config->signer(), $config->signingKey());

            $token =  $user->createToken('authToken', '+1 hour');

            return response()->json([
                // 'access_token' => $token->toString(),
                'access_token' =>  $token,
                'expires_in' => 3600
            ]);
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

    // private function verifyMFA(User $user, string $code): bool
    // {
    //     if (empty($user->mfa_secret)) return true;
        
    //     $google2fa = new \PragmaRX\Google2FA\Google2FA();
    //     return $google2fa->verifyKey($user->mfa_secret, $code);
    // }
}