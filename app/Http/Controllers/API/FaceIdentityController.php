<?php

namespace App\Http\Controllers\API;

use App\Helpers\ServiceResponse;
use App\Http\Controllers\Controller;
use App\Models\FaceIdentity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class FaceIdentityController extends Controller
{
    //

    public function FaceIdentity(Request $request)
    {
        $user = Auth::user();
        $data = $request->all();

        $validator = Validator::make($data, [
            'link' => 'required|file|mimes:mp4,avi,mov|max:10240',
        ]);

        if ($validator->fails()) {
            return ServiceResponse::error('Validation failed', $validator->errors());
        }

        if ($request->hasFile('link')) {
            $video = $request->file('link');

            $filename = time() . '-' . Str::slug(pathinfo($video->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $video->getClientOriginalExtension();

            $path = $video->move(public_path('FaceIdentity'), $filename);
            $video_url = asset('FaceIdentity/' . $filename);
        }

        $slug = $data['slug'] ?? Str::slug($user->name . '-' . pathinfo($filename, PATHINFO_FILENAME));

        $review = new FaceIdentity();
        $review->user_id = $user->id;
        $review->link = $video_url;
        $review->slug = $slug;
        $review->save();

        return ServiceResponse::success('Face identity submitted successfully.', $review);
    }

}
