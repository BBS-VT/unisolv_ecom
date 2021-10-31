<?php


namespace App\Http\Controllers\Api\V1\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    protected function sendResetLinkResponse(Request $request, $response)
    {
        $response = ['message' => "Password reset email sent"];
        return response($response, 200);
    }

    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        $response = "Email cound not be sent to this email address";
        return response($response, 500);
    }
}
