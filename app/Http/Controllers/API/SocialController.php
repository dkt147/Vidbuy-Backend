<?php

namespace App\Http\Controllers\API;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Mail\VerificationCodeMail;
use App\Models\Category;
use App\Models\Country;
use App\Models\Influencer;
use App\Models\InfluencerCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\UserCode;
use App\Models\UserDetail;
use App\Srv\SocialService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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
            'username' => 'required|string|max:255',
            'email' => 'required|email|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            'password' => 'required|string|min:8',
            'role_id' => 'required|integer|in:2,3', // 2 = UserAccount, 3 = InfluencerAccount
            'image' => 'required|string',
            'country_id' => 'nullable|integer|exists:countries,id',
        ]);

        if ($validation->fails()) {
            return self::failure($validation->errors()->first());
        }

        $existingUser = User::where('email', $data['email'])->first();

        if ($existingUser) {
            return self::failure('A user with this email already exists.');
        }

        $imageUrl = null;
        if(isset($data['image'])){
            $imageUrl = Helper::getBase64ImageUrl($data['image']);
        }
        


        $countryId = null;
        $countryName = null;

        if ($data['role_id'] == 3 && isset($data['country_id'])) {

            $country = Country::find($data['country_id']);
            if ($country) {
                $countryId = $data['country_id'];
                $countryName = $country->name;
            } else {
                return self::failure('Invalid country_id provided.');
            }
        }

        $user = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'country_id' => $countryId,
            'country_name' => $countryName,
            'password' => Hash::make($data['password']),
            'role_id' => $data['role_id'],
            'status' => 'Pending',
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

        return self::success('User registered successfully. A verification code has been sent to your email.', [
            'user' => $user,
            'code' => $verificationCode
        ]);
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

        return self::success('Code verified successfully. User is now active.', ['user' => $user, 'bool' => true]);
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
        if ($user->status === 'Pending') {
            return self::failure('Review your Account code sent on your gmail ');
        }

        $token = $user->createToken('AuthToken')->accessToken;

        return self::success('Login successful',[ 'user' => $user, 'token' => $token ]);
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

    // public function logout(Request $request)
    // {
    //     // Get the authenticated user
    //     $user = Auth::user();

    //     if ($user) {
    //         // Revoke the user's current token
    //         $user->currentAccessToken()->delete();

    //         return response()->json([
    //             'success' => 'Logout successful.',
    //         ]);
    //     }

    //     return response()->json([
    //         'error' => 'User not authenticated.',
    //     ], 401);
    // }


    public function deleteUserAccount()
    {
        $user = Auth::user();


        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $userId = $user->id;

        User::where('id', $userId)->delete();


        return response()->json(['message' => 'User account deleted successfully']);
    }





    public function Useredit(Request $request)
    {
        $user = Auth::user();

        $editUserData = User::find($user->id);

        if (!$editUserData) {
            return self::failure('User not found.');
        }

        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'nullable|string',
            'username' => 'required|string|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'image' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return self::failure($validator->errors()->first());
        }

        $validated = $validator->validated();

        if (isset($validated['image'])) {
            $imageUrl = Helper::getBase64ImageUrl($validated['image']);
            if ($imageUrl) {
                $validated['image'] = $imageUrl;
            } else {
                return self::failure('Invalid image format.');
            }
        }


        $editUserData->update($validated);

        return self::success('User updated successfully.', ['user' => $editUserData]);
    }



    public function Influenceredit(Request $request)
    {
        $user = Auth::user();

        // Fetch user by ID
        $editUserData = User::find($user->id);

        if (!$editUserData) {
            return self::failure('User not found.');
        }

        $data = $request->all();

        // Validate request data
        $validator = Validator::make($data, [
            'name' => 'nullable|string',
            'username' => 'required|string|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'image' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'country_id' => 'nullable|exists:countries,id',
        ]);

        if ($validator->fails()) {
            return self::failure($validator->errors()->first());
        }

        $validated = $validator->validated();

        // Process image if provided
        if (isset($validated['image'])) {
            $imageUrl = Helper::getBase64ImageUrl($validated['image']);
            if ($imageUrl) {
                $validated['image'] = $imageUrl;
            } else {
                return self::failure('Invalid image format.');
            }
        }

        // Update category if provided
        if (isset($validated['category_id'])) {
            $category = Category::find($validated['category_id']);
            if ($category) {
                InfluencerCategory::updateOrCreate(
                    ['user_id' => $user->id],
                    ['category_name' => $category->name]
                );
            }
        }

        // Update country if provided
        if (isset($validated['country_id'])) {
            $country = Country::find($validated['country_id']);
            if ($country) {
                $editUserData->country_name = $country->name;
            }
        }

        // Update user data
        $editUserData->update($validated);

        return self::success('User updated successfully.', ['user' => $editUserData]);
    }



    public function getUser(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $email = $request->input('email');



        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json(['user' => $user]);
    }

}
