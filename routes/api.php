<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\ProductController;


Route::post('login', [UserController::class, 'login']);
Route::post('register', [UserController::class, 'register']);

Route::middleware(['admin.api'])->group(function() {
    // Categories -> Hanya diketahui oleh Admin -> Admin Middleware
    Route::post('categories', [CategoryController::class, 'store']);
    Route::get('categories', [CategoryController::class, 'show']);
    Route::put('categories/{id}', [CategoryController::class, 'update']);
    Route::delete('categories/{id}', [CategoryController::class, 'delete']);  
});
    

Route::middleware(['auth.api'])->group(function() {
    // Products -> Bisa diakses oleh Admin dan User -> Auth Middleware
    Route::post('products', [ProductController::class, 'store']);
    Route::get('products', [ProductController::class, 'show']);
    Route::put('products/{id}', [ProductController::class, 'update']);
    Route::delete('products/{id}', [ProductController::class, 'delete']);
    
});
Route::middleware('web')->group(function(){
    Route::get('/oauth/register',[GoogleController::class,'redirect']);
    Route::get('/oauth/google/callback',[googleController::class,'callback']);

});


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