<?php

Route::pattern('id', '[0-9]+');

Route::get('/',
    function () {
        return View::make('hello');
    }
);

// все пост-запросы прогоняем через csrf фильтр
Route::when('/admin/*', 'csrf', array('post'));

// also see /app/start/global.php
App::missing(function() {
    return Response::view('main.404', array(), 404);
});

/** common routes */
Route::group(
    array('before' => ['auth', 'can:manage_dashboard']),
    function () {
        Route::post('/admin/sort',  ['as' => 'sorter',      'uses' => '\Rutorika\Sortable\SortableController@sort']);
        Route::post('upload',       ['as' => 'uploader',    'uses' => '\Rutorika\Dashboard\Uploader\UploadController@handle']);
    }
);


Route::group(
    ['before' => ['auth', 'can:manage_dashboard'], 'prefix' => 'admin', 'namespace' => 'App\Controllers\Admin'],
    function () {
        Route::get('/', ['as' => '.main', 'uses' => 'MainController@index']);

        $crudRoutes = [
            ['name' => 'user'],
            ['name' => 'role', 'entityNameSpace' => 'Rico\Auth\\'],
        ];

        generate_crud_routes($crudRoutes);
    }
);

// confide routes
Route::group(
    ['namespace' => 'Rico\Auth'],
    function () {
//        Route::get('users/create', 'UsersController@create');
//        Route::post('users', 'UsersController@store');
        Route::get('auth/login', ['as' => 'login', 'uses' => 'UsersController@login']);
        Route::post('auth/login', ['as' => 'do-login', 'uses' => 'UsersController@doLogin']);
//        Route::get('users/confirm/{code}', 'UsersController@confirm');
//        Route::get('users/forgot_password', 'UsersController@forgotPassword');
//        Route::post('users/forgot_password', 'UsersController@doForgotPassword');
//        Route::get('users/reset_password/{token}', 'UsersController@resetPassword');
//        Route::post('users/reset_password', 'UsersController@doResetPassword');
        Route::get('auth/logout', ['as' => 'logout', 'uses' => 'UsersController@logout']);
    }
);

