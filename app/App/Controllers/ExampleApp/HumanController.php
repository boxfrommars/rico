<?php
/**
 * @author Dmitry Groza <boxfrommars@gmail.com>
 */

namespace App\Controllers\ExampleApp;

use Rico\Dashboard\Controllers\RestController;

class HumanController extends RestController {

    protected $_entity = '\App\Entities\Human';
    protected $_name = 'human';
    protected $_rules = [
        'title' => 'required',
        'bio'    => 'required',
        'height' => 'numeric',
    ];

    public function test() {
        return \Input::all();
    }
}