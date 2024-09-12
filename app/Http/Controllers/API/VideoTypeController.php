<?php

namespace App\Http\Controllers\API;

use App\Helpers\ServiceResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Models\TeacherLanguage;
use App\Models\User;
use App\Models\VideoType;
use Illuminate\Support\Facades\Validator;

class VideoTypeController extends Controller
{
    //



    public function list(Request $request) {

        $data = $request->all();

        $search = isset($data['search'])? $data['search'] : '';
        $page = isset($data['page'])? $data['page']: '';

        $list = VideoType::query();

        if ($search) {
            $list->where('name', 'like', '%' . $search . '%');
        }

        $page = $request->input('page', 1);

        $list = $list->paginate(10, ['*'], 'page', $page);

        return response()->json($list);

    }


    public function add(Request $request)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ServiceResponse::error('Validation failed', $validator->errors());
        }


        $videoType = new VideoType();


        $videoType->name = $request->input('name');


        $videoType->save();


        return ServiceResponse::success('Type added successfully', $videoType);
    }






}
