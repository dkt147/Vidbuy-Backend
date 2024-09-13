<?php

namespace App\Http\Controllers\API;

use App\Helpers\ServiceResponse;
use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function list(Request $request)
    {

        $data = $request->all();

        $search = isset($data['search']) ? $data['search'] : '';
        $page = isset($data['page']) ? $data['page'] : '';

        $list = Country::query();

        if ($search) {
            $list->where('name', 'like', '%' . $search . '%');
        }

        $page = $request->input('page', 1);

        $list = $list->paginate(10, ['*'], 'page', $page);

        return response()->json($list);
    }


}
