<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ChatGroupController;
use App\Http\Controllers\ContactListController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::controller(UserController::class)->group(function(){
    Route::get('users', 'getAllUsers');
    Route::get('user', 'authUser')->middleware('auth:api');
    Route::post('update-user', 'updateUser');
});

Route::controller(AuthController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::get('logout', 'logout')->middleware('auth:api');
    Route::post('forgot-password', 'forgotPassword');
    Route::post('reset-password', 'resetPassword');
});


Route::controller(MessageController::class)->group(function(){
    Route::post('message', 'store');
    Route::post('user-messages', 'userMessages');
});

Route::controller(ContactListController::class)->group(function(){
    Route::get('contact-list', 'index')->middleware('auth:api');
    Route::post('add-contact', 'store');
    Route::get('contact-list/{contactList}', 'show');
    Route::put('contact-list/{contactList}/update', 'update');
    Route::delete('contact-list/{contactList}/delete', 'destroy');
});

Route::controller(ChatGroupController::class)->group(function(){
    Route::get('groups', 'index')->middleware('auth:api');
    Route::post('create-group', 'store');
    Route::get('group/{group}', 'show');
});