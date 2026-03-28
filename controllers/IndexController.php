<?php

namespace Controllers;

use Core\Rendering\RenderEngine;
use Core\Rendering\View;
use Models\User;

class IndexController {
    private RenderEngine $rEngine;

    public function __construct(RenderEngine $rEngine) {
        $this->rEngine = $rEngine;
    }

    public function index(){
        $view = new View("public/mainpage");
        $view->data(["title"=>"Pagina principal", "content"=>"2","Dato"=>"Algun dato cualquiera"]);
        $this->rEngine->render("master", $view);
    }
}