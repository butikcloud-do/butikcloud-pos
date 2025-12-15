<?php

namespace App\Http\Controllers\Api;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthorizationController extends Controller
{
    protected function checkCodeValidity($user, $addMin = 2)
    {
        if (!$user->ver_code_send_at) {
            return false;
        }
        if ($user->ver_code_send_at->addMinutes($addMin) < Carbon::now()) {
            return false;
        }
        return true;
    }

    public function authorization()
    {
        $user = auth()->user();
        if (!$user->status) {
            $type = 'ban';
        } elseif (!$user->ev) {
            $type           = 'email';
            $notifyTemplate = 'EVER_CODE';
        } elseif (!$user->sv) {
            $type           = 'sms';
            $notifyTemplate = 'SVER_CODE';
        } elseif (!$user->tv) {
            $type = '2fa';
        } else {
            $notify[] = 'You are already verified';
            return jsonResponse("already_verified", "error", $notify);
        }

        if (!$this->checkCodeValidity($user) && ($type != '2fa') && ($type != 'ban')) {
            $user->ver_code         = verificationCode(6);
            $user->ver_code_send_at = Carbon::now();
            $user->save();
            notify($user, $notifyTemplate, [
                'code' => $user->ver_code
            ], [$type]);
        }

        $notify[] = 'Verify your account';
        return jsonResponse("code_sent", "success", $notify);
    }


    public function sendVerifyCode($type)
    {
        $user = auth()->user();

        if ($this->checkCodeValidity($user)) {
            $targetTime = $user->ver_code_send_at->addMinutes(2)->timestamp;
            $delay      = $targetTime - time();

            $notify[] = 'Please try after ' . $delay . ' seconds';
            return jsonResponse("try_after", "error", $notify);
        }

        $user->ver_code         = verificationCode(6);
        $user->ver_code_send_at = Carbon::now();
        $user->save();

        if ($type == 'email') {
            $type           = 'email';
            $notifyTemplate = 'EVER_CODE';
        } else {
            $type           = 'sms';
            $notifyTemplate = 'SVER_CODE';
        }

        notify($user, $notifyTemplate, [
            'code' => $user->ver_code
        ], [$type]);

        $notify[] = 'Verification code sent successfully';
        return jsonResponse("code_sent", "success", $notify);
    }

    public function emailVerification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
        ]);

        if ($validator->fails()) {
            return jsonResponse("validation_error", "error", $validator->errors()->all());
        }
        $user = auth()->user();

        if ($user->ver_code == $request->code) {
            $user->ev               = Status::VERIFIED;
            $user->ver_code         = null;
            $user->ver_code_send_at = null;
            $user->save();
            $notify[] = 'Email verified successfully';

            return jsonResponse("email_verified", "success", $notify);
        }

        $notify[] = 'Verification code doesn\'t match';
        return jsonResponse("code_not_match", "error", $notify);
    }

    public function mobileVerification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
        ]);

        if ($validator->fails()) {
            return jsonResponse("validation_error", "error", $validator->errors()->all());
        }

        $user = auth()->user();
        if ($user->ver_code == $request->code) {
            $user->sv               = Status::VERIFIED;
            $user->ver_code         = null;
            $user->ver_code_send_at = null;
            $user->save();
            $notify[] = 'Mobile verified successfully';

            return jsonResponse("mobile_verified", "success", $notify, [
                'user' => $user
            ]);
        }
        $notify[] = 'Verification code doesn\'t match';
        return jsonResponse("code_not_match", "error", $notify);
    }

    public function g2faVerification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
        ]);

        if ($validator->fails()) {
            return jsonResponse("validation_error", "error", $validator->errors()->all());
        }
        $user     = auth()->user();
        $response = verifyG2fa($user, $request->code);

        if ($response) {
            $notify[] = 'Verification successful';
            return jsonResponse("twofa_verified", "success", $notify, [
                'user' => $user
            ]);
        } else {
            $notify[] = 'Wrong verification code';
            return jsonResponse("wrong_code", "error", $notify);
        }
    }
}
