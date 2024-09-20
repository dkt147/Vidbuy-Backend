<?php

namespace App\Http\Controllers\API;

use App\Helpers\ServiceResponse;
use App\Http\Controllers\Controller;
use App\Models\Review;
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
            'rating' => 'required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return ServiceResponse::error('Validation failed', $validator->errors());
        }

        // Check if the review already exists
        $review = Review::where('user_id', $data['user_id'])
            ->where('influencer_id', $data['influencer_id'])
            ->first();


        if ($review) {
            return ServiceResponse::error('Review Already given', $data);
        }

        // Create a new review
        $review = new Review();
        $review->user_id = $data['user_id'];
        $review->influencer_id = $data['influencer_id'];
        $review->message = $data['message'];
        $review->rating = $data['rating'];
        $review->save();

        $ratings = Review::where('influencer_id', $data['influencer_id'])->get();
        $reviewCount = $ratings->count();
        $reviewAvg = $ratings->avg('rating');
        $reviewAvg = round($reviewAvg, 1);




        return ServiceResponse::success('Review added successfully', $review);
    }
}
