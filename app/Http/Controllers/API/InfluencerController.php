<?php

namespace App\Http\Controllers\API;

use App\Helpers\ServiceResponse;
use App\Http\Controllers\Controller;
use App\Models\Influencer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InfluencerController extends Controller
{
    //

    public function influencerLanguage(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;
        $validator = Validator::make($request->all(), [
            'language_id' => 'required|exists:languages,id',
        ]);

        if ($validator->fails()) {
            return ServiceResponse::error('Validation failed', $validator->errors());
        }

        $influencerLanguage = Influencer::where('user_id', $userId)->first();

        if (!$influencerLanguage) {
            return ServiceResponse::error('Influencer details not found');
        }

        $influencerLanguage->language_id = $request->input('language_id');

        $influencerLanguage->save();

        return ServiceResponse::success('Influencer Language added successfully', $influencerLanguage);
    }


    public function influencerNotification(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;
        $validator = Validator::make($request->all(), [
            'push_notification' => 'integer|in:0,1',
            'email_notification' => 'integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return ServiceResponse::error('Validation failed', $validator->errors());
        }

        $userNotification = Influencer::where('user_id', $userId)->first();

        if (!$userNotification) {
            return ServiceResponse::error('Influencer details not found');
        }

        $userNotification->push_notification = $request->input('push_notification');
        $userNotification->email_notification = $request->input('email_notification');

        $userNotification->save();

        return ServiceResponse::success('Influencer Notification Setting added successfully', $userNotification);
    }
}
