<?php

namespace App\Http\Controllers\API;

use App\Helpers\Helper;
use App\Helpers\ServiceResponse;
use App\Http\Controllers\Controller;
use App\Models\Giveaway;
use App\Models\GiveawayDoner;
use App\Models\GiveawayInfluencer;
use App\Models\GiveawayLike;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class GiveawayController extends Controller
{
    //

    public function list(Request $request)
    {

        $Giveaway = Giveaway::with(['user'])->get();

        return ServiceResponse::success('GIv$Giveaway retrieved successfully', $Giveaway);
    }



    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|string',
            'image' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ServiceResponse::error('Validation failed', $validator->errors());
        }


        $Giveaway = new Giveaway();

        $imageUrl = Helper::getBase64ImageUrl($request->input('image'));

        $Giveaway->title = $request->input('title');
        $Giveaway->description = $request->input('description');
        $Giveaway->price = $request->input('price');
        $Giveaway->image = $imageUrl;


        $Giveaway->save();


        return ServiceResponse::success('Giveaway added successfully', $Giveaway);
    }

    public function addInfluencerOnGiveawy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'influencer_id' => 'required|exists:users,id',
            'giveaway_id' => 'required|exists:giveaways,id'
        ]);

        if ($validator->fails()) {
            return ServiceResponse::error('Validation failed', $validator->errors());
        }


        $Giveaway = new GiveawayInfluencer();


        $Giveaway->influencer_id = $request->input('influencer_id');
        $Giveaway->giveaway_id = $request->input('giveaway_id');


        $Giveaway->save();


        return ServiceResponse::success('Influencer added on giveaway successfully', $Giveaway);
    }


    public function giveawayDetail($id)
    {

        $giveaway = Giveaway::where('id', $id)->first();

        $giveawayInfluencerIds = GiveawayInfluencer::where('giveaway_id', $id)->pluck('influencer_id')->toArray();

        $influencerDetails = User::whereIn('id', $giveawayInfluencerIds)->get();

        $detail = [
            'giveaway' => $giveaway,
            'influencer_details' => $influencerDetails,
        ];

        return ServiceResponse::success('Detail Retrieved successfully', $detail);
    }


    public function donateInfluencer(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'giveaway_id' => 'required|exists:giveaways,id',
            'influencer_id' => 'required|exists:users,id',
            'points' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return ServiceResponse::error('Validation failed', $validator->errors());
        }

        $points = $request->input('points');

        $Giveaway = new GiveawayDoner();
        $Giveaway->user_id = $user->id;
        $Giveaway->giveaway_id = $request->input('giveaway_id');
        $Giveaway->influencer_id = $request->input('influencer_id');
        $Giveaway->points = $points;

        $Giveaway->save();


        $giveawayInfluencer = GiveawayInfluencer::where('giveaway_id', $request->input('giveaway_id'))
            ->where('influencer_id', $request->input('influencer_id'))
            ->first();

        if ($giveawayInfluencer) {
            $giveawayInfluencer->total_points += $points;
            $giveawayInfluencer->save();
        } else {
            $giveawayInfluencer = new GiveawayInfluencer();
            $giveawayInfluencer->giveaway_id = $request->input('giveaway_id');
            $giveawayInfluencer->influencer_id = $request->input('influencer_id');
            $giveawayInfluencer->total_points = $points;
            $giveawayInfluencer->save();
        }

        return ServiceResponse::success('Donation added successfully', $Giveaway);
    }

    public function giveawayDonerDetail($id)
    {
        $giveaway = Giveaway::where('id' , $id)->get();
        $giveawaydoner = GiveawayDoner::where('giveaway_id', $id)->get();
        $giveawaydoneruser = GiveawayDoner::where('giveaway_id', $id)->pluck('user_id')->toArray();
        $giveawayinfluencer = GiveawayDoner::where('giveaway_id', $id)->pluck('influencer_id')->toArray();


        $userDetails = User::whereIn('id', $giveawaydoneruser)->get();
        $influencerDetail = User::whereIn('id', $giveawayinfluencer)->get();
        $influencerGiveawayDetail = GiveawayInfluencer::whereIn('influencer_id', $giveawayinfluencer)->get();

        $detail = [
            'giveaway' => $giveaway,
            'giveaway_doner' => $giveawaydoner,
            'user_details' => $userDetails,
            'influencer_details' => $influencerDetail,
            'influencer_giveaway_details' => $influencerGiveawayDetail,
        ];

        return ServiceResponse::success('Detail Retrieved successfully', $detail);
    }










    public function delete($id)
    {

        $Giveaway = Giveaway::find($id);

        if (!$Giveaway) {
            return response()->json(['error' => 'Giveaway not found'], 404);
        }

        $deleted = $Giveaway->delete();

        if ($deleted) {
            return response()->json(['message' => 'Giveaway deleted successfully'], 200);
        } else {
            return response()->json(['error' => 'Failed to delete Giveaway'], 500);
        }
    }


    public function likeByUser(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'giveaway_id' => 'required|exists:giveaways,id'
        ]);

        if ($validator->fails()) {
            return ServiceResponse::error('Validation failed', $validator->errors());
        }

        $existingLike = GiveawayLike::where('user_id', $user->id)
            ->where('giveaway_id', $request->input('giveaway_id'))
            ->first();

        $giveaway = Giveaway::find($request->input('giveaway_id'));

        if ($existingLike) {
            return ServiceResponse::error('You have already liked this giveaway');
        }

        $giveawayLike = new GiveawayLike();
        $giveawayLike->user_id = $user->id;
        $giveawayLike->giveaway_id = $request->input('giveaway_id');
        $giveawayLike->save();

        $giveaway->increment('like');

        return ServiceResponse::success('Giveaway Like added successfully', $giveawayLike);
    }

    public function dislikeByUser(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'giveaway_id' => 'required|exists:giveaways,id'
        ]);

        if ($validator->fails()) {
            return ServiceResponse::error('Validation failed', $validator->errors());
        }


        $existingLike = GiveawayLike::where('user_id', $user->id)
            ->where('giveaway_id', $request->input('giveaway_id'))
            ->first();



        $existingLike->delete();


        $giveaway = Giveaway::find($request->input('giveaway_id'));
        $giveaway->decrement('like');

        return ServiceResponse::success('Giveaway Like removed successfully', $existingLike);
    }
}
