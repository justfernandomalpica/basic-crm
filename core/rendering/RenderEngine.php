<?php declare(strict_types=1);

namespace Core\Rendering;

class RenderEngine {
    private string $layoutPath = '';
    private string $viewPath = '';

    public function __construct(string $layoutPath, string $viewPath) {
        $this->layoutPath = $this->resolveFolder($layoutPath);
        $this->viewPath = $this->resolveFolder($viewPath);
    }

    public function render(string $layout, View $view) {
        // Comprobar si la vista y layout a renderizar existen en el path
        $vPath = $this->buildViewDir($view);
        $lPath = $this->buildLayoutDir($layout);

        // Convertir elementos de data a variables individuales
        $data = $view->getData();
        if($data !== []) extract($data, EXTR_PREFIX_ALL, "view_");

        // Guardar en memoria la vista
        ob_start();
        include $vPath;
        $content = ob_get_clean();

        // Renderizar el layout incluyendo la vista
        include $lPath;
    }

    private function resolveFolder(string $path) : string {
        $path = trim($path);
        if($path === "") throw new \Exception("Error on setting RenderEngine folder: Path cannot be empty");
        if(str_starts_with($path, DIRECTORY_SEPARATOR)) throw new \Exception("Error on setting RenderEngine folder: Path does not must start with ' ". DIRECTORY_SEPARATOR . " '.");
        if(str_ends_with($path,DIRECTORY_SEPARATOR)) throw new \Exception("Error on setting RenderEngine folder: Path does not must end with ' ". DIRECTORY_SEPARATOR . " '.");
        $path = realpath(PROJECT_ROOT . DIRECTORY_SEPARATOR . $path);
        if($path === false) throw new \Exception("Error on setting RenderEngine folder: No folder resolved with setted path [{$path}]");
        return $path;
    }

    private function buildViewDir(View $view) : string {
        $dir = $this->viewPath . DIRECTORY_SEPARATOR . $view->getPath() . ".php";
        if(!is_file($dir)) throw new \Exception("View don't exist in current views folder");
        return $dir;
    }

    private function buildLayoutDir(string $layout) : string {
        $dir = $this->layoutPath . DIRECTORY_SEPARATOR . $layout . ".php";
        if(!is_file($dir)) throw new \Exception("Layout don't exist in current layouts folder");
        return $dir;
    }

}