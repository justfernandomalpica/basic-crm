<?php

namespace Controllers;

use Core\Rendering\RenderEngine;
use Core\Rendering\View;

class IndexController {
    private RenderEngine $rEngine;

    public function __construct(RenderEngine $rEngine) {
        $this->rEngine = $rEngine;
    }

    public function index() : self {
        $view = new View("public/mainpage");
        $view->data(["title"=>"Title", "content"=>"2","Dato"=>"Algun dato cualquiera"]);
        $this->rEngine->render("master", $view);
        return $this;
    }
}