<?php

namespace App\Http\Controllers\API;

use App\Helpers\ServiceResponse;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    //hhell

    public function list(Request $request)
    {

        $data = $request->all();

        $search = isset($data['search']) ? $data['search'] : '';
        $page = isset($data['page']) ? $data['page'] : '';

        $list = Category::query();

        if ($search) {
            $list->where('name', 'like', '%' . $search . '%');
        }

        
        $list = $list->get();

        return self::success("Category List", [ 'list' => $list ]);
    }


    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ServiceResponse::error('Validation failed', $validator->errors());
        }


        $category = new Category();


        $category->name = $request->input('name');


        $category->save();


        return ServiceResponse::success('Category added successfully', $category);
    }
}
