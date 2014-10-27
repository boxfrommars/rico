<?php
/**
 * @author Dmitry Groza <boxfrommars@gmail.com>
 */

namespace Rico\Notifications;


use Illuminate\Support\Facades\Facade;

class FlashFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'flash';
    }
}
