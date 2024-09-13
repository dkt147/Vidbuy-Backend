<?php

namespace App\Http\Controllers\API;

use App\Helpers\ServiceResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function InfluencerList()
    {
        $user = User::where('role_id' , 3)->get();

        return ServiceResponse::success('User Language updated successfully', $user);

    }
}
