<?php
define("PROJECT_ROOT", dirname(__DIR__, 1));
require PROJECT_ROOT . "/vendor/autoload.php";

use Dotenv\Dotenv;
use Core\ActiveRecord;
use Controllers\IndexController;
use Core\Routing\Router;
use Core\Rendering\RenderEngine;

// Environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
require PROJECT_ROOT . "/helpers/functions.php";

// Set Database
require __DIR__ . "/database.php";

// Set Active Record
ActiveRecord::setDB($db);
// Set Router
$router = new Router();
// Set Render Engine
$rEngine = new RenderEngine("views/layout", "views");

// Services
$indexController = new IndexController($rEngine);