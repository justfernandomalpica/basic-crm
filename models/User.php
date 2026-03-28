<?php declare(strict_types=1);

namespace Models;

use Core\ActiveRecord;
use Gabrola\EmailNormalizer\EmailNormalizer;
use Gabrola\EmailNormalizer\EmailRules;

class User extends ActiveRecord {
    protected static string $table = 'users';
    protected static array $columns = ['id', 'name','email','password','role','token','isConfirmed'];

    public string $name;
    public string $email;
    public string $password;
    public string $role;
    public ?string $token;
    public int $isConfirmed;

    public function __construct(array $args =[]) {
        $this->name = $args['name'] ?? ''; 
        $this->email = $args['email'] ?? ''; 
        $this->password = $args['password'] ?? ''; 
        $this->role = $args['role'] ?? ''; 
        $this->token = $args['token'] ?? null; 
        $this->isConfirmed = $args['isConfirmed'] ?? 0; 
    }

    public function validate() {
        if($this->name === '') $this->setError('Campo vacío', "El nombre no puede ir vacío");
        if($this->email === '') $this->setError('Campo vacío', "El correo es obligatorio");
        if($this->password === '') $this->setError('Campo vacío', "La contraseña es obligatoria");
        if($this->role === '') $this->setError('Campo vacío', "Debe asignar un rol al usuario");
    }

    public function normalizeEmail() : string {
        $emailNmlzr = new EmailNormalizer(new EmailRules());
        $normalizedEmail = $emailNmlzr->normalize($this->email);
        return $normalizedEmail;
    }    

    public function passHash(string $passPlain) : string {
        $hash = password_hash($passPlain, PASSWORD_BCRYPT);
        return $hash;
    }

    public function passVerify(string $password) : bool {
        $result = password_verify($password, $this->password);
        return $result;
    }

    public function passwordValidation($password) {
        $password = trim($password);
        if(preg_match('/[A-Z]/', $password) === 1) $this->setError("Contraseña", "La contraseña debe contener al menos una mayuscula");
        if(preg_match('/[^a-zA-Z0-9_]/', $password) === 0) $this->setError("Contraseña", "La contraseña debe contener al menos un caracter especial");
        if(strlen($password) < 8) $this->setError("Contraseña", "La contraseña debe contener minimo 8 caracteres");
    }
}