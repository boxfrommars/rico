<?php

namespace Rico\Dashboard\Controllers;

/**
 * Class BaseController
 *
 * @package Rico\Dashboard\Controllers\Admin
 */
class BaseController extends \Controller
{
    public $layoutName = 'admin.layout';

    protected function setupLayout()
    {
        $this->layout = \View::make($this->layoutName);
    }

    protected function _populateView($viewName, $viewParams)
    {
        $this->layout->nest('content', $viewName, $viewParams);
    }
}