<?php

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

Route::resource('books','BookController',[
   'except' => ['create','edit']
]);

Route::post('books/multiple',
    'BookController@storeBooks');

Route::post('books/excel',
    'BookController@storeBooksFromExcel');

Route::post('books/isbn',
    'BookController@isValidIsbn');

Route::resource('authors','AuthorController',[
   'only' => ['index','show']
]);

Route::fallback(function (){
    abort(404, 'API resource not found');
});

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
