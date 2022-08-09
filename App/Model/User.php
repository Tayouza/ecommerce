<?php

namespace App\Model;

use App\Db\Sql;
use App\Model\Model;
use Exception;

class User extends Model
{

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

        if(password_verify($pass, $data['despassword'])){
            $user = new User();
            $user->setIdUser($data['iduser']);
        }else{
            throw new Exception("Usuário inexistente ou senha inválida");
        }
    }

}
