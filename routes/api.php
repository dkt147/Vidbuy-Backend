<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ContactUsController;
use App\Http\Controllers\API\CountryController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\DonationController;
use App\Http\Controllers\API\FaceIdentityController;
use App\Http\Controllers\API\GiveawayController;
use App\Http\Controllers\API\InfluencerCategoryController;
use App\Http\Controllers\API\InfluencerController;
use App\Http\Controllers\API\InfluencerDetailController;
use App\Http\Controllers\API\InfluencerRequestVideoController;
use App\Http\Controllers\API\LanguageController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\RequestVideoController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\SocialController;
use App\Http\Controllers\API\StreamController;
use App\Http\Controllers\API\StreamUserController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\UserDetailController;
use App\Http\Controllers\API\VideoTypeController;
use App\Http\Controllers\API\WalletController;
use App\Models\InfluencerRequestVideo;
use App\Models\RequestVideo;
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

Route::get('/user-by-email', [SocialController::class, 'getUser']);



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


    Route::post('/stream/add', [StreamController::class, 'add']);





    Route::delete('/giveaway/delete/{id}', [GiveawayController::class, 'delete']);
    Route::get('/giveaway/list', [GiveawayController::class, 'list']);

    Route::post('/giveaway/like/add', [GiveawayController::class, 'likeByUser']);
    Route::post('/giveaway/dislike', [GiveawayController::class, 'dislikeByUser']);


    Route::post('/influencer/donate', [GiveawayController::class, 'donateInfluencer']);


    Route::post('/review/add', [ReviewController::class, 'addReview']);


    Route::post('/video-request/add', [RequestVideoController::class, 'RequestVideo']);
    Route::get('/video-request/list/influencer', [RequestVideoController::class, 'RequestedVideoListForInfluencer']);
    Route::get('/video-request/list/influencer/pending', [RequestVideoController::class, 'RequestedVideoListForInfluencerPending']);
    Route::get('/video-request/list/influencer/inprogress', [RequestVideoController::class, 'RequestedVideoListForInfluencerInprogress']);
    Route::get('/video-request/list/influencer/completed', [RequestVideoController::class, 'RequestedVideoListForInfluencerCompleted']);
    Route::get('/video-request/list/user', [RequestVideoController::class, 'RequestedVideoListForUser']);
    Route::get('/video-request/delete/{id}', [RequestVideoController::class, 'DeleteRequestedList']);
    Route::post('/video-request/reject/{requestVideoId}', [RequestVideoController::class, 'RejectVideoRequest']);








    Route::post('/request-video', [InfluencerRequestVideoController::class, 'RequestedVideo']);


    Route::get('/get/request-video/from-user', [InfluencerRequestVideoController::class, 'GetVideosFromUser']);
    Route::get('/get/rejected/request-video/from-user', [InfluencerRequestVideoController::class, 'getRejectedVideosFromUser']);
    Route::get('/get/completed/request-video/from-user', [InfluencerRequestVideoController::class, 'getCompletedVideosFromUser']);


    Route::get('/get/request-video/from-influencer', [InfluencerRequestVideoController::class, 'GetVideosFromInfluencer']);
    Route::post('/video/reject/{InfluencerrequestVideoId}', [InfluencerRequestVideoController::class, 'RejectVideo']);



    Route::post('/face-identity', [FaceIdentityController::class, 'FaceIdentity']);
});
Route::post('/giveaway/add', [GiveawayController::class, 'add']);
Route::post('/influencer/giveaway/add', [GiveawayController::class, 'addInfluencerOnGiveawy']);
Route::post('/influencer/giveaway/detail/{id}', [GiveawayController::class, 'giveawayDetail']);
Route::get('/influencer/giveaway/doner/detail/{id}', [GiveawayController::class, 'giveawayDonerDetail']);



Route::post('/stream/user/add', [StreamUserController::class, 'add']);

Route::get('/languages/list', [LanguageController::class, 'list']);
Route::post('/languages/add', [LanguageController::class, 'add']);


Route::post('/category/add', [CategoryController::class, 'add']);

Route::post('/video-types/add', [VideoTypeController::class, 'add']);

Route::get('/category/list', [CategoryController::class, 'list']);
Route::get('/countries/list', [CountryController::class, 'list']);

Route::get('/video-types/list', [VideoTypeController::class, 'list']);

Route::get('/price-range/list', [InfluencerDetailController::class, 'PriceRange']);



Route::get('/influencer/list', [UserController::class, 'InfluencerList']);

Route::get('/influencer/list/by/category', [UserController::class, 'InfluencerListByCategory']);

Route::get('/admin/pending-influencer', [AdminController::class, 'pendingInfluencer']);
Route::get('/admin/approved-influencer', [AdminController::class, 'approvedInfluencer']);
Route::get('/admin/cancelled-influencer', [AdminController::class, 'approvedInfluencer']);
Route::post('/admin/update-status/{id}', [AdminController::class, 'updateInfluencerStatus']);




Route::get('/dashboard/categories', [DashboardController::class, 'dashboardCategory']);
Route::get('/dashboard/recently-added', [DashboardController::class, 'dashboardRecentlyAdded']);
Route::get('/stream/list', [DashboardController::class, 'list']);
Route::get('/trending/list', [DashboardController::class, 'Trendinglist']);





Route::get('/influencer/detail/by-id/{id}', [UserController::class, 'influencerById']);
