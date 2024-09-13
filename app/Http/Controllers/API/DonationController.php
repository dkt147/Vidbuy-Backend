<?php

namespace App\Http\Controllers\API;

use App\Helpers\ServiceResponse;
use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Order;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DonationController extends Controller
{
    //
    public function addDonation(Request $request)
    {
        $user = Auth::user();


        $validator = Validator::make($request->all(), [
            'influencer_id' => 'required|exists:users,id',
            'price' => 'required|numeric',
            'currency' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ServiceResponse::error('Validation failed', $validator->errors());
        }

        $donationAmount = $request->input('price');


        $userWallet = Wallet::where('user_id', $user->id)->first();
        $userWalletBalance = $userWallet->amount;

        if ($userWalletBalance < $donationAmount) {
            return ServiceResponse::error('Insufficient balance in your wallet to make this donation.');
        }


        $order = new Donation();
        $order->user_id = $user->id;
        $order->influencer_id = $request->input('influencer_id');
        $order->price = $donationAmount;
        $order->currency = $request->input('currency');
        $order->save();


        $userWallet->amount -= $donationAmount;
        $userWallet->save();

        return ServiceResponse::success('Donation added successfully', $order);
    }
}
