<?php declare(strict_types=1);

namespace Models;

use Core\ActiveRecord;
use Gabrola\EmailNormalizer\EmailNormalizer;
use Gabrola\EmailNormalizer\EmailRules;

class User extends ActiveRecord {
    protected static string $table = 'users';
    protected static array $columns = ['id', 'name','email','password','role','token','isConfirmed'];
    protected array $roles = ['admin', 'user'];

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $role = '';
    public ?string $token = null;
    public int $isConfirmed = 0;

    public function name(string $name) : self {
        $errHead = "Nombre";

        $name = trim($name);
        if($name === '') {
            $this->setError($errHead, "El nombre es obligatorio");
            return $this;
        }
        if(preg_match('[^a-zA-Z_\s]', $name) === 1) $this->setError($errHead, "El nombre no debe contener caracteres inválidos");
        if(empty($this->getErrorsByHead($errHead))) $this->name = $name;
        return $this;
    }

    public function email(string $email) : self {
        if($email === '') {
            $this->setError("Correo", "El correo es obligatorio");
            return $this;
        }
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if($email === false) throw new \Error("Email sanitize gone bad");
        $email = $this->normalizeEmail($email);
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if($email === false) throw new \Error("Email validation gone bad");

        if(empty($this->getErrorsByHead("Correo"))) $this->email = $email;
        return $this;
    }

    public function password(string $password) : self { // Password no necesita cortar con su ejecución al encontrar un error, mientras al final se valide que no hay errores para proceder a hashear y almacenar la contraseña
        $errHead = "Contraseña";
        $password = trim($password);
        if($password === '') {
            $this->setError($errHead,"La contraseña es obligatoria");
            return $this;
        }

        if(preg_match('/[A-Z]/', $password) === 0) $this->setError($errHead, "La contraseña debe contener al menos una mayuscula");
        if(preg_match('/[0-9]/', $password) === 0) $this->setError($errHead,"La contraseña debe contener al menos un numero");
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
        if($role === ''){
            $this->setError($errHead, "Un rol es obligatorio");
            return $this;
        }
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

    public function passVerify(string $password) : bool {
        if(trim($password) === '') return false;
        $result = password_verify($password, $this->password);
        return $result;
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
}