<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ServiceResponse;
use App\Http\Controllers\Controller;
use App\Models\FaceIdentity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    //
    public function pendingInfluencer()
    {
        $pendingInfluencer = FaceIdentity::where('status', 'Pending')
            ->with('user')
            ->get();

        return ServiceResponse::success('Pending influencers retrieved successfully', $pendingInfluencer);
    }



    public function approvedInfluencer()
    {
        $approvedInfluencer = FaceIdentity::where('status', 'Approved')
        ->with('user')
        ->get();

        return ServiceResponse::success('Approved Influencers retrieved successfully', $approvedInfluencer);
    }


    public function cancelledInfluencer()
    {
        $cancelledInfluencer = FaceIdentity::where('status', 'Cancelled')
        ->with('user')
        ->get();

        return ServiceResponse::success('Cancelled Influencers retrieved successfully', $cancelledInfluencer);
    }

    public function updateInfluencerStatus(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:Pending,Approved,Cancelled'
        ]);

        if ($validator->fails()) {
            return ServiceResponse::error('Invalid data provided', $validator->errors(), 422);
        }

        $influencer = User::where('role_id', 3)->find($id);

        if (!$influencer) {
            return ServiceResponse::error('Influencer not found', null, 404);
        }

        $influencer->status = $request->status;
        $influencer->save();

        $faceIdentity = FaceIdentity::where('user_id', $influencer->id)->first();

        if ($faceIdentity) {
            $faceIdentity->status = $request->status;
            $faceIdentity->save();
        }

        return ServiceResponse::success('Influencer status and FaceIdentity status updated successfully', [
            'user' => $influencer,
            'face_identity' => $faceIdentity
        ]);
    }

}
