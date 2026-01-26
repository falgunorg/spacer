<?php

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

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('dashboard', function () {
    return view('layouts.master');
});

Route::group(['middleware' => 'auth'], function () {


    Route::resource('item-types', 'ItemTypeController');
    Route::get('/api/item-types', 'ItemTypeController@apiItemTypes')->name('api.item_types');

    Route::resource('items', 'ItemController');
    Route::get('/apiItems', 'ItemController@apiItems')->name('api.items');
    Route::get('/tokens', 'ItemController@tokens')->name('tokens');

    Route::resource('user', 'UserController');
    Route::get('/apiUsers', 'UserController@apiUsers')->name('api.users');

    // Cabinet Routes (You likely already have this)
    Route::resource('cabinets', 'CabinetController');
    Route::get('apiCabinets', 'CabinetController@apiCabinets')->name('api.cabinets');
    Route::get('api/cabinet-details/{id}', 'CabinetController@getCabinetDetails');
// Drawer Routes (Add these)
    Route::post('drawers', 'CabinetController@storeDrawer')->name('drawers.store');
    Route::delete('drawers/{id}', 'CabinetController@deleteDrawer')->name('drawers.destroy');

    Route::resource('locations', 'LocationController');
    Route::get('/apiLocations', 'LocationController@apiLocations')->name('api.locations');

    Route::get('api/cabinets-by-location/{id}', 'ItemController@getCabinets');
    Route::get('api/drawers-by-cabinet/{id}', 'ItemController@getDrawers');
    
    
    //desks 
    
     Route::resource('desks', 'DeskController');
    Route::get('apiDesks', 'DeskController@apiDesks')->name('api.desks');
    
    
    Route::get('api/desk-details/{id}', 'DeskController@getDeskDetails');
// Drawer Routes (Add these)
    Route::post('deskparts', 'DeskController@storeDeskPart')->name('deskparts.store');
    Route::delete('deskparts/{id}', 'DeskController@deleteDeskPart')->name('deskparts.destroy');
});
