<?php
define("PROJECT_ROOT", dirname(__DIR__, 1));

require PROJECT_ROOT . "/vendor/autoload.php";

use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

require PROJECT_ROOT . "/helpers/functions.php";
require __DIR__ . "/database.php";

use Core\ActiveRecord;
ActiveRecord::setDB($db);

use Core\Routing\Router;
$router = new Router();

use Core\Rendering\RenderEngine;
$rEngine = new RenderEngine("views/layout", "views");