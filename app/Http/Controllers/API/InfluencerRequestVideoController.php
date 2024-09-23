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
        $review->save();

        $requestVideo = RequestVideo::find($data['request_video_id']);
        $requestVideo->status = 'completed';
        $requestVideo->save();

        return ServiceResponse::success('Requested Video submitted successfully and status updated to completed', $review);
    }
}
