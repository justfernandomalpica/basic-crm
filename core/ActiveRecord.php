<?php declare(strict_types=1);

namespace Core;

class ActiveRecord {
    
    private static ?\mysqli $db = null;
    protected static string $table = '';
    protected static array $columns = [];
    protected ?int $id = null;
    protected array $errors = [];

    // Entablish database instance
    public static function setDB(\mysqli $database) : void { self::$db = $database; }

    // Save 
    public function save() : static | bool{
        if(is_null($this->id)) return $this->create();
        else return $this->update();
    }

    // Create
    private function create() : static | bool {
        self::initialValidation();
        $table = static::$table;
        $attrs = $this->getAttributes();
        if(empty($attrs)) return false;

        $columns = implode(", ", array_keys($attrs));
        $params = array_values($attrs);

        $values = self::buildPlaceholdersChain($params);
        $query = "INSERT INTO $table ($columns) VALUES ($values)";
        $result = self::$db->execute_query($query, $params);

        if(!$result) return false;
        $this->id = self::$db->insert_id;

        return $this;
    }
    
    // Read
    public static function all() : array {
        self::initialValidation();
        $table = static::$table;
        $query = "SELECT * FROM $table";

        return self::fetchAll($query);
    }

    public static function find(int $id) : ?static {
        self::initialValidation();
        if($id <= 0) return null;
        $table = static::$table;
        $query = "SELECT * FROM $table WHERE id = ? LIMIT 1";

        return self::fetch($query, [$id]);
    }

    public static function get(int $lim) : array {
        self::initialValidation();
        $table = static::$table;
        if($lim <= 0) return [];
        $query = "SELECT * FROM $table LIMIT ?";
        $result = self::fetchAll($query,[$lim]);

        return $result;
    }

    public static function where(string $column, mixed $value) : array {
        self::initialValidation();
        $table = static::$table;
        if (!in_array($column, static::$columns,true)) return [];
        $query = "SELECT * FROM $table WHERE $column = ?";
        $result = self::fetchAll($query, [$value]);

        return $result;
    }

    public static function findBy(string $column, mixed $value) : ?static {
        self::initialValidation();
        $table = static::$table;
        if (!in_array($column, static::$columns,true)) return null;
        $query = "SELECT * FROM $table WHERE $column = ? LIMIT 1";

        return self::fetch($query, [$value]);
    }

    public static function count() : int {
        self::initialValidation();
        $table = static::$table;
        $query = "SELECT COUNT(*) as total FROM $table";
        $result = self::$db->execute_query($query);
        if($result === false) return 0;

        return (int) $result->fetch_assoc()['total'];
    }
    // Update
    public function sync(array $data) : static {
        self::initialValidation();
        foreach ($data as $key => $val) {
            if(!in_array($key, static::$columns)) continue;
            if($key==='id') continue;
            $this->$key = $val;
        }

        return $this;
    }

    public function update() : static | bool {
        self::initialValidation();

        $table = static::$table;
        $id = $this->id;
        $attrs = $this->getAttributes();
        if(is_null($id) || empty($attrs)) return false;
        
        $columns = array_keys($attrs);
        $params = array_values($attrs);

        $values = self::buildPlaceholderPairsChain($columns);
        $query = "UPDATE $table SET $values WHERE id = ? LIMIT 1";
        $params[] = $id; // Esto aqui asegura que id siempre sea el ultimo parámetro antes de ejecutar el query
        $result = self::$db->execute_query($query, $params);

        return $result ? $this : false;
    }

    // Delete
    public function delete() : bool {
        self::initialValidation();
        $table = static::$table;
        $id = $this->id;
        if(is_null($id)) return false;

        $query = "DELETE FROM $table WHERE id = ? LIMIT 1";
        $result = self::$db->execute_query($query, [$id]);
        if ($result === false) return false;

        $this->id = null;
        return $result;
    }

    public function hasErrors() : bool {
        return !empty($this->errors);
    }

    public function getErrors() : array {
        $errors = $this->errors;
        $this->errors = [];
        return $errors;
    }

    protected function setError(string $head, string $body) : void {
        if($head === '' || $body === '') throw new \InvalidArgumentException("A model error cannot be empty");
        $this->errors[$head][] = $body;
    }


    private function getAttributes() : array {
        $attrs = [];
        foreach(static::$columns as $column) {
            if(!property_exists($this, $column)) continue;
            if($column === "id") continue;
            $attrs[$column] = $this->$column; 
        }
        
        return $attrs;
    }

    private static function buildPlaceholdersChain(array $attrs) : string {
        $array = array_fill(0,count($attrs),'?');
        $chain = implode(", ", $array);
        return $chain;
    }

    private static function buildPlaceholderPairsChain(array $columns) : string {
        $array = [];
        foreach($columns as $column) {
            $array[] = $column . " = ?";
        }
        $chain = implode(", ", $array);
        return $chain;
    }

    private static function objectify(array $array) : static {
        $object = new static;
        foreach ($array as $key => $val) {
            if(!property_exists($object,$key)) continue;
            if($key === "id") $val = (int) $val;
            $object->$key = $val;
        }

        return $object;
    }

    private static function fetch(string $query, array $params = []) : ?static {
        $result = self::$db->execute_query($query, $params);
        if($result === false) return null;
        if($result->num_rows === 0) return null;
        $result = self::objectify($result->fetch_assoc());

        return $result;
    }

    private static function fetchAll(string $query, array $params = []) : array {
        $results = [];
        $result = self::$db->execute_query($query, $params);
        if($result === false) return [];
        while ($res = $result->fetch_assoc()) {
            $results[] = self::objectify($res);
        }

        return $results;
    }

    private static function initialValidation() : void {
        $baseErrorMsg = "Active Record error: ";
        if(is_null(self::$db)) throw new \Exception($baseErrorMsg."An instance of \\mysqli must be setted before any operation");
        if(trim(static::$table) === '') throw new \Exception($baseErrorMsg."Table name cannot be empty");
        if(empty(static::$columns)) throw new \Exception($baseErrorMsg."At least one column must be declared");
        foreach(static::$columns as $column) {
            if(!property_exists(static::class, $column)) throw new \Exception($baseErrorMsg."Declared column '{$column}' does not have a matching model property.");
        }
        return;
    }
}