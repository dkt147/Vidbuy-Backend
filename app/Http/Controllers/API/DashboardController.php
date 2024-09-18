<?php

namespace App\Http\Controllers\API;

use App\Helpers\ServiceResponse;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\InfluencerCategory;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function dashboardCategory(Request $request)
    {

        $search = $request->input('search', '');
        $page = $request->input('page', 1);

        $categoryIds = InfluencerCategory::pluck('category_id');

        $categoryDetails = Category::whereIn('id', $categoryIds)->get();


        $list = InfluencerCategory::query();
        if (!empty($search)) {
            $list->where('name', 'like', '%' . $search . '%');
        }

        $list = $list->paginate(10, ['*'], 'page', $page);

        return ServiceResponse::success('Dashboard categories retrieved successfully', [
            'categories' => $categoryDetails,
            'list' => $list
        ]);
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

        return ServiceResponse::success('Dashboard recently added retrieved successfully', [
            'users' => $users
        ]);
    }

}
