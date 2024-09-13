<?php

namespace App\Http\Controllers\API;

use App\Helpers\ServiceResponse;
use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ContactUsController extends Controller
{
    //
    public function addMessage(Request $request)
    {
        $user = Auth::user();


        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:users,email',
            'message' => 'required|string',
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ServiceResponse::error('Validation failed', $validator->errors());
        }

        $addmessage = new ContactUs();
        $addmessage->user_id = $user->id;
        $addmessage->name = $request->input('name');
        $addmessage->email = $request->input('email');
        $addmessage->message = $request->input('message');
        $addmessage->save();


        return ServiceResponse::success('Donation added successfully', $addmessage);
    }
}
