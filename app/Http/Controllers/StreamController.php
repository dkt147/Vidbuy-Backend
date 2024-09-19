<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Helpers\ServiceResponse;
use App\Models\Stream;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StreamController extends Controller
{
    //






    public function add(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string',
            'sub_title' => 'required|string',
            'image' => 'required|string',
            'duration' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ServiceResponse::error('Validation failed', $validator->errors());
        }


        $Stream = new Stream();

        $imageUrl = Helper::getBase64ImageUrl($request->input('image'));


        $Stream->user_id = $user->id;
        $Stream->category_id = $request->input('category_id');
        $Stream->title = $request->input('title');
        $Stream->sub_title = $request->input('sub_title');
        $Stream->image = $imageUrl;
        $Stream->duration = $request->input('duration');


        $Stream->save();


        return ServiceResponse::success('Stream added successfully', $Stream);
    }
}
