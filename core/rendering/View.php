<?php declare(strict_types=1);

namespace Core\Rendering;

use Exception;

class View {
    private string $path;
    private array $data = [];


    public function __construct(string $path) {
        $this->path = $this->validatePath($path);
    }

    public function data(array $args) : self { 
        $vArgs = $this->validateAssocArray($args);
        foreach($vArgs as $key => $value){
            $this->data[$key] = $value;
        }
        return $this;
    }

    public function getPath() : string {
        return $this->path;
    }
    public function getData() : array {
        return $this->data; 
    }

    private function validatePath(string $path) {
        $baseErrorMsg = "Error on setting View Path: ";
        $path = trim($path);
        if($path === "") throw new \Exception($baseErrorMsg."Path cannot be empty");
        if(preg_match("/\.\.\//",$path) === 1) throw new \Exception($baseErrorMsg."Path cannot contain '../' expression");
        if(str_starts_with($path, DIRECTORY_SEPARATOR)) throw new \Exception($baseErrorMsg."Path does not must start with ' ". DIRECTORY_SEPARATOR . " '.");
        if(str_ends_with($path,DIRECTORY_SEPARATOR)) throw new \Exception($baseErrorMsg."Path does not must end with ' ". DIRECTORY_SEPARATOR . " '.");
        if(str_ends_with($path,".php")) trim($path, ".php");
        return $path;
    }

    private function validateAssocArray(array $array) : array {
        $baseErrorMsg = "Error while validating view data format: ";
        if(empty($array)) throw new \Exception($baseErrorMsg."Array cannot be empty.");
        if(array_is_list($array)) throw new \Exception($baseErrorMsg."Array must be associative.");
        foreach (array_keys($array) as $key) if(!is_string($key)) throw new \Exception($baseErrorMsg."All keys from the array must be strings. Key: [".$key."].");
        return $array;
    }
}