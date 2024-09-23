<?php

namespace App\Http\Controllers\API;

use App\Helpers\ServiceResponse;
use App\Http\Controllers\Controller;
use App\Models\InfluencerVideoType;
use App\Models\RequestVideo;
use App\Models\User;
use App\Models\VideoType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RequestVideoController extends Controller
{
    //

    public function RequestVideo(Request $request)
    {
        $user = Auth::user();
        $data = $request->all();

        // Validate the input data
        $validator = Validator::make($data, [
            'influencer_id' => 'required|exists:users,id',
            'video_type_id' => 'required|exists:video_types,id',
            'video_for' => 'required',
            'from' => 'required|string',
            'to' => 'required|string',
            'description' => 'required|string',
            'required_days' => 'required|string',
            'delivery_charges' => 'required|numeric', // Ensure delivery_charges is numeric
        ]);

        if ($validator->fails()) {
            return ServiceResponse::error('Validation failed', $validator->errors());
        }

        $influencerVideoType = InfluencerVideoType::where('user_id', $data['influencer_id'])
            ->where('video_type_id', $data['video_type_id'])
            ->first();

        if (!$influencerVideoType) {
            return ServiceResponse::error('The specified video type is not available for this influencer.');
        }

        $videoTypePrice = VideoType::where('id', $data['video_type_id'])->pluck('price')->first();

        $price = $influencerVideoType->price;

        $totalPrice = $videoTypePrice + $price + $data['delivery_charges'];

        $review = new RequestVideo();
        $review->user_id = $user->id;
        $review->influencer_id = $data['influencer_id'];
        $review->video_type_id = $data['video_type_id'];
        $review->video_for = $data['video_for'];
        $review->description = $data['description'];
        $review->from = $data['from'];
        $review->to = $data['to'];
        $review->required_days = $data['required_days'];
        $review->delivery_charges = $data['delivery_charges'];
        $review->service_charges = $price;
        $review->total_price = $totalPrice;
        $review->status = 'Pending';
        $review->save();

        return ServiceResponse::success('Video request submitted successfully', $review);
    }


    public function RequestedVideoListForInfluencerPending()
    {
        $influencer = Auth::user();


        $requestedVideos = RequestVideo::with(['user', 'videoType'])
            ->where('influencer_id', $influencer->id)
            ->where('status', 'Pending')
            ->get();

        return ServiceResponse::success('Pending video requests fetched successfully', $requestedVideos);
    }
    public function RequestedVideoListForInfluencerInprogress()
    {
        $influencer = Auth::user();


        $requestedVideos = RequestVideo::with(['user', 'videoType'])
            ->where('influencer_id', $influencer->id)
            ->where('status', 'In Progress')
            ->get();

        return ServiceResponse::success('Pending video requests fetched successfully', $requestedVideos);
    }

    public function RequestedVideoListForInfluencerCompleted()
    {
        $influencer = Auth::user();


        $requestedVideos = RequestVideo::with(['user', 'videoType'])
            ->where('influencer_id', $influencer->id)
            ->where('status', 'Completed')
            ->get();

        return ServiceResponse::success('Completed video requests fetched successfully', $requestedVideos);
    }


    public function RequestedVideoListForInfluencer()
    {
        $influencer = Auth::user();


        $requestedVideos = RequestVideo::with(['user', 'videoType'])
            ->where('influencer_id', $influencer->id)
            ->get();

        return ServiceResponse::success('video requests fetched successfully', $requestedVideos);
    }




    public function RequestedVideoListForUser()
    {
        $user = Auth::user();

        $requestedVideos = RequestVideo::with(['influencer', 'videoType'])
            ->where('user_id', $user->id)
            ->get();

        return ServiceResponse::success('Video request fetched successfully', $requestedVideos);
    }







    public function DeleteRequestedList($id)
    {
        $requestedVideo = RequestVideo::find($id);

        if (!$requestedVideo) {
            return ServiceResponse::error('Requested video not found', 404);
        }

        $requestedVideo->delete();

        return ServiceResponse::success('Requested video deleted successfully', null);
    }
}
