<?php declare(strict_types=1);

namespace Core\Rendering;

use Exception;

class View {
    private string $path;
    private array $data = [];


    public function __construct(string $path) {
        $this->path = $path;
    }

    public function data(array $args) : self { 
        $this->data = $args;
        return $this;
    }

    public function getPath() : string {
        return $this->path;
    }
    public function getData() : array {
        return $this->data; 
    }
}