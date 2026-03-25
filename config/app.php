<?php

require __DIR__ . "/../vendor/autoload.php";

use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

require __DIR__ . "/../helpers/functions.php";
require __DIR__ . "/database.php";

use Core\ActiveRecord;
ActiveRecord::setDB($db);

use Core\Routing\Router;
$router = new Router();

use Core\Rendering\RenderEngine;
RenderEngine::setLayoutsFolder("views/layout");
RenderEngine::setViewsFolder("views");