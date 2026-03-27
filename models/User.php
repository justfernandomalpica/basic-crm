<?php declare(strict_types=1);

namespace Models;

use Core\ActiveRecord;

class User extends ActiveRecord {
    protected static $table = 'users';
    protected static $columns = ['id', 'name','email','password','role','token','isConfirmed'];

    public $name;
    public $email;
    public $password;
    public $role;
    public $token;
    public $isConfirmed;

    public function __construct(array $args) {
        $this->validate($args);
        $this->name = $args['name']; 
        $this->email = $args['email']; 
        $this->password = $args['password']; 
        $this->role = $args['role']; 
        $this->token = $args['token']; 
        $this->isConfirmed = $args['isConfirmed']; 
    }

    private function validate(array $args) {
        $baseErrorMsg = "Error on User Model: ";
        if(empty($args)) throw new \InvalidArgumentException($baseErrorMsg."Class cannot be instanced with empty arguments array");
        foreach($args as $key => $value){
            if(!in_array($key, self::$columns)) throw new \InvalidArgumentException($baseErrorMsg."'{$key}' is not a column in SQL table");
            if($value === '') throw new \InvalidArgumentException($baseErrorMsg."'{$key} cannot be empty");
        }
    }
}