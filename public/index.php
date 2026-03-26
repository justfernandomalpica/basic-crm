<?php

use Controllers\IndexController;

require __DIR__ . "/../config/app.php";

$router->get("/", [$indexController, "index"])->name("index");

$router->dispatch();