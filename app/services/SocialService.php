<?php

namespace App\services;

use App\Models\User;
use App\Models\UserCode;
use App\Helpers\ServiceResponse;
use App\Http\Resources\API\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgetPassword;
use App\Mail\OtpVerification;
use Carbon\Carbon;
use App\Models\SpaceAdmin;
use App\Models\PhoneNumber;

class SocialService
{



    public function __construct() {}


    public function userForget($data)
    {
        $user = User::where(['email' => $data['email']])->first();
        if (!$user) {
            return ServiceResponse::error('User does not exist with this email');
        }


        $code = random_int(1000, 9999);
        $user->update(['reset_code' => $code, 'reset_code_expire_at' => Carbon::now()->addMinutes(5)]);

        return ServiceResponse::success('Email sent successfully', ['code' => $code]);
    }






    public function validateOtpAndChangePassword($data)
    {
        $user = User::find($data['user_id']);
        if (!$user) {
            return ServiceResponse::error('User Does Not Exist');
        }

        $coder = UserCode::where(['user_id' => $user->id, 'code' => $data['code']])->first();
        if (!$coder) {
            return ServiceResponse::error('Invalid Code for User');
        }

        if (Carbon::parse($coder->expire_at)->isPast()) {
            return ServiceResponse::error('Code Expired, please generate a new code');
        }


        $user->update([
            'password' => bcrypt($data['password']),
        ]);


        $coder->delete();


        $updatedUser = User::find($user->id);

        return ServiceResponse::success('Password updated successfully', $updatedUser);
    }
}
