<?php

Auth::routes(['register' => false]);
Route::get('logout', 'Auth\LoginController@logout');
Route::get('profile', 'Profile\ProfileController@profile')->middleware('auth');;

// Home page
Route::get('/', 'MainController@home');

Route::get('/meters/values', 'MetersController@metersValues')->middleware('auth');

// //Ночной мониторинг
// Route::get('/water_meters/values', 'MetersController@waterValuesForNight');

// Device
Route::get('/meters/{meter}', 'MetersController@show')->middleware('auth');

Route::get('/meters/night_water/{meter}', 'MetersController@showNightWater')->middleware('auth');

Route::get('/meters/{meter}/consumption/{days}', 'MetersController@consumption')->middleware('auth');
Route::get('/meters/{meter}/consumption_by_night/{days}', 'MetersController@consumption_by_night')->middleware('auth');

// Consumptions
Route::get('/meters/{meter}/last_consumption', 'MetersController@last_consumption')->middleware('auth');

Route::get('/meters/{meter}/last_electricity_consumption', 'MetersController@last_electricity_consumption')->middleware('auth');

Route::get('/meters/{meter}/last_water_consumption', 'MetersController@last_water_consumption')->middleware('auth');

Route::get('/meters/{meter}/last_night_water_consumption', 'MetersController@lastNightWaterConsumption')->middleware('auth');

// District
Route::get('/districts/{district}', 'DistrictsController@show')->middleware('auth');

// Sector
Route::get('/sectors/{sector}', 'SectorsController@show')->middleware('auth');

// Object
Route::get('/objects/{object}', 'ObjectsController@show')->middleware('auth');

// Building
Route::get('/buildings/{building}', 'BuildingsController@show')->middleware('auth');

// Game
Route::get('/games/sapper', 'GamesController@sapper');

// Monitoring
Route::get('/meters/{meter}/monitoring', 'MetersController@monitoring')->middleware('auth');

// Driver
Route::get('/meters/{meter}/driver', 'DriversController@show');

// Params
Route::get('/meters/{meter}/params', 'DriversController@params');

// Fresh last actual data (request to meter) and write values to DB (type = water, electricity or heat) for 1 meter
Route::get('/meters/refresh/{meter}', 'DriversController@writeSingleMeterFreshData');

// For Cron tasks write to DB every hour without night (6:00 to 23:00)
Route::get('/meters/cron_refresh/pulsar', 'DriversController@writeCronRefreshPulsar');
Route::get('/meters/cron_refresh/oven', 'DriversController@writeCronRefreshOven');
Route::get('/meters/cron_refresh/mercury', 'DriversController@writeCronRefreshMercury');
// For Cron tasks write to DB every 1 minute at night time (23:01 to 05:59)
Route::get('/meters/cron_refresh/pulsar_night', 'DriversController@ ');

// Request electricity devices and write their consumptions
Route::get('/meters/write/{type}', 'DriversController@write');

// Request for print report with consumptions for each sector of military districts
Route::post('/report_object', 'ReportController@show_object');

Route::post('/report', 'ReportController@show')->middleware('auth');

Route::get('/test/oven30', 'DriversController@testOven30')->middleware('auth');

Route::get('/observing', 'MetersController@observe')->middleware('auth');

Route::get('/observing_night', 'MetersController@observe_night')->middleware('auth');

Route::get('/building_list', 'BuildingsController@list')->middleware('auth');
Route::post('/building_list', 'BuildingsController@listWithFilters')->name('setup-filter')->middleware('auth');
Route::get('/building_list_night_water', 'BuildingsController@listNightWorkedWater')->middleware('auth');

Route::get('/c2000/ping/{meter}', 'MetersController@c2000_ping')->middleware('auth');

Route::get('/info', 'InfoController@show');