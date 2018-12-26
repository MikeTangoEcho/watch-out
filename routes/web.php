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

Route::get('/record', 'StreamController@record')
    ->name('stream.record');


Route::resource('streams', 'StreamController');
Route::get('/stream/{stream}/full', 'StreamController@full')
    ->name('stream.full');
Route::get('/streams/{stream}/chunks', 'StreamController@pull')
    ->name('stream.pull');
Route::post('/streams/{stream}/chunks', 'StreamController@push')
    ->name('stream.push');