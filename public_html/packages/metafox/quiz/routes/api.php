<?php

namespace MetaFox\Quiz\Http\Controllers\Api;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'namespace'  => __NAMESPACE__,
    'middleware' => 'auth:api',
], function () {
    // Quiz's Forms
    Route::get('quiz/form/{id?}', 'QuizController@form');
    Route::get('quiz/search-form', 'QuizController@searchForm');

    Route::patch('quiz/approve/{id}', 'QuizController@approve');
    Route::patch('quiz/feature/{id}', 'QuizController@feature');
    Route::patch('quiz/sponsor/{id}', 'QuizController@sponsor');
    Route::patch('quiz/sponsor-in-feed/{id}', 'QuizController@sponsorInFeed');
    Route::resource('quiz', 'QuizController');

    //Quiz Result API endpoints
    Route::get('quiz-result/view-individual-play', 'ResultController@viewIndividualPlay');
    Route::resource('quiz-result', 'ResultController')->only(['index', 'store']);

    //Quiz Question API endpoints
    Route::get('quiz-question/view-plays', 'QuestionController@viewPlays');
});
