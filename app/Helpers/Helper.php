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

    static public function returnPartialImageUrl($imagePath)
    {
        // If the $imagePath is null or empty, return null
        if (!$imagePath || $imagePath == "") {
            return null;
        }

        $partialImageUrl = '';

        // Check if the $imagePath starts with "http://" or "https://"
        if (strpos($imagePath, 'http://') === 0 || strpos($imagePath, 'https://') === 0) {
            // If it's a full URL, extract the part after the domain, which should start with /images
            $parsedUrl = parse_url($imagePath);
            $partialImageUrl = $parsedUrl['path']; // This gives the part starting from /images
        } else {
            // If it's already a partial path (like starting with /images), return it as is
            $partialImageUrl = $imagePath;
        }

        // Ensure that the returned path always starts with "/images"
        if (strpos($partialImageUrl, '/images') !== 0) {
            return null; // Optionally handle cases where the path doesn't start with /images
        }

        // Return the partial image URL
        return $partialImageUrl;
    }

    static public function returnFullImageUrl($imagePath)
    {
        if (!$imagePath || $imagePath == "") {
            return null;
        }

        return Storage::disk('s3')->temporaryUrl($imagePath, now()->addMinutes(120)); //
        //$imagePath = 'images/662e227e61176.png'; // Example image path retrieved from the database
        $baseUrl = env('IMAGE_BASE_URL'); // Get the base URL from Laravel configuration
        // dd(strpos($baseUrl, 'http://') !== 0 && strpos($baseUrl, 'https://') !== 0);
        $fullImageUrl = '';
        // Check if $baseUrl already contains "http://" or "https://"
        if (strpos($baseUrl, 'http://') !== 0 || strpos($baseUrl, 'https://') !== 0) {
            // Concatenate the base URL with the image path
            $fullImageUrl = rtrim($baseUrl, '/') . '/' . ltrim($imagePath, '/');
        } else {
            $fullImageUrl = $baseUrl;
            // dd($fullImageUrl);

        }

        // Return the full image URL
        return $fullImageUrl;
    }

    static public function deleteImageFromS3($data){

        function isBase64($string) {
            return preg_match('/^data:image\/\w+;base64,/', $string);
        }

        $image = isset($data["image"]) ? $data["image"] : null;

        if($image){

            if (!isBase64($image)) {
                if (Storage::disk('s3')->exists($image)) {
                    Storage::disk('s3')->delete($image);
                }
            }

        }
    }

    static public function getBase64ImageUrl($data)
    {
        // dd($data["image"]);
        $image = isset($data["image"]) ? $data["image"] : null;

        if ($image) {
            // Strip the base64 prefix if it exists
            $base64Image = preg_replace('/^data:image\/\w+;base64,/', '', $image);

            // Create a unique filename for the image
            $filename = uniqid() . '.png'; // You can adjust the file extension as needed

            // Get the path where you want to save the image

            $path = base_path(env('IMAGE_BASE_FOLDER') . $filename);

            // dd($path);

            // Convert base64 to binary data and save it to a file
            // Storage::disk('s3')->put('folder/file.jpg', file_get_contents($filePath));
            // $file = file_put_contents($path, base64_decode($base64Image));
            $file = Storage::disk('s3')->put('images/' . $filename, base64_decode($base64Image));

            if ($file) {
                // File saved successfully
                // Now you can use $path to save the image path in your database
                $imagePath = 'images/' . $filename;

                // Save $imagePath in your database for the product
                // Example:
                // $product->image = $imagePath;
                // $product->save();

                // Return the saved image path
                return $imagePath;
            } else {
                // Error saving the file
                return null;
            }
        } else {
            // No image provided
            return null;
        }
    }
}
