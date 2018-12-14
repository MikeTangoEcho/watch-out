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

Route::get('/', 'StreamController@index')
    ->name('stream.index');
Route::get('/record', 'StreamController@record')
    ->name('stream.record');

Route::get('/stream', 'StreamController@pull')
    ->name('stream.pull');
Route::post('/stream', 'StreamController@push')
    ->name('stream.push');
