<?php

use Controllers\IndexController;

require __DIR__ . "/../config/app.php";

$router->get("/", [IndexController::class,"index"])->name("index");

$router->dispatch();