<?php declare(strict_types=1);

namespace Models;

use Core\ActiveRecord;
use Gabrola\EmailNormalizer\EmailNormalizer;
use Gabrola\EmailNormalizer\EmailRules;

class User extends ActiveRecord {
    protected static string $table = 'users';
    protected static array $columns = ['id', 'name','email','password','role','token','isConfirmed'];
    protected array $roles = ['admin', 'user'];

    public string $name;
    public string $email;
    public string $password;
    public string $role;
    public ?string $token;
    public int $isConfirmed;

    public function name(string $name) : self {
        $errHead = "Nombre";

        $name = trim($name);
        if($name === '') $this->setError($errHead, "El nombre es obligatorio");
        if(preg_match('/[a-zA-Z0-9_]/', $name) === 0) $this->setError($errHead, "El nombre tiene un caracter inválido");
        
        if(empty($this->getErrorsByHead($errHead))) $this->name = $name;
        return $this;
    }

    public function email(string $email) : self {
        if($email === '') {
            $this->setError("Correo", "El correo es obligatorio");
            return $this;
        }
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $email = $this->normalizeEmail($email);
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);

        $this->email = $email;
        return $this;
    }

    public function password(string $password) : self {
        $errHead = "Contraseña";
        $password = trim($password);
        if(preg_match('/[A-Z]/', $password) === 0) $this->setError($errHead, "La contraseña debe contener al menos una mayuscula");
        if(preg_match('/[^a-zA-Z0-9_]/', $password) === 0) $this->setError($errHead, "La contraseña debe contener al menos un caracter especial");
        if(strlen($password) < 8) $this->setError($errHead, "La contraseña debe contener minimo 8 caracteres");
        
        if(empty($this->getErrorsByHead($errHead))) {
            $hash = $this->passHash($password);
            if(strlen($hash) !== 60) throw new \Error("Bad password hashing in User Model");
            $this->password = $hash;
        }
        return $this;
    }

    public function role(string $role) : self {
        $errHead = "Rol";
        $role = trim($role);
        if($role === '') $this->setError($errHead, "Un rol es obligatorio");
        if(!in_array($role,$this->roles)) $this->setError($errHead,"Rol no válido");
        if(empty($this->getErrorsByHead($errHead))) $this->role = $role;
        return $this;
    }

    public function isConfirmed(bool $isConfirmed = true) : self {
        $this->isConfirmed = $isConfirmed ? 1 : 0;
        return $this;
    }

    public function token() : self {
        $token = uniqid((string) rand(), true);
        if(strlen($token) < 30) throw new \Error("Bad token generated in User Model");
        $this->token = $token;
        return $this;
    }

    public function validate() {
        if($this->name === '') $this->setError('Campo vacío', "El nombre no puede ir vacío");
        if($this->email === '') $this->setError('Campo vacío', "El correo es obligatorio");
        if($this->password === '') $this->setError('Campo vacío', "La contraseña es obligatoria");
        if($this->role === '') $this->setError('Campo vacío', "Debe asignar un rol al usuario");
    }

    private function normalizeEmail(string $email) : string {
        $emailNmlzr = new EmailNormalizer(new EmailRules());
        $normalizedEmail = $emailNmlzr->normalize($email);
        return $normalizedEmail;
    }

    private function passHash(string $passPlain) : string {
        $hash = password_hash($passPlain, PASSWORD_BCRYPT);
        $this->password = $hash;
        return $this->password;
    }

    public function passVerify(string $password) : bool {
        $result = password_verify($password, $this->password);
        return $result;
    }
}