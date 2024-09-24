<?php

namespace App\Http\Controllers\API;

use App\Helpers\ServiceResponse;
use App\Http\Controllers\Controller;
use App\Models\InfluencerRequestVideo;
use App\Models\RequestVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class InfluencerRequestVideoController extends Controller
{
    //

    public function RequestedVideo(Request $request)
    {
        $user = Auth::user();
        $data = $request->all();

        $validator = Validator::make($data, [
            'request_video_id' => 'required|exists:request_videos,id',
            'link' => 'required|file|mimes:mp4,avi,mov|max:10240', // Adjust file size limit if needed
        ]);

        if ($validator->fails()) {
            return ServiceResponse::error('Validation failed', $validator->errors());
        }

        if ($request->hasFile('link')) {
            $video = $request->file('link');
            $filename = time() . '-' . Str::slug(pathinfo($video->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $video->getClientOriginalExtension();


            $path = $video->move(public_path('videos'), $filename);
            $video_url = asset('videos/' . $filename);
        }

        $slug = $data['slug'] ?? Str::slug(pathinfo($filename, PATHINFO_FILENAME));

        $review = new InfluencerRequestVideo();
        $review->user_id = $user->id;
        $review->request_video_id = $data['request_video_id'];
        $review->link = $video_url;
        $review->slug = $slug;
        $review->status = 'Completed';
        $review->save();

        $requestVideo = RequestVideo::find($data['request_video_id']);
        $requestVideo->status = 'Completed';
        $requestVideo->save();

        return ServiceResponse::success('Requested Video submitted successfully and status updated to completed', $review);
    }


    public function GetVideosFromUser() {
        $user = Auth::user();


        $requestvideos = RequestVideo::with(['influencer', 'influencerRequestVideos'])
        ->where('user_id', $user->id)
        ->get();
        return ServiceResponse::success('Video retrieved successfully', $requestvideos);
    }

    public function GetVideosFromInfluencer() {
        $user = Auth::user();


        $requestvideos = RequestVideo::with(['user', 'influencerRequestVideos'])
        ->where('influencer_id', $user->id)
        ->get();
        return ServiceResponse::success('Video retrieved successfully', $requestvideos);
    }

    public function RejectVideo(Request $request, $InfluencerrequestVideoId)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'reason' => 'required|string'
        ]);

        if ($validator->fails()) {
            return ServiceResponse::error('Validation failed', $validator->errors());
        }


        $InfluencerrequestVideo = InfluencerRequestVideo::find($InfluencerrequestVideoId);


        $requestVideo = RequestVideo::find($InfluencerrequestVideo->request_video_id);


        if (!$InfluencerrequestVideo) {
            return ServiceResponse::error('video not found');
        }


        $InfluencerrequestVideo->status = 'Rejected';
        $InfluencerrequestVideo->reason = $data['reason'];
        $InfluencerrequestVideo->save();

        $requestVideo->status = 'Rejected';
        $requestVideo->save();
        return ServiceResponse::success('Video rejected', $InfluencerrequestVideo);
    }


    public function getRejectedVideosFromUser() {
        $user = Auth::user();

        $requestVideos = RequestVideo::with(['influencer', 'influencerRequestVideos'])
            ->where('user_id', $user->id)
            ->where('status', 'Rejected')
            ->get();

        return ServiceResponse::success('Rejected Videos retrieved successfully', $requestVideos);
    }



    public function getCompletedVideosFromUser() {
        $user = Auth::user();

        $requestVideos = RequestVideo::with(['influencer', 'influencerRequestVideos'])
            ->where('user_id', $user->id)
            ->where('status', 'Completed')
            ->get();

        return ServiceResponse::success('Completed Videos retrieved successfully', $requestVideos);
    }







}
