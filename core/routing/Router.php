<?php

namespace MVC;

class Router {
    private array $routes = [];

    public function get(string $path, callable|array $handler) { $this->addRoute('GET', $path, $handler); }
    public function post(string $path, callable|array $handler) { $this->addRoute('POST', $path, $handler); }
    public function put(string $path, callable|array $handler) { $this->addRoute('PUT', $path, $handler); }
    public function pathc(string $path, callable|array $handler) { $this->addRoute('PATCH', $path, $handler); }
    public function delete(string $path, callable|array $handler) { $this->addRoute('DELETE', $path, $handler); }

    private function addRoute(string $method, string $path, callable|array $handler) : void {
        $path = $this->normalizePath($path);
        $this->routes[$method][] = [
            'path' => $path,
            'handler' => $handler
        ];
    }
    private function normalizePath(string $path): string {
        if ($path !== '/') $path = rtrim($path, '/');
        return $path ?: '/';
    }
}