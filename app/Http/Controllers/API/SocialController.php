<?php

namespace App\Http\Controllers\API;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Mail\VerificationCodeMail;
use App\Models\Influencer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\UserCode;
use App\Models\UserDetail;
use App\services\SocialService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class SocialController extends Controller
{

    protected $service;
    public function __construct(SocialService $service)
    {
        $this->service = $service;
    }


    public function signupViaEmail(Request $request)
    {
        $data = $request->all();

        $validation = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            'password' => 'required|string|min:8',
            'confirm_password' => 'required|string|same:password',
            'login_type' => 'required|string',
            'role_id' => 'required|integer|in:2,3', // 2 = UserAccount, 3 = InfluencerAccount
            'image' => 'required|string',
        ]);

        if ($validation->fails()) {
            return self::failure($validation->errors()->first());
        }

        $existingUser = User::where('email', $data['email'])->first();

        if ($existingUser) {
            return self::failure('A user with this email already exists.');
        }

        $imageUrl = Helper::getBase64ImageUrl($data['image']);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'login_type' => $data['login_type'],
            'role_id' => $data['role_id'],
            'status' => 'In Review',
            'image' => $imageUrl,
        ]);

        if ($data['role_id'] == 2) {
            UserDetail::insert([
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } elseif ($data['role_id'] == 3) {
            Influencer::insert([
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $verificationCode = rand(1000, 9999);

        UserCode::insert([
            'user_id' => $user->id,
            'code' => $verificationCode,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Mail::to($user->email)->send(new VerificationCodeMail($verificationCode));

        return self::success('User registered successfully. A verification code has been sent to your email.', ['user' => $user]);
    }



    public function verifyCode(Request $request)
    {
        $data = $request->all();

        $validation = Validator::make($data, [
            'user_id' => 'required|integer|exists:users,id',
            'code' => 'required|integer',
        ]);

        if ($validation->fails()) {
            return self::failure($validation->errors()->first());
        }


        $userCode = UserCode::where('user_id', $data['user_id'])
            ->where('code', $data['code'])
            ->first();

        if (!$userCode) {
            return self::failure('Invalid verification code.');
        }


        $user = User::find($data['user_id']);
        $user->status = 'Active';
        $user->save();

        // Optionally, delete the code after verification
        UserCode::where('user_id', $data['user_id'])->delete();

        return self::success('Code verified successfully. User is now active.', ['user' => $user]);
    }

    public function loginViaEmail(Request $request)
    {
        $data = $request->all();

        // Validate the required fields
        $validation = Validator::make($data, [
            'email' => 'required|email',
            'password' => 'required|string',
            'role_id' => 'required',
        ]);

        // If validation fails
        if ($validation->fails()) {
            return self::failure($validation->errors()->first());
        }

        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            return self::failure('Invalid Email');
        }

        if ($user->role_id != $data['role_id']) {
            return self::failure('Invalid credentials');
        }

        if (!Hash::check($data['password'], $user->password)) {
            return self::failure('Invalid credentials');
        }

        // Check if the user's status is 'In Review'
        if ($user->status === 'In Review') {
            return self::failure('Review your Account code sent on your gmail ');
        }

        $token = $user->createToken('AuthToken')->accessToken;

        return self::success('Login successful', ['user' => $user, 'token' => $token]);
    }




    public function forgetPassword(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = User::where('email', $data['email'])->first();


        $code = random_int(1000, 9999);

        UserCode::updateOrCreate([
            'user_id' => $user->id
        ], [
            'code' => $code,
            'expire_at' => Carbon::now()->addMinutes(5)
        ]);



        // Mail::to($user->email)->send(new ForgetPasswordMail($code));



        return self::success("Code Generated successfully", [
            'result' => [
                'code' => $code,
                'user_id' => $user->id,

            ]
        ]);
    }




    public function validateOtpAndChangePassword(Request $request)
    {
        $data = $request->all();


        $validation = Validator::make($data, [
            'user_id' => 'required|exists:users,id',
            'code' => 'required',
            'password' => 'required|min:6',
            'confirm_password' => 'required|same:password',
        ]);


        if ($validation->fails()) {
            return self::failure($validation->errors()->first());
        }

        $res = $this->service->validateOtpAndChangePassword($data);

        return self::success("Password changed successfully", $res);
    }




    // public function loginViaSocial(Request $request)
    // {
    //     $data = $request->all();

    //     // Validate the required fields
    //     $validation = Validator::make($data, [
    //         'name' => 'required|string',
    //         'email' => 'required|email',
    //         'login_type' => 'required|string',
    //         'role_id' => 'required|integer|in:2,3',
    //     ]);

    //     // If validation fails
    //     if ($validation->fails()) {
    //         return self::failure($validation->errors()->first());
    //     }

    //     // Retrieve the user by email
    //     $user = User::where('email', $data['email'])->first();

    //     if (!$user) {
    //         // Create a new user
    //         $user = new User();
    //         $user->name = $data['name'];
    //         $user->email = $data['email'];
    //         $user->password = bcrypt(env('APP_KEY', '1338922534'));
    //         $user->login_type = $data['login_type'];
    //         $user->role_id = $data['role_id'];
    //         $user->status = "In Review";
    //         $user->save();
    //     } else {
    //         $user->login_type = $data['login_type'];
    //         $user->save();
    //     }

    //     // Generate token
    //     $tokenResult = $user->createToken('AuthToken');
    //     $token = $tokenResult->accessToken;

    //     return self::success('Login successful', ['user' => $user, 'token' => $token]);
    // }




}
