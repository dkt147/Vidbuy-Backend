<?php

namespace App\Http\Controllers\API;

use App\Helpers\ServiceResponse;
use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    //

    public function addReview(Request $request)
    {
        $data = $request->all();

        // Validate the input data
        $validator = Validator::make($data, [
            'user_id' => 'required|exists:users,id',
            'influencer_id' => 'required|exists:users,id',
            'message' => 'required|string',
            'rating' => 'required|numeric|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return ServiceResponse::error('Validation failed', $validator->errors());
        }


        $review = Review::where('user_id', $data['user_id'])
            ->where('influencer_id', $data['influencer_id'])
            ->first();

        if ($review) {
            return ServiceResponse::error('Review already given');
        }

        // Create a new review
        $review = new Review();
        $review->user_id = $data['user_id'];
        $review->influencer_id = $data['influencer_id'];
        $review->message = $data['message'];
        $review->rating = (float)$data['rating'];
        $review->save();

        // Calculate new average rating and count
        $ratings = Review::where('influencer_id', $data['influencer_id'])->get();
        $reviewCount = $ratings->count();

        $reviewSum = $ratings->sum('rating');
        $reviewAvg = $reviewCount > 0 ? $reviewSum / $reviewCount : 0.0;

        $influencer = User::find($data['influencer_id']);
        if ($influencer) {
            $influencer->avg_rating = $reviewAvg;
            $influencer->review_count = $reviewCount;
            $influencer->save();
        }

        return ServiceResponse::success('Review added successfully', $review);
    }
}
