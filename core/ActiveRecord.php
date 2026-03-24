<?php

namespace Core;

/**
 * @property int|null $id
 */
class ActiveRecord {
    
    private static \mysqli $db;
    private static $table = '';
    private static $columns = [];

    public static function setDB($database) { self::$db = $database; }

    // Save 
    public function save() : static | bool{
        if(!isset($this->id)) return $this->create();
        else return $this->update();
    }

    // Create
    public function create() : static | bool {
        $table = static::$table;
        $attrs = $this->getAtributes();
        $columns = implode(", ", array_keys($attrs));
        $values = implode("', '", array_values($attrs));

        $query = "INSERT INTO $table ($columns) VALUES ('$values')";
        $result = self::$db->query($query);
        
        if(!$result) return false;
        $this->id = self::$db->insert_id;

        return $this;
    }
    
    // Read
    public static function all() : array {
        $table = static::$table;
        $query = "SELECT * FROM $table";
        $result = self::querySQL($query);

        return $result;
    }

    public static function find(int $id) : static | null {
        $id = filter_var((int) $id, FILTER_VALIDATE_INT);
        $table = static::$table;
        $query = "SELECT * FROM $table WHERE id = $id LIMIT 1";
        $result = self::querySQL($query);

        return empty($result) ? null : $result;
    }

    public static function get(int $lim) : array | static {
        $table = static::$table;
        $lim = filter_var((int) $lim, FILTER_VALIDATE_INT);
        $lim = ($lim > 0) ? $lim : 0;
        $query = "SELECT * FROM $table LIMIT $lim";
        $result = self::querySQL($query);

        return $result;
    }

    public static function where(string $column, mixed $value) : null | static {
        $table = static::$table;
        $value = self::$db->escape_string($value);
        if (!in_array($column, static::$columns)) return null;
        $query = "SELECT * FROM $table WHERE $column = '$value'";
        $result = self::querySQL($query);

        return $result instanceof static ? $result : null;
    }

    public static function findBy(string $column, mixed $value) : null | static {
        $table = static::$table;
        $value = self::$db->escape_string($value);
        if (!in_array($column, static::$columns)) return null;
        $query = "SELECT * FROM $table WHERE $column = '$value' LIMIT 1";
        $result = self::querySQL($query);

        return $result instanceof static ? $result : null;
    }

    public static function count() : int {
        $table = static::$table;
        $query = "SELECT COUNT(*) as total FROM $table";
        $result = self::$db->query($query);

        return $result->fetch_assoc()['total'];
    }
    // Update
    public function sync($data) : static {
        foreach ($data as $key => $val) {
            if(!in_array($key, static::$columns)) continue;
            $this->$key = self::$db->escape_string($val);
        }

        return $this;
    }

    public function update() : static | bool {
        $table = static::$table;
        $id = filter_var((int) $this->id, FILTER_VALIDATE_INT);

        $values = [];
        foreach ($this->getAtributes() as $key => $val) {
            $values[] = "$key = '$val'";
        }
        $values = implode(", ", $values);
        
        $query = "UPDATE $table SET $values WHERE id = '$id' LIMIT 1";
        $result = self::$db->query($query);

        return $result ? $this : $result;
    }   
    // Delete
    public function delete() : static | bool {
        $table = static::$table;
        $id = filter_var((int) $this->id, FILTER_VALIDATE_INT);
        $query = "DELETE FROM $table WHERE id = '$id' LIMIT 1";
        $result = self::$db->query($query);

        if(!$result) return false;
        unset($this->id);

        return $this;
    }

    private function getAtributes() : array {
        $attrs = [];
        foreach(static::$columns as $column) {
            if(!property_exists($this, $column)) continue;
            if($column === "id") continue;
            $attrs[$column] = $this->$column; 
        }
        
        return $attrs;
    }

    private static function objectify(array $array) : static {
        $object = new static;
        foreach ($array as $key => $val) {
            $object->$key = self::$db->escape_string($val);
        }

        return $object;
    }

    private static function querySQL(string $query) : array | static{
        $results = [];
        $result = self::$db->query($query);
        if (!$result) return [];
        while ($res = $result->fetch_assoc()) {
            $results[] = self::objectify($res);
        }

        if(empty($results)) return [];
        return (count($results) > 1) ? $results : $results[0];
    }
}