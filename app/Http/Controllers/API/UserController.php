<?php

namespace App\Http\Controllers\API;

use App\Helpers\ServiceResponse;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\InfluencerCategory;
use App\Models\InfluencerRequestVideo;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function InfluencerList()
    {
        $user = User::where('role_id', 3)->get();

        return ServiceResponse::success('User Language updated successfully', $user);
    }




    public function InfluencerListByCategory(Request $request)
    {
        $user = Auth::user();

        $searchQuery = $request->input('search');
        $filterCategory = $request->input('category_id');
        $filterLanguage = $request->input('language_id');

        $query = User::query();


        if ($filterCategory) {
            $userIds = InfluencerCategory::where('category_id', $filterCategory)->pluck('user_id');
            $query->whereIn('id', $userIds);
        }

        if ($searchQuery) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('name', 'LIKE', "%{$searchQuery}%");
            });
        }

        $query->orderBy('id', 'desc');

        $perPage = 10;
        $page = $request->input('page', 1);
        $data = $query->paginate($perPage, ['*'], 'page', $page);

        return ServiceResponse::success('List retrieved successfully', $data);
    }


    public function influencerById(Request $request, $id)
    {
        $fromuser = User::with(['influencerCategories.category', 'reviews.reviewer'])
            ->find($id);

        if (!$fromuser) {
            return ServiceResponse::error('Influencer not found', 404);
        }
        $videos = InfluencerRequestVideo::where('user_id',$id)->get();

        $categories = $fromuser->influencerCategories->pluck('category');
        $enhancedReviews = $fromuser->reviews->map(function ($review) {
            return [
                'id' => $review->id,
                'message' => $review->message,
                'rating' => $review->rating,
                'created_at' => $review->created_at,
                'updated_at' => $review->updated_at,
                'reviewer' => $review->reviewer
            ];
        });

        $data = [
            'user' => $fromuser,
            'categories' => $categories,
            'reviews' => $enhancedReviews,
            'videos' => $videos
        ];

        return ServiceResponse::success('Influencer detail retrieved successfully', $data);
    }
}
