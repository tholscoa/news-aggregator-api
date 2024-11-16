<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

/**
 *     Controller for user registration, login, logout, initiate passowrd reset and passowrd reset"
 */
class AuthController extends Controller
{
    public function register(RegistrationRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return ResponseHelper::success($user, "Successful registration", 200); // Ensure this returns a response

        } catch (Exception $e) {
            Log::error("Registration failed: " . $e->getMessage());
            return ResponseHelper::error("An error occurred during registration", 500); // Ensure this returns a response
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                return ResponseHelper::error('Incorrect email or password', 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;
            return ResponseHelper::success(['token' => $token], 'Login successful');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseHelper::error('Error occured', 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return ResponseHelper::success([], 'Logged out successfully');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseHelper::error('An error occured', 500);
        }
    }


    public function InitiateResetPassword(Request $request)
    {
        try {
            $request->validate(['email' => 'required|email']);

            // Send the reset link using the Password facade
            $send = Password::sendResetLink(
                $request->only('email')
            );

            // Check the status and return the appropriate response
            if ($send === 'passwords.sent') {
                return ResponseHelper::success(['email' => $request->email], "password reset link sent to your email", 200);
            } else if ($send === 'passwords.throttled') {
                Log::error($send);
                return ResponseHelper::error("Too many password reset attempt", 500);
            } else {
                Log::error($send);
                return ResponseHelper::error("error occured", 500);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseHelper::error('An error occured', 500);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:8|confirmed',
            ]);

            // Reset password using the Password facade
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->password = Hash::make($password);
                    $user->setRememberToken(Str::random(60));
                    $user->save();

                    event(new PasswordReset($user));
                }
            );

            // Check the status and return the appropriate response
            if ($status === Password::PASSWORD_RESET) {
                return ResponseHelper::success([], "password reset successfully", 200);
            }
            return ResponseHelper::error([], "error occured", 422);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseHelper::error('An error occured', 500);
        }
    }
}
