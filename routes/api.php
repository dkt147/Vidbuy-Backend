<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ContactUsController;
use App\Http\Controllers\API\CountryController;
use App\Http\Controllers\API\DonationController;
use App\Http\Controllers\API\InfluencerCategoryController;
use App\Http\Controllers\API\InfluencerController;
use App\Http\Controllers\API\InfluencerDetailController;
use App\Http\Controllers\API\LanguageController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\SocialController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\UserDetailController;
use App\Http\Controllers\API\VideoTypeController;
use App\Http\Controllers\API\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/signup-via-email', [SocialController::class, 'signupViaEmail']);
Route::post('/verify-signup', [SocialController::class, 'verifyCode']);
Route::post('/login', [SocialController::class, 'loginViaEmail']);
Route::post('/forget-password', [SocialController::class, 'forgetPassword']);
Route::post('/validate-otp-and-change-password', [SocialController::class, 'validateOtpAndChangePassword']);


Route::middleware('auth:api')->group(function () {

    // Route::post('/logout', [SocialController::class, 'logout']);


    Route::post('/user/profile/edit', [SocialController::class, 'Useredit']);
    Route::post('/influencer/profile/edit', [SocialController::class, 'Influenceredit']);
    Route::post('/delete/account', [SocialController::class, 'deleteUserAccount']);



    Route::post('/user/language/add', [UserDetailController::class, 'UserLanguage']);
    Route::post('/user/notification/add', [UserDetailController::class, 'UserNotification']);

    Route::post('/influencer/language/add', [InfluencerController::class, 'influencerLanguage']);
    Route::post('/influencer/notification/add', [InfluencerController::class, 'influencerNotification']);

    // ===================================================CATEGORY=================================================================

    Route::post('/influencer/category/add', [InfluencerDetailController::class, 'InfluencerCategoryadd']);
    Route::get('/influencer/selected/category/list', [InfluencerDetailController::class, 'InfluencerSelectedCategory']);

    // ===================================================VIDEO TYPE=================================================================

    Route::post('/influencer/videotype/add', [InfluencerDetailController::class, 'InfluencerVideoTypeadd']);
    Route::get('/influencer/videotype/list', [InfluencerDetailController::class, 'InfluencerSelectedVideoType']);
    Route::post('/influencer/videotype/price/add/{influVideoTypeId}', [InfluencerDetailController::class, 'InfluencerSelectedVideoTypePriceAdd']);
    Route::get('/influencer/videotype/price/list', [InfluencerDetailController::class, 'InfluencerSelectedVideoTypePrice']);

    // ===================================================PRICE RANGE=================================================================

    Route::post('/influencer/price-range/add', [InfluencerDetailController::class, 'InfluencerPriceRangeadd']);
    Route::get('/influencer/price-range/list', [InfluencerDetailController::class, 'InfluencerSelectedPriceRange']);

    Route::get('/influencer/donation', [InfluencerController::class, 'getDontaion']);


    Route::post('/wallet/deposit/add', [WalletController::class, 'Depositadd']);
    Route::get('/deposit/list', [WalletController::class, 'DepositList']);

    Route::post('/donation/add', [DonationController::class, 'addDonation']);



    Route::post('/contact-us/add', [ContactUsController::class, 'addMessage']);


});


Route::get('/languages/list', [LanguageController::class, 'list']);
Route::post('/languages/add', [LanguageController::class, 'add']);


Route::post('/category/add', [CategoryController::class, 'add']);

Route::post('/video-types/add', [VideoTypeController::class, 'add']);

Route::get('/category/list', [CategoryController::class, 'list']);
Route::get('/countries/list', [CountryController::class, 'list']);

Route::get('/video-types/list', [VideoTypeController::class, 'list']);

Route::get('/price-range/list', [InfluencerDetailController::class, 'PriceRange']);



Route::get('/influencer/list', [UserController::class, 'InfluencerList']);

Route::get('/admin/pending-influencer', [AdminController::class, 'pendingInfluencer']);
Route::get('/admin/approved-influencer', [AdminController::class, 'approvedInfluencer']);
Route::get('/admin/cancelled-influencer', [AdminController::class, 'approvedInfluencer']);
Route::post('/admin/update-status/{id}', [AdminController::class, 'updateInfluencerStatus']);
