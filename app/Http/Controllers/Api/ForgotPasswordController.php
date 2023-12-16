<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        if ($response == Password::RESET_LINK_SENT) {
            // Generate the reset link using the email and token
            $resetLink = $this->generateResetLink($request->email);

            // Include the reset link in the response
            return response()->json([
                'message' => 'Password reset link sent to your email',
                'reset_link' => $resetLink,
            ], 200);
        }

        throw ValidationException::withMessages([
            'email' => [trans($response)],
        ]);
    }

    protected function generateResetLink($email)
{
    // Retrieve the user instance by email
    $user = User::where('email', $email)->first();

    if (!$user) {
        // Handle the case where the user is not found (perhaps show an error)
        return null;
    }

    // Use the URL class to generate the reset link
    $token = app('auth.password.broker')->createToken($user);
    $resetLink = URL::to('/password/reset/' . $token) . '?email=' . urlencode($email);

    return $resetLink;
}

    protected function broker()
    {
        return Password::broker();
    }

    protected function validateEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
    }


    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $response = $this->broker1()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password),
                ])->save();
            }
        );

        if ($response == Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password reset successfully'], 200);
        }

        throw ValidationException::withMessages([
            'email' => [trans($response)],
        ]);
    }

    protected function broker1()
    {
        return Password::broker();
    }
}
