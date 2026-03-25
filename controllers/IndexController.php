<?php

namespace Controllers;

use Core\Rendering\RenderEngine;
use Core\Rendering\View;

class IndexController {
    public static function index() {
        $view = new View("public/mainpage");
        $view->data([
            "title" => "Index"
        ]);
        RenderEngine::render("master", $view);
    }
}