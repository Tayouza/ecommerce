<?php

namespace App\Model;

use App\Db\Sql;
use App\Model\Model;
use Exception;

class User extends Model
{
    const SESSION = "user";

    public static function login($login, $pass)
    {
        $sql = new Sql();

        $results = $sql->selectQuery("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(
            ":LOGIN" => $login
        ));
        if (count($results) === 0) {
            throw new Exception("Usuário inexistente ou senha inválida");
        }

        $data = $results[0];

        if (password_verify($pass, $data['despassword'])) {
            $user = new User();
            $user->setData($data);
            $_SESSION[User::SESSION] = $user->getValues();
            $valores = $user->getValues();
            return $user;
        } else {
            throw new Exception("Usuário inexistente ou senha inválida");
        }
    }

    public static function verifyLogin($inadmin = true)
    {
        if (
            !isset($_SESSION[User::SESSION])
            ||
            !(int)$_SESSION[User::SESSION]['iduser'] > 0
            ||
            (bool)$_SESSION[User::SESSION]['inadmin'] !== $inadmin
        ) {
            header("Location: /admin/login");
            exit;
        }
    }

    public static function logout()
    {
        unset($_SESSION[User::SESSION]);
    }
}
