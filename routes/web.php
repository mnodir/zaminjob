<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'AdminController@index')->name('home');
Route::get('/admin', 'AdminController@index')->name('admin');
