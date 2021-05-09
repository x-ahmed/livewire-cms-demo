<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(
    attributes: [
        'middleware' => [
            'auth:sanctum',
            'verified',
        ],
    ],
    routes: function () {
        Route::view(uri: '/pages', view: 'admin.pages')->name(name: 'pages');
        Route::view(uri: '/', view: 'dashboard')->name(name: 'dashboard');
    }
);
