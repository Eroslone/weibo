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
//用户激活验证邮件
Route::get('signup/confirm/{token}', 'UsersController@confirmEmail')->name('confirm_email');
//修改密码
Route::get('password/reset','PasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email','PasswordController@sendResetLinkEmail')->name('password.email');

Route::get('password/reset/{token}',  'PasswordController@showResetForm')->name('password.reset');
Route::post('password/reset',  'PasswordController@reset')->name('password.update');
//微博创建和删除
Route::resource('statuses', 'StatusesController', ['only' => ['store', 'destroy']]);