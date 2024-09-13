<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ServiceResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    //
    public function pendingInfluencer()
    {
        $pendingInfluencer = User::where('role_id', 3)
            ->where('status', 'Pending')
            ->get();

        return ServiceResponse::success('pending Influencers retrieved successfully', $pendingInfluencer);
    }


    public function approvedInfluencer()
    {
        $approvedInfluencer = User::where('role_id', 3)
            ->where('status', 'Approved')
            ->get();

        return ServiceResponse::success('Approved Influencers retrieved successfully', $approvedInfluencer);
    }


    public function cancelledInfluencer()
    {
        $cancelledInfluencer = User::where('role_id', 3)
            ->where('status', 'Cancelled')
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

        return ServiceResponse::success('Influencer status updated successfully', $influencer);
    }
}
