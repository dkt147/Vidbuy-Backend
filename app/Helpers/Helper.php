<?php

namespace App\Helpers;

use App\Models\ChatRoom;
use App\Models\Currency;
use DateTime;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Pusher\Pusher;

class Helper
{



    static public function returnFullImageUrl($imagePath)
    {
        if (!$imagePath || $imagePath == "") {
            return null;
        }
        $baseUrl = env('IMAGE_BASE_URL');
        $fullImageUrl = '';
        if (strpos($baseUrl, 'http://') !== 0 || strpos($baseUrl, 'https://') !== 0) {
            $fullImageUrl = rtrim($baseUrl, '/') . '/' . ltrim($imagePath, '/');
        } else {
            $fullImageUrl = $baseUrl;
        }
        return $fullImageUrl;
    }

    static public function getBase64ImageUrl($data)
    {
        $image = isset($data) ? $data : null;

        if ($image) {
            $base64Image = preg_replace('/^data:image\/\w+;base64,/', '', $image);

            $filename = uniqid() . '.png';
            $path = public_path(env('IMAGE_BASE_FOLDER') . '/' . $filename);

            $file = file_put_contents($path, base64_decode($base64Image));

            if ($file) {
                $imagePath = env('IMAGE_BASE_FOLDER') .'/'. $filename;
                return self::returnFullImageUrl($imagePath);
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
}
