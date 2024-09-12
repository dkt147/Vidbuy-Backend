<?php

namespace App\Http\Controllers\API;

use App\Helpers\ServiceResponse;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Influencer;
use App\Models\InfluencerCategory;
use App\Models\InfluencerVideoPrice;
use App\Models\InfluencerVideoType;
use App\Models\VideoType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InfluencerDetailController extends Controller
{
    //

    public function InfluencerCategoryadd(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return ServiceResponse::error('Validation failed', $validator->errors());
        }

        $category = Category::find($request->input('category_id'));

        if (!$category) {
            return ServiceResponse::error('Category not found');
        }

        $influCategory = new InfluencerCategory();

        $influCategory->user_id = $userId;
        $influCategory->category_id = $request->input('category_id');
        $influCategory->category_name = $category->name;

        $influCategory->save();

        return ServiceResponse::success('Influencer Category added successfully', $influCategory);
    }



    public function InfluencerSelectedCategory()
    {
        $user = Auth::user();
        $userId = $user->id;
        $selectedCat = InfluencerCategory::where('user_id', $userId)->get();

        if ($selectedCat->isEmpty()) {
            return ServiceResponse::error('No Selected Cateogory found for the given user ID');
        }

        return ServiceResponse::success('Selected Cateogory retrieved successfully', $selectedCat);
    }



    public function InfluencerVideoTypeadd(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $validator = Validator::make($request->all(), [
            'video_type_id' => 'required|exists:video_types,id',
        ]);

        if ($validator->fails()) {
            return ServiceResponse::error('Validation failed', $validator->errors());
        }

        $type = VideoType::find($request->input('video_type_id'));

        if (!$type) {
            return ServiceResponse::error('Category not found');
        }

        $influvideotype = new InfluencerVideoType();

        $influvideotype->user_id = $userId;
        $influvideotype->video_type_id = $request->input('video_type_id');
        $influvideotype->video_type_name = $type->name;

        $influvideotype->save();

        return ServiceResponse::success('Influencer Video type added successfully', $influvideotype);
    }

    public function InfluencerSelectedVideoType()
    {
        $user = Auth::user();
        $userId = $user->id;

        $priceRanges = InfluencerVideoType::where('user_id', $userId)->get();

        if ($priceRanges->isEmpty()) {
            return ServiceResponse::error('No Video Type found for the given user ID');
        }

        return ServiceResponse::success('Selected Video types retrieved successfully', $priceRanges);
    }




    public function PriceRange(Request $request)
    {
        $priceRanges = [
            '€0-50',
            '€50-100',
            '€100-200',
            '€200-500',
            '€500-700',
            '€700 + ',
        ];

        return response()->json($priceRanges);
    }



    public function InfluencerPriceRangeadd(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $validator = Validator::make($request->all(), [
            'price_range' => 'required',
        ]);

        if ($validator->fails()) {
            return ServiceResponse::error('Validation failed', $validator->errors());
        }

        $influencerPriceRange = Influencer::where('user_id', $userId)->first();

        if (!$influencerPriceRange) {
            return ServiceResponse::error('Influencer details not found');
        }

        $influencerPriceRange->price_range = $request->input('price_range');

        $influencerPriceRange->save();

        return ServiceResponse::success('Influencer Price Range added successfully', $influencerPriceRange);
    }

    public function InfluencerSelectedPriceRange()
    {
        $user = Auth::user();
        $userId = $user->id;

        $priceRanges = Influencer::where('user_id', $userId)->pluck('price_range');

        if ($priceRanges->isEmpty()) {
            return ServiceResponse::error('price Range is not selected');
        }

        return ServiceResponse::success('Selected price range retrieved successfully', $priceRanges);
    }






    public function InfluencerSelectedVideoTypePriceAdd(Request $request, $influVideoTypeId)
    {
        $validator = Validator::make($request->all(), [
            'price' => 'required',
        ]);

        if ($validator->fails()) {
            return ServiceResponse::error('Validation failed', $validator->errors());
        }

        $videoType = InfluencerVideoType::where('id', $influVideoTypeId)->first();

        if (!$videoType) {
            return ServiceResponse::error('Influencer Video Type not found');
        }

        $videoType->price = $request->input('price');

        $videoType->save();

        return ServiceResponse::success('Influencer Video Price added successfully', $videoType);
    }

    public function InfluencerSelectedVideoTypePrice()
    {
        $user = Auth::user();
        $userId = $user->id;

        $priceRanges = InfluencerVideoType::where('user_id', $userId)->pluck('price');

        if ($priceRanges->isEmpty()) {
            return ServiceResponse::error('price Range is not selected');
        }

        return ServiceResponse::success('Selected price range retrieved successfully', $priceRanges);
    }




}
