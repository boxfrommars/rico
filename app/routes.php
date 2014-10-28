<?php

Route::pattern('id', '[0-9]+');

Route::get('/',
    function () {
        return View::make('hello');
    }
);

// все пост-запросы прогоняем через csrf фильтр
Route::when('*', 'csrf', array('post'));

// also see /app/start/global.php
App::missing(function() {
    return Response::view('main.404', array(), 404);
});

/** общие админские роуты */
Route::group(
    array('before' => ['auth', 'can:manage_dashboard'], 'prefix' => 'admin'),
    function () {
        Route::post('sort', '\Rutorika\Sortable\SortableController@sort');

        Route::post('image/upload', array('as' => '.image.upload',  'uses' => 'App\Controllers\Admin\UploadController@image'));
        Route::post('file/upload',  array('as' => '.file.upload',   'uses' => 'App\Controllers\Admin\UploadController@file'));
    }
);

Route::group(
    ['before' => ['auth', 'can:manage_dashboard'], 'prefix' => 'admin', 'namespace' => 'App\Controllers\Admin'],
    function () {
        Route::get('/', ['as' => '.main', 'uses' => 'MainController@index']);

        $entityDefaultNameSpace = 'App\Entities\\';

        $crudRoutes = [
            ['name' => 'user'],
            ['name' => 'role', 'entityNameSpace' => 'Rico\Auth\\'],

            ['name' => 'human'],
            ['name' => 'pet', 'prefix' => 'human/{human}/'],
        ];

        foreach ($crudRoutes as $route) {
            $name = $route['name'];
            $entity = camel_case($name);
            $controller = studly_case($name) . 'Controller';
            $prefix = array_key_exists('prefix', $route) ? $route['prefix'] : '';
            $entityClassName = studly_case($name);

            $entityNameSpace = isset($route['entityNameSpace']) ? $route['entityNameSpace'] : $entityDefaultNameSpace;
            Route::model($entity, $entityNameSpace . "{$entityClassName}");

            Route::get( "{$name}/{id}",                 ["as" => ".{$name}.view",      "uses" => "{$controller}@view"]);
            Route::get( "{$prefix}{$name}",             ["as" => ".{$name}.index",     "uses" => "{$controller}@index"]);
            Route::get( "{$prefix}{$name}/create",      ["as" => ".{$name}.create",    "uses" => "{$controller}@create"]);
            Route::post("{$name}/store",                ["as" => ".{$name}.store",     "uses" => "{$controller}@store"]);
            Route::post("{$name}/{id}/update",          ["as" => ".{$name}.update",    "uses" => "{$controller}@store"]);
            Route::get( "{$name}/{{$entity}}/destroy",  ["as" => ".{$name}.destroy",   "uses" => "{$controller}@destroy"]);
        }
    }
);

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

