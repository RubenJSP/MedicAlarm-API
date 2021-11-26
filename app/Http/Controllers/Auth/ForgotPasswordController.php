<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;
   
    protected function sendResetLinkResponse($response)
    {
        if (request()->header('Content-Type') == 'application/json') {
            return response()->json(['message' => 'Se ha enviado un enlace de recuperación a su correo,
            si no logra ver el mensaje, revise su bandeja de spam.']);
        }
        return back()->with('status', 'Se ha enviado un enlace de recuperación a su correo,
        si no logra ver el mensaje, revise su bandeja de spam.');
    }
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        if (request()->header('Content-Type') == 'application/json') {
            return response()->json(['message' => 'No se ha podido enviar el correo de recuperación, intente nuevamente.']);
        }
        return back()->withErrors(
            ['email' => 'No se ha podido enviar el correo de recuperación, intente nuevamente.']
        );
    }
}
