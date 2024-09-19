<?php

namespace App\Http\Controllers\API;

use App\Helpers\Helper;
use App\Helpers\ServiceResponse;
use App\Http\Controllers\Controller;
use App\Models\Giveaway;
use App\Models\GiveawayLike;
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
        $user = Auth::user();
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


        $Giveaway->user_id = $user->id;
        $Giveaway->title = $request->input('title');
        $Giveaway->description = $request->input('description');
        $Giveaway->price = $request->input('price');
        $Giveaway->image = $imageUrl;


        $Giveaway->save();


        return ServiceResponse::success('Giveaway added successfully', $Giveaway);
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
