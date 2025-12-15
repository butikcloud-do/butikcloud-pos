<?php

namespace App\Http\Controllers\Api\Auth;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    public function login(Request $request)
    {
        $usernameFiled = $this->findUsername();

        $validator     = Validator::make($request->all(), [
            $usernameFiled => 'required|string',
            'password'     => 'required|string',
        ]);

        if ($validator->fails()) {
            return jsonResponse("validation_error", "error", $validator->errors()->all());
        }

        $credentials = request([$usernameFiled, 'password']);

        if (!Auth::attempt($credentials)) {
            $response[] = 'Unauthorized user';
            return jsonResponse("invalid_credential", "error", $response);
        }

        $user = auth()->user();

        if ($user->status != Status::USER_ACTIVE  || !$user->ev  || !$user->sv) {
            $response[] = 'After complete the account verification from web, then you will login to the ap.';
            return jsonResponse("unverified", "error", $response);
        }

        $tokenResult = $user->createToken('auth_token', ['admin'])->plainTextToken;
        $response[]  = 'Login Successful';

        return jsonResponse("login_success", "success", $response, [
            'user'         => $user,
            'access_token' => $tokenResult,
            'token_type'   => 'Bearer'
        ]);
    }

    public function findUsername()
    {
        $login     = request()->input('username');
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$fieldType => $login]);
        return $fieldType;
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        $notify[] = 'Logout Successful';
        return jsonResponse("logout", "success", $notify);
    }


    public function checkToken(Request $request)
    {
        $validationRule = [
            'token' => 'required',
        ];

        $validator = Validator::make($request->all(), $validationRule);
        if ($validator->fails()) {
            return jsonResponse("validation_error", "error", $validator->errors()->all());
        }
        $accessToken = PersonalAccessToken::findToken($request->token);

        if ($accessToken) {
            $notify[]      = 'Token exists';
            $data['token'] = $request->token;
            return jsonResponse("token_exists", "success", $notify, $data);
        }

        $notify[] = 'Token doesn\'t exists';

        return jsonResponse("token_not_exists", "error", $notify);
    }
}
