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

Route::group(['namespace' => 'API\Campaign', 'prefix' => 'campaign'], function (Router $route) {
    $route->get('/get', 'CampaignController@index')->name('get-campaigns');
    $route->get('/create', 'CampaignController@create')->name('create-campaign');
});
