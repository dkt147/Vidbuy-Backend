<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Helpers\ServiceResponse;
use App\Models\StreamUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StreamUserController extends Controller
{
    //

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'stream_id' => 'required|exists:streams,id',
        ]);

        if ($validator->fails()) {
            return ServiceResponse::error('Validation failed', $validator->errors());
        }


        $Stream = new StreamUser();



        $Stream->user_id = $request->input('user_id');
        $Stream->stream_id = $request->input('stream_id');

        $Stream->joining_datetime = now();


        $Stream->save();


        return ServiceResponse::success('Stream added successfully', $Stream);
    }
}
