<?php

namespace App\Http\Controllers\API;

use App\Helpers\ServiceResponse;
use App\Http\Controllers\Controller;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserDetailController extends Controller
{
    //

    public function UserLanguage(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return ServiceResponse::error('User not authenticated');
        }

        $userId = $user->id;

        $validator = Validator::make($request->all(), [
            'language_id' => 'required|exists:languages,id',
        ]);

        if ($validator->fails()) {
            return ServiceResponse::error('Validation failed', $validator->errors());
        }

        $userLanguage = UserDetail::where('user_id', $userId)->first();

        if (!$userLanguage) {
            return ServiceResponse::error('User details not found');
        }

        $userLanguage->language_id = $request->input('language_id');
        $userLanguage->save();

        return ServiceResponse::success('User Language updated successfully', $userLanguage);
    }


    public function UserNotification(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return ServiceResponse::error('User not authenticated');
        }

        $userId = $user->id;

        $validator = Validator::make($request->all(), [
            'push_notification' => 'integer|in:0,1',
            'email_notification' => 'integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return ServiceResponse::error('Validation failed', $validator->errors());
        }

        $userNotification = UserDetail::where('user_id', $userId)->first();

        if (!$userNotification) {
            return ServiceResponse::error('User details not found');
        }

        $userNotification->push_notification = $request->input('push_notification', $userNotification->push_notification);
        $userNotification->email_notification = $request->input('email_notification', $userNotification->email_notification);

        $userNotification->save();

        return ServiceResponse::success('User Notification Settings updated successfully', $userNotification);
    }
}
