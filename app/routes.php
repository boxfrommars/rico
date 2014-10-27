<?php

Route::get('/',
    function () {
        return View::make('hello');
    }
);

Route::group(
    ['before' => ['auth', 'can:manage_dashboard'], 'prefix' => 'admin', 'namespace' => 'App\Controllers\Admin'],
    function () {
        Route::get('/', ['as' => '.main', 'uses' => 'MainController@index']);

        $crudRoutes = [];

        foreach ($crudRoutes as $route) {
            $name = $route['name'];
            $entity = camel_case($name);
            $controller = studly_case($name) . 'Controller';
            $prefix = array_key_exists('prefix', $route) ? $route['prefix'] : '';
            $entityClassName = studly_case($name);

            Route::model($entity, "Neo\\Entities\\{$entityClassName}");

            Route::get( "{$name}/{id}",                 ["as" => ".{$name}.view",      "uses" => "{$controller}@view"]);
            Route::get( "{$prefix}{$name}",             ["as" => ".{$name}.index",     "uses" => "{$controller}@index"]);
            Route::get( "{$prefix}{$name}/create",      ["as" => ".{$name}.create",    "uses" => "{$controller}@create"]);
            Route::post("{$name}/store",                ["as" => ".{$name}.store",     "uses" => "{$controller}@store"]);
            Route::post("{$name}/{id}/update",          ["as" => ".{$name}.update",    "uses" => "{$controller}@store"]);
            Route::get( "{$name}/{$entity}/destroy",    ["as" => ".{$name}.destroy",   "uses" => "{$controller}@destroy"]);
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

