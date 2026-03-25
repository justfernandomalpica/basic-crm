<?php declare(strict_types=1);

namespace Core\Rendering;

class RenderEngine {
    private static string $layoutPath = '';
    private static string $viewPath = '';

    public static function render(string $layout, View $view) {
        // Comprobar si los paths fueron setteados;
        if(self::$viewPath === "") throw new \Exception("View path must be declared before rendering");
        if(self::$layoutPath === "") throw new \Exception("Layout path must be declared before rendering");

        // Comprobar si la vista y layout a renderizar existen en el path
        $vPath = self::buildViewDir($view);
        $lPath = self::buildLayoutDir($layout);

        // Convertir elementos de data a variables individuales
        $data = $view->getData();
        if($data !== []){
            foreach ($data as $key => $value) {
                $$key = $value;
            }
        }

        // Guardar en memoria la vista
        ob_start();
        include $vPath;
        $content = ob_get_clean();

        // Renderizar el layout incluyendo la vista
        include $lPath;
    }

    public static function setLayoutsFolder(string $path) : void {
        $path = trim($path);
        if($path === "") throw new \Exception("Layout path cannot be empty");
        $path = __DIR__ . "/../../" . $path;
        self::$layoutPath = $path;
    }

    public static function setViewsFolder(string $path) : void {
        $path = trim($path);
        if($path === "") throw new \Exception("Layout path cannot be empty");
        $path = __DIR__ . "/../../" . $path;
        self::$viewPath = $path;
    }

    private static function buildViewDir(View $view) : string {
        $dir = self::$viewPath . "/" . $view->getPath() . ".php";
        if(!is_file($dir)) throw new \Exception("View don't exist in current views folder");
        return $dir;
    }

    private static function buildLayoutDir(string $layout) : string {
        $dir = self::$layoutPath . "/" . $layout . ".php";
        if(!is_file($dir)) throw new \Exception("Layout don't exist in current layouts folder");
        return $dir;
    }

}