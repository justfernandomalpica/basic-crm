<?php

namespace Controllers;

class NotFoundController {
    public static function index() : void{
        echo "<h2>Error de direccionamiento</h2>";
        echo "<h1>404</h1>";
        echo "<h3>Página no encontrada</h3>";
        return;
    }
}