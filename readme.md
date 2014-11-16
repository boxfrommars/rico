## Requirements

* PHP >= 5.4
* MCrypt PHP Extension

### Install

    xu@calypso:~$ git clone https://github.com/boxfrommars/rico.git
    xu@calypso:~$ cd rico/

создаём бд (если изменили здесь параметры бд, то меняем их в кофигурации в файле `.env.(local.)php)`

    mysql> CREATE USER 'rico'@'localhost' IDENTIFIED BY 'rico';
    mysql> CREATE DATABASE rico;
    mysql> GRANT ALL PRIVILEGES ON rico . * TO 'rico'@'localhost';
    mysql> FLUSH PRIVILEGES;

настраиваем

    xu@calypso:~$ cp example.env.php .env.php // файл конфигурации текущей машины (если используем окружение local, то: cp example.env.php .env.local.php )

устанавливаем зависимости

    xu@calypso:~$ composer update

Открываем папки для записи сервером (я тут по-простому сделал, можно аккуратнее -- только серверу)

    xu@calypso:~$ chmod a+rw app/storage -R // папка для хранения логов, кеша и всего такого
    xu@calypso:~$ chmod a+rw public/assets/image -R // папка для загрузки пользовательских изображений
    xu@calypso:~$ chmod a+rw public/assets/file -R // папка для загрузки пользовательских файлов

если нет дампа, то

    xu@calypso:~$ php artisan migrate
    xu@calypso:~$ php artisan db:seed // тестовые данные, чтобы обновить миграции и данные: `php artisan migrate:refresh --seed`

создастся тестовый пользователь-администратор с логином/паролем boxfrommars@gmail.com/test, изменить эти данные можно
в файле `app/database/seeds/UserTableSeeder.php`

    // если не настроен апач или нгинкс, то можно запустить сервер (не использовать на продакшене и тесте, только во время разработки)
    xu@calypso:~$ php artisan serve --port 8444 // или любой другой незанятый порт, по умолчанию 8000
    // теперь сайт доступен по адресу http://localhost:8444

Для установки тестового окружения (`local`), добавьте в массив `$env` файла `bootstrap/start.php` имя своего компьютера (определяется командой `hostname`)
В тестовом окружении будет доступна дебагбар-панель

Административная панель доступна по `/admin`


### Overview

Основая идея -- расширение функциональности фреймворка laravel с помощью пакетов, при этом не сокращающее базовую функциональность фреймворка

### Использованные пакеты
 
* Zizaco Confide https://github.com/Zizaco/confide authentication solution for Laravel made to cut repetitive work involving the management of users
* Zizaco Entrust https://github.com/Zizaco/entrust provides a flexible way to add Role-based Permissions
* Dashboard https://github.com/boxfrommars/rutorika-dashboard пакет реализующий базовый функционал административной панели и CRUD
* Intervention Image https://github.com/Intervention/image библиотека для работы с изображениями

пакеты для разработчика:

* barryvdh/laravel-ide-helper хелпер для автодополнения фасадов
* barryvdh/laravel-debugbar дебагбар панель
* fzaninotto/faker предоставляет фейковые данные

Все пакеты рпедоставляются с базовой конфигурацией (см. `app/config/packages`)

### Разработка

Контроллеры, модели и прочие классы разрабатываемого приложения добавляются в папку `app/App` (namespace App)

виды кладутся в папку `app/views`, роуты в `app/routes.php`, миграции (если созданы стандартными средствами) в папке `app/database/migrations`

в общем структура приложения в точности повторяет структуру поставляемую с фреймворком Laravel, за исключением использования неймспейса App, для более удобной структуры (см. https://laracasts.com/lessons/where-do-i-put-this) 

#### Добавление сущности и соответствующего раздела админки

Для начала создаётся модель и таблица сущностей, 

например, создайте модель `App/Entities/Human`, наследующую `Rutorika\Dashboard\Entities\Entity`

```php
namespace App\Entities;

use Rutorika\Dashboard\Entities\Entity;

class Human extends Entity
{
    protected $table = 'humans';
    protected $fillable = ['title', 'bio', 'image', 'height', 'location', 'birthdate'];
}
```

в свойстве `$table` укажите соответствующее имя таблицы (можно не указывать, если таблица -- множественное число от имени модели)

в свойстве `$fillable` укажите поля изменяемые из административной панели

для того, чтобы создать миграцию, создающую таблицу в базе данных, необходимо выполнить в консоли

```
php artisan generate:migration add_humans_table
```

в папке `app/database/migrations` при этом создастся шаблон миграции, который необходимо заполнить требуемыми полями. в данном случае:

```
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateHumanTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('humans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('bio');
            $table->string('image');
            $table->integer('height');
            $table->string('location');
            $table->date('birthdate');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('humans');
    }
}
```

Подробнее о миграциях, см. http://laravel.com/docs/4.2/migrations

Теперь создаём админский контроллер, для этого создаём класс `App\Controllers\Admin\HumanController`, который наследуем от `Rutorika\Dashboard\Controllers\CrudController`:

```
namespace App\Controllers\Admin;

use Rutorika\Dashboard\Controllers\CrudController;

class HumanController extends CrudController {

    protected $_entity = '\App\Entities\Human';
    protected $_name = 'human';
    protected $_rules = [
        'title' => 'required',
        'bio'    => 'required',
        'height' => 'numeric',
    ];
    protected $_afterSaveRoute = 'self'; // 'self' (default) | 'index' | 'parent'
    protected $_afterDeleteRoute = 'index'; // 'parent' (default) | 'index'
}
```
в котором прописываем свойства-параметры:

* `$_entity` строка, в которой указывается класс сущности
* `$_name` имя сущности (используется в пути к видам, в названии параметров передаваемых в вид)
* `$_parentName` имя родительской сущности (если есть)
* `$_rules` рулзы для валидации (общие, используется, если не указаны `$_createRules` и/или `$_updateRules`)
* `$_createRules` рулзы для валидации во время создания
* `$_updateRules` рулзы для валидации во время апдейта
* `$_afterSaveRoute` тип роута на который редиректим после создания (возможные значения: 'self' (по умолчанию) | 'index' | 'parent')
* `$_afterDeleteRoute` тип роута на который редиректим после удаления (возможные значения: 'parent' (по умолчанию) | 'index')

Теперь добавляем виды для сущности, по соглашению, они должен располагаться в папке `app\views\dashboard\{name}`, в нашем случае `app\views\dashboard\human`.
Необходимо добавить как минимум два файла: `index.blade.php` и `create.blade.php`. 
* в `index` файле доступна переменная `$humans` -- множетсенное число от имени сущности
* в `create` файл досутпна перемення `$human`
 
```blade
@section('content')

<ol class="breadcrumb">
    <li class="active">Гуманоиды</li>
</ol>

<div class="row">
    <div class="col-md-9">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Имя</th>
                <th>Дата рождения</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($humans as $human)
                <tr>
                    <td><a href="{{ route('.human.view', $human->id) }}">{{ $human->title }}</a></td>
                    <td><a href="{{ route('.human.view', $human->id) }}">{{ $human->birthdate }}</a></td>
                    <td class="grid-actions">
                        {{ grid_link('human', 'view', $human->id) }}
                        {{ grid_link('human', 'destroy', $human->id) }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <p><a href="{{ route('.human.create') }}" class="btn btn-primary" role="button">
            <span class="glyphicon glyphicon-plus"></span>
            Добавить
        </a></p>

    </div>
    <div class="col-md-3"></div>
</div>
@endsection
```

```
@section('content')

<ol class="breadcrumb">
    <li><a href="{{ route('.human.index') }}">Гуманоиды</a></li>
    <li class="active">{{ $human->id ? $human->title : 'Добавление гуманоида' }}</li>
</ol>

<div class="row">
    <div class="col-md-9">

        <?php $url = $human->id ? route('.human.update', $human->id) : route('.human.store'); ?>

        {{ Form::model($human, array('url' => $url, 'class' => 'form-horizontal', 'human' => 'form')) }}
        {{ Form::textField('Название', 'title') }}
        {{ Form::textareaField('Биография', 'bio') }}
        {{ Form::imageField('Изображение', 'image') }}
        {{ Form::numberField('Рост', 'height') }}
        {{ Form::geopointField('Расположение', 'location') }}
        {{ Form::dateField('Дата рождения', 'birthdate') }}
        {{ Form::submitField() }}
        {{ Form::close() }}
    </div>

    <div class="col-md-3"></div>
</div>

@endsection
```

отметим хелперы, использованные здесь (`grid_link`) -- все доступные хелперы можно посмотреть https://github.com/boxfrommars/rutorika-dashboard/blob/master/src/Rutorika/Dashboard/Support/helpers.php, 
а стандартные хелперы laravel (`route` и т.д.) http://laravel.com/docs/4.2/helpers

Все формы и поля идут с поддержкой `twitter bootstrap`, доступные поля (см. https://github.com/boxfrommars/rutorika-dashboard/blob/master/src/Rutorika/Dashboard/HTML/FormBuilder.php):

* `textField` порстое текстовое поле
* `textareaField` текстареа
* `colorField` выбор цвета
* `numberField` числовое поле
* `selectField` селект
* `checkboxField` чекбокс
* `geopointField` точка на карте (используются яндекс карты)
* `imageField` изображение
* `fileField` файл
* `dateField` дата
* `datetimeField` дата и время
* `timeField` время
* `submitField` кнопка сабмита

осталось добавить имя ресурса в круд-роуты (файл `app/routes.php`):

```php
$crudRoutes = [
//   ...
    ['name' => 'human', 'entityNameSpace' => 'App\Entities\\'],
];
```
список теперь доступен по `/admin/human/index` и т.д.

для получения и отображения данных в приложении теперь можно использовать стандартные eloquent запрсы (см. http://laravel.com/docs/4.2/eloquent)


#### Добавление REST-контроллера

Воспользуемся той же сущностью Human и добавим контроллер и роуты для доступа к ней по REST

Для этого создадим контрллер и унаследуем его от `Rutorika\Dashboard\Controllers\RestController`
 
```php
namespace App\Controllers\ExampleApp;

use Rutorika\Dashboard\Controllers\RestController;

class HumanController extends RestController {

    protected $_entity = '\App\Entities\Human';
    protected $_name = 'human';
    protected $_rules = [
        'title' => 'required',
        'bio'    => 'required',
        'height' => 'numeric',
    ];
}
```
Настройки все те же, что и для CRUD-контрллера, за исключением свойств, связанных с видами и редиректами (они, естественно, отсутствуют)

* `$_entity` строка, в которой указывается класс сущности
* `$_name` имя сущности
* `$_rules` рулзы для валидации (общие, используется, если не указаны `$_createRules` и/или `$_updateRules`)
* `$_createRules` рулзы для валидации во время создания
* `$_updateRules` рулзы для валидации во время апдейта

теперь необходимо добавить роуты в файл `app/routes.php`:

```
Route::get(   'humans',       ['as' => 'api.human.index',     'uses' => 'App\Controllers\ExampleApp\HumanController@index']);
Route::post(  'humans',       ['as' => 'api.human.store',     'uses' => 'App\Controllers\ExampleApp\HumanController@store']);
Route::get(   'humans/{id}',  ['as' => 'api.human.view',      'uses' => 'App\Controllers\ExampleApp\HumanController@view']);
Route::put(   'humans/{id}',  ['as' => 'api.human.update',    'uses' => 'App\Controllers\ExampleApp\HumanController@store']);
Route::delete('humans/{id}',  ['as' => 'api.human.destroy',   'uses' => 'App\Controllers\ExampleApp\HumanController@destroy']);
```

или воспользоваться хелпером `rest_routes`

```
rest_routes()
```
всё, просмотр изменение и создание сущностей доступны по соответствующим урлам


#### Работа с пользователями

Для аутентификации и авторизации используются библиотеки Confide (https://github.com/Zizaco/confide) и Entrust (https://github.com/Zizaco/entrust) соответственно.
структура базы данных для пользователей см `docs/db-schema.dia` или `docs/db-schema.png`

##### разграничение роутов по правам

вы можете использовать фильтр `can:{permission_name}` (наличие разрешения) и `auth` (аутентифицирован ли пользователь), например:

```
Route::post('/admin/sort',  ['before' => ['auth', 'can:manage_dashboard'], 'as' => 'sorter', 'uses' => '\Rutorika\Sortable\SortableController@sort']);
```

в таком случае доступ по данному роуту возможен только для пользователей у которых есть разрешение на `manage_dashboard`

также вы можете использовать стандартные механизмы Entrust, использующие wildcards:

```
// Only users with roles that have the 'manage_posts' permission will
// be able to access any route within admin/post.
Entrust::routeNeedsPermission( 'admin/post*', 'manage_posts' );

// Only owners will have access to routes within admin/advanced
Entrust::routeNeedsRole( 'admin/advanced*', 'Owner' );

// Optionally the second parameter can be an array of permissions or roles.
// User would need to match all roles or permissions for that route.
Entrust::routeNeedsPermission( 'admin/post*', array('manage_posts','manage_comments') );

Entrust::routeNeedsRole( 'admin/advanced*', array('Owner','Writer') );
```

Подробнее см. https://github.com/Zizaco/entrust

##### проверка в коде

В коде вы можете использовать следующие порверки

```
$user->hasRole("owner");    // false проверяем роли
$user->hasRole("admin");    // true
$user->can("manage_posts"); // true проверяем разрешение 
$user->can("manage_users"); // false
```

##### аутентификация

контроллер отвечающий за аутентификацию -- `\Rico\Auth\UsersController`, в него частично (!) методы контроллера из пакета Confide (только касающиеся входа/выхода),
для работы с забытыми паролями, регистрацией, подтверждениями и прочим, перенесите нужные методы из `controllers/_UserController.php`, при этом внимательно поверьте конфигурацию 
(`app/config/zizaco/confide/config.php`) и соответствующие виды.




