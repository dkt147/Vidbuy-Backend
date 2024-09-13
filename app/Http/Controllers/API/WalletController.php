<?php

namespace App\Http\Controllers\API;

use App\Helpers\ServiceResponse;
use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    //

    public function Depositadd(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'amount' => 'required|string',
            'currency' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ServiceResponse::error('Validation failed', $validator->errors());
        }

        $Despositwallet = new Wallet();

        $Despositwallet->user_id = $user->id;
        $Despositwallet->amount = $request->input('amount');
        $Despositwallet->currency = $request->input('currency');

        $Despositwallet->save();


        return ServiceResponse::success('Deposit added successfully', $Despositwallet);
    }

    public function DepositList()
    {
        $user = Auth::user();

        $depositlist = Wallet::where('user_id', $user->id)->get();

        $total = $depositlist->sum('amount');

        $res = [
            'list' => $depositlist,
            "totalAmount" => $total
        ];

        return ServiceResponse::success('Deposit List retrieved successfully', $res);
    }


}
