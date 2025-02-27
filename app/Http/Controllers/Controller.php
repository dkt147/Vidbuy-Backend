<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;


    public static function success($msg, $data = [], $code = 200){

        $msgArray = array(
            'bool' => true,
            'status' => $code,
            "message" => $msg
        );

        $returnArray = array_merge($msgArray, $data);
        // Log::info('app.requests', ['type'=> 'success', 'request' => request()->all(), 'response' => $msg]);
        return response()->json( $returnArray, $code);
    }

    public static function failure($error, $data = [], $code = 409 ){

        $msgArray = array(
            'bool' => false,
            'status' => $code,
            "message" => $error
        );

        $returnArray = array_merge($msgArray, $data);
        // Log::info('app.requests', ['type'=> 'error', 'request' => request()->all(), 'response' => $error]);
        return response()->json( $returnArray, $code);

    }

}
