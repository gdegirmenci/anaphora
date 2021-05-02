<?php

use Illuminate\Routing\Router;
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

Route::group(['namespace' => 'API\Campaign', 'prefix' => 'campaigns'], function (Router $route) {
    $route->get('/get', 'CampaignController@index')->name('get-campaigns');
    $route->get('/create', 'CampaignController@create')->name('create-campaign');
});

Route::group(['namespace' => 'API', 'prefix' => '/dashboard'], function (Router $route) {
    $route->get('/get', 'DashboardController@index')->name('get-dashboard');
});
