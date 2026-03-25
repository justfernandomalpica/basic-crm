<?php

namespace Controllers;

use Core\Rendering\View;

class IndexController {
    public static function index() {
        $view = new View("public/mainpage");
        $view->data(["title"=>"Title", "content"=>"2","Dato"=>"Algun dato cualquiera"]);
    }
}