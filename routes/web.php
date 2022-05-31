<?php

use Illuminate\Support\Facades\Route;
//静态页面
Route::get('/', 'StaticPagesController@home')->name('home');
Route::get('/help', 'StaticPagesController@help')->name('help');
Route::get('/about', 'StaticPagesController@about')->name('about');
//注册
Route::get('signup','UsersController@create')->name('signup');
Route::resource('users', 'UsersController');
//登录相关
Route::get('login', 'SessionsController@create')->name('login');
Route::post('login', 'SessionsController@store')->name('login');
Route::delete('logout', 'SessionsController@destroy')->name('logout');