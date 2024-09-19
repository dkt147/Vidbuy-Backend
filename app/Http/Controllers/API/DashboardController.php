<?php

namespace App\Http\Controllers\API;

use App\Helpers\ServiceResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\DashboardResource;
use App\Models\Category;
use App\Models\InfluencerCategory;
use App\Models\Stream;
use App\Models\StreamUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    //
    public function list(Request $request)
    {

        $streams = Stream::with(['user', 'category'])->get();

        return ServiceResponse::success('Streams retrieved successfully', $streams);
    }

    public function Trendinglist(Request $request)
    {

        $streams = Stream::with(['user', 'category'])->get();

        $topUsers = StreamUser::select('user_id', DB::raw('count(*) as total_streams'))
            ->groupBy('user_id')
            ->orderBy('total_streams', 'desc')
            ->take(3)
            ->get();


        $userIds = $topUsers->pluck('user_id');


        $users = User::whereIn('id', $userIds)->get();


        $result = [
            'streams' => $streams,
            'top_users' => $users,
        ];

        return ServiceResponse::success('Streams and top users retrieved successfully', $result);
    }



    public function dashboardRecentlyAdded(Request $request)
    {
        $perPage = 10;
        $page = $request->input('page', 1);

        $twentyFourHoursAgo = now()->subHours(24);

        $users = User::where('role_id', 3)
            ->where('created_at', '>=', $twentyFourHoursAgo)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);


        $usersResource = $users->map(function ($user) {
            return new DashboardResource($user);
        });

        return ServiceResponse::success('Dashboard recently added retrieved successfully', [
            'users' => $usersResource,
            'pagination' => $users->toArray(),
        ]);
    }
}
