<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VerifyUserController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\DiseasesController;
use App\Http\Controllers\HealthRecordsController;
use App\Http\Controllers\PostCommentsController;
use App\Http\Controllers\PostLikesController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\ApllyToRoleController;
use App\Http\Controllers\ExcersiseController;
use App\Http\Controllers\ExcersiseMediaController;
use App\Http\Controllers\WorkoutCategorieController;
use App\Http\Controllers\WorkoutController;
use App\Http\Controllers\WorkoutExcersisesController;
use App\Http\Controllers\WorkoutReviewController;
use App\Models\Excersise;
use App\Http\Controllers\DashboardsController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ChallengesExcercisesController;
use App\Http\Controllers\ChallengeController;

// Route::group(['middleware' => ['apikey', 'json', 'lang']], function () {
//     Route::get('/testlang', function () {
//         return response()->json(['message' => __('messages.somthing went wrong'), 'local' => App::currentLocale()], 200);
//     });
// });

//No token needed routes
Route::group(['middleware' => ['apikey', 'json', 'lang', 'bots', 'timeZone']], function () {
    //user Registration
    Route::controller(AuthController::class)->group(function () {
        Route::post('/', 'register');
        Route::post('/gettoken', 'getTokenfromRefreshToken');
        Route::post('/login', 'login');
    });
    //Google + Facebook
    Route::post('/login/callback', [SocialiteController::class, 'handleProviderCallback']);
    //Forget Password
    Route::prefix('forgetpassword')->controller(ForgotPasswordController::class)->group(function () {
        Route::post('/', 'submitForgetPasswordForm')->middleware(['emailVerified']);
        Route::post('/verify', 'verifytoken');
        Route::post('/reset', 'resetpassword')->middleware('emailVerified');;
    });
});

//verify Email
Route::group(['middleware' => ['apikey', 'json', 'lang', 'bots', 'timeZone']], function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::prefix('emailVerfiy')->controller(VerifyUserController::class)->group(function () {
            Route::post('/', 'verifyAccount');
            Route::post('/reget', 'reGetCode');
            Route::post('/newEmailReget', 'newEmailReGetCode');
        });
    });
});

//Token needed routes
Route::group(['middleware' => ['apikey', 'json', 'lang', 'timeZone', 'emailVerified', 'seen', 'deltedAccount', 'auth:api']], function () {
    Route::prefix('user')->controller(AuthController::class)->group(function () {
        Route::post('/info', 'info'); //add his info
        Route::get('/profile', 'useraccount'); //get his profile
        Route::get('/profile/{id}', 'show'); //get user->id profile
        Route::get('/logout', 'logout')->withoutMiddleware(['emailVerified', 'deltedAccount']);
        Route::get('/all_logout', 'allLogout');
        Route::put('/update', 'update');
        Route::post('/updateEmail', 'updateEmail')->middleware('provider');
        Route::post('/updatePassword', 'updatePassword')->middleware('provider');
        Route::post('/verifyNewEmail', 'confirmNewEmail');
        Route::post('/delete', 'firstdestroy');
        Route::post('/recover/reget', 'reGetRecoveryCode')->middleware('bots')->withoutMiddleware('deltedAccount');
        Route::post('/recover', 'recoverVerify')->middleware('bots')->withoutMiddleware('deltedAccount');
    });
    Route::prefix('user')->controller(FollowController::class)->group(function () {
        Route::get('/follow/{id}', 'follow');
        Route::get('/unfollow/{id}', 'unfollow');
        Route::get('/followers/{id}', 'getFollowers');
        Route::get('/following/{id}', 'getFollowing');
        Route::get('/block/{id}', 'block');
        Route::get('/unblock/{id}', 'unblock');
        Route::get('/blocklist', 'blocklist');
    });
    Route::prefix('diseases')->controller(DiseasesController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });
    Route::prefix('hRecord')->controller(HealthRecordsController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::delete('/', 'destroy');
    });
    Route::prefix('posts')->controller(PostsController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/vote/{id}/{vote_id}', 'vote')->middleware('block');
        Route::get('/save/{id}', 'savePost')->middleware('block');
        Route::get('/savedPosts', 'savePostList');
        Route::get('/myPosts', 'showMyPosts')->middleware('posts');
        Route::get('/showPosts/{user_id}', 'showOthersPosts')->middleware('block');
        Route::get('/report/{id}', 'report')->middleware('block');
        Route::post('/', 'storeNormal')->middleware('posts');
        Route::post('/poll', 'storepoll')->middleware('posts');
        Route::put('/update/{id}', 'updateNormal')->middleware('posts');
        Route::put('/updatePoll/{id}', 'updatePoll')->middleware('posts');
        Route::delete('/{id}', 'destroy')->middleware('posts');
    });
    Route::prefix('posts/like')->middleware(['block', 'likeable'])->controller(PostLikesController::class)->group(function () {
        Route::get('/list/{id}', 'likeList');
        Route::get('/{id}/{type}', 'like');
    });
    Route::prefix('posts/comment')->middleware(['block'])->controller(PostCommentsController::class)->group(function () {
        Route::get('/{id}', 'index');
        Route::post('/{id}', 'store');
        Route::put('/{id}', 'update')->withoutMiddleware('block');
        Route::delete('/{id}', 'destroy')->withoutMiddleware('block');
        Route::get('/report/{id}', 'report')->withoutMiddleware('block');
    });
    Route::prefix('cv')->controller(ApllyToRoleController::class)->group(function () {
        Route::get('/', 'show');
        Route::Post('/deleteRole', 'DowngradeRole');
        Route::get('/{id}', 'showOthers');
        Route::get('/acc/{id}', 'Accept')->middleware('ms');
        Route::get('/ref/{id}', 'Refuse')->middleware('ms');
        Route::post('/', 'store');
        Route::delete('/', 'destroy');
    });
    Route::prefix('dash')->middleware('ms')->controller(DashboardsController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/cvs', 'CVsDashboard');
        Route::get('/posts', 'PostsDashboard');
        Route::get('/repPosts', 'ReportedPosts');
        Route::get('/RPComments', 'ReportedComments');
        Route::get('/ARPosts/{id}', 'AcceptPost');
        Route::get('/ARComments/{id}', 'AcceptCommentReport');
        Route::get('/repChallenges', 'ReportedChallenges');
        Route::get('/ARChallenges/{id}', 'AcceptChallengeReport');
    });
    Route::prefix('search')->controller(SearchController::class)->group(function () {
        Route::post('/', 'search');
        Route::post('/sug', 'searchSug');
    });
    Route::prefix('chEx')->middleware('ms')->controller(ChallengesExcercisesController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::put('/{id}', 'update');
        Route::get('/{id}', 'show');
        Route::delete('/{id}', 'destroy');
    });
    Route::prefix('ch')->controller(ChallengeController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/list', 'exList');
        Route::get('/rep/{id}', 'report'); //add Block
        Route::get('/rev/{id}/{num}', 'review'); //add Block
        Route::get('/sub/{id}', 'sub'); //add Block
        Route::post('/done/{id}', 'done'); //add Block
        Route::post('/', 'store')->middleware('coach');
        Route::put('/{id}', 'update')->middleware('coach');
        Route::put('/{id}', 'update')->middleware('coach');
        Route::get('/my', 'showMy')->middleware('coach');
        Route::get('/show/{id}', 'show'); //add block protect
        Route::get('/mySub', 'showMySubs');
        Route::delete('/{id}', 'destroy')->middleware('coach');
    });
});
Route::get('/any', function (Request $request) {
    $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIyIiwianRpIjoiOWRhZWRhMmRmZDA0YzVmNTEzN2I1MmZjZDkxZDg2MTVhNzE2Njk3YWY0Y2UyZTViMzExYTU5MzQ3MzhlM2U1NzU1YWU0OWI2NGEyOTUzNjUiLCJpYXQiOjE2NTcxODY4MTUuNDM1MjI3LCJuYmYiOjE2NTcxODY4MTUuNDM1MjMxLCJleHAiOjE2NTc3OTE2MTUuNDI1NzM1LCJzdWIiOiIxIiwic2NvcGVzIjpbIioiXX0.gF-j4I9Hlh94NOTv__UyUTO-XSf4uVsyu0C607diV7RdiVv4qxU_-MJEhfEOBu1zfzSLncK_ys47DPa7dOZAnZGfUSzZuoJuV5_K7UTGUHX42GI341kxsB-Tbs7IxOneRy-tB2ng63ll4lIZYZGiP9TIlsZhUHgY7WOl7xN6e8VADsTelxOKjMfyjFQScuOaOrjZJXDLD8zCzjctA2Deb7lcIegJPVSQsiVQeCR2nu8CDPK7R_7Vpg_C-lmuGuw7AQJcu06f8LWMEVrwBsgSQ3EnCqZDnXz3YUCGVVisb5C_nYmRVyyiRfewAAsmrcpXdNw24G2bwE-uy2JyMUytsga65G7NLzc5x5NQRf1Pkka6yDoIBcF_WfS3GyurAKTvQzApDdA129ZHNdjFTc9lTLDqzSWF3rcVamYsYwb-FGa86I1eJAESeyNGHbaYenhb64sShKK8HFt7YKFvPv8lNjwt6E3-RGvm8DhOSvEDrD6mUPRuzAdTqBrAs6v44WIZC2UZJ6ZhubZYcFmCGyJhWIAJwHM8sp1nBcuAjx5soWzeevlWU6flT0vinPzJGL25bUCJ29N7Yoq1V0Fzx8wBYfjri-7xulKWq70cZompEjNqKRAJm9ckOYxCuhxrEqxEKAw5yngvLnStPHHU4oPeiopfk9p7pcTJDOIwqen9lcg';
    return ($token);
});

//Workout + Excersise Routes
//Add MiddleWares
Route::group(['middleware' => ['apikey', 'json', 'lang', 'timeZone', 'auth:api', 'emailVerified', 'deltedAccount']], function () {
    Route::prefix('workout_categorie')->controller(WorkoutCategorieController::class)->group(function () {
        Route::get('/show', 'show');
        Route::post('/create', 'create');
        Route::post('/delete', 'destroy');
        Route::post('/update', 'edit');
        Route::get('/all', 'index');
    });

    Route::prefix('workout')->controller(WorkoutController::class)->group(function () {
        Route::get('/show', 'show');
        Route::post('/create', 'create');
        Route::post('/delete', 'destroy');
        Route::post('/update', 'edit');
        Route::get('/all', 'index');
    });

    Route::prefix('workout_excersise')->controller(WorkoutExcersisesController::class)->group(function () {
        Route::get('/show', 'show');
        Route::post('/create', 'create');
        Route::post('/delete', 'destroy');
        Route::post('/update', 'edit');
        Route::get('/all', 'index');
    });

    Route::prefix('excersise')->controller(ExcersiseController::class)->group(function () {
        Route::get('/show', 'show');
        Route::post('/create', 'create');
        Route::post('/delete', 'destroy');
        Route::post('/update', 'edit');
        Route::get('/all', 'index');
    });

    Route::prefix('excersise_media')->controller(ExcersiseMediaController::class)->group(function () {
        Route::get('/show', 'show');
        Route::post('/create', 'create');
        Route::post('/delete', 'destroy');
        Route::post('/update', 'edit');
        Route::get('/all', 'index');
    });

    Route::prefix('workout_review')->controller(WorkoutReviewController::class)->group(function () {
        Route::get('/show', 'show');
        Route::post('/create', 'create');
        Route::post('/delete', 'destroy');
        Route::post('/update', 'edit');
        Route::get('/all', 'index');
    });
});
