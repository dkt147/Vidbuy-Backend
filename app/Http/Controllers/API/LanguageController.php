<?php

namespace App\Http\Controllers\API;

use App\Helpers\ServiceResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Models\TeacherLanguage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LanguageController extends Controller
{
    //



    public function list(Request $request)
    {

        $data = $request->all();

        $search = isset($data['search']) ? $data['search'] : '';
        $page = isset($data['page']) ? $data['page'] : '';

        $list = Language::query();

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


        $language = new Language();


        $language->name = $request->input('name');


        $language->save();


        return ServiceResponse::success('Language added successfully', $language);
    }




    // public function addTeacherLanguage(Request $request)
    // {

    //     $data = $request->all();
    //     // Validate incoming request data
    //     $validator = Validator::make($data, [
    //         'languages' => 'required|array',
    //         'user_id' => 'required|exists:users,id'
    //     ]);

    //     if ($validator->fails()) {
    //         return ServiceResponse::error('Validation failed', $validator->errors());
    //     }

    //     TeacherLanguage::where(['teacher_id' => $data['user_id']])->delete();

    //     $languages = $data['languages'];
    //     foreach($languages as $languageId){
    //         TeacherLanguage::create(['teacher_id' => $data['user_id'], 'language_id' => $languageId ]);
    //     }


    //     return ServiceResponse::success('Language added successfully');
    // }

    // public function mylist(Request $request){

    //     $data = $request->all();
    //     // Validate incoming request data
    //     $validator = Validator::make($data, [
    //         'user_id' => 'required|exists:users,id'
    //     ]);

    //     if ($validator->fails()) {
    //         return ServiceResponse::error('Validation failed', $validator->errors());
    //     }

    //     $user = User::where('id', $data['user_id'])->first();
    //     $langs = $user->languages();

    //     return ServiceResponse::success('Languages return successfully', $langs);
    // }

    // public function removeFromMyList(Request $request){

    //     $data = $request->all();
    //     // Validate incoming request data
    //     $validator = Validator::make($data, [
    //         'user_id' => 'required|exists:users,id',
    //         'language_id' => 'required|exists:languages,id'
    //     ]);

    //     if ($validator->fails()) {
    //         return ServiceResponse::error('Validation failed', $validator->errors());
    //     }

    //     $record = TeacherLanguage::where(['language_id' => $data['language_id'], 'teacher_id' =>  $data['user_id'] ])->delete();

    //     return ServiceResponse::success('Language removed successfully', $record);

    // }






}
