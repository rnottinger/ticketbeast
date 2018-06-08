<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/
/*
Route::get('/', function () {
    return view('welcome');
});
*/

/*
 * could hard code route with id = 1 but that doesn't make progress towards having an actual working system
 *  so use {id} instead
 * could specify a closure here and do any logic within this closure
 *      but for the same reason, because eventually want to work towards putting logic in a Controller
 *      instead of filling the routes file will a lot of code
 *      so instead going to use a programming strategy called programming by wishfull thinking
 *      where i refer to a controller that I know doesn't exist yet
 *          I'm going to let the test tell me that it doesn't exist until I actually create it
 */
Route::get('/concerts/{id}', 'ConcertsController@show');

// so when I'm working on nested resources like this where there is orders inside of concerts
// I like to give orders their own dedicated controller
Route::post('/concerts/{id}/orders', 'ConcertOrdersController@store');  // a controller that belongs to the orders of the concerts
// going to use the store method since we are creating a new order