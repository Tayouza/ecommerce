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

        $results = $sql->selectQuery("  SELECT * FROM tb_users users
                                        INNER JOIN tb_persons persons
                                        USING(idperson)
                                        WHERE deslogin = :LOGIN", array(
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
        } else {
            throw new Exception("Usuário inexistente ou senha inválida");
        }
    }

    private static function verifyUser()
    {
        $sql = new Sql();

        $results = $sql->selectQuery("  SELECT * FROM tb_users users
                                        INNER JOIN tb_persons persons
                                        USING(idperson)
                                        WHERE iduser = :IDUSER", array(
            ":IDUSER" => $_SESSION[User::SESSION]['iduser']
        ));
        
        $user = new User();
        $user->setData($results[0]);
        $_SESSION[User::SESSION] = $user->getValues();
        
    }

    public static function verifyLogin()
    {
        self::verifyUser();
        if (
            !isset($_SESSION[User::SESSION])
            ||
            !(int)$_SESSION[User::SESSION]['iduser'] > 0
            ||
            (bool)$_SESSION[User::SESSION]['inadmin'] !== true
        ) {
            header("Location: /admin/login");
            exit;
        }
    }

    public static function logout()
    {
        unset($_SESSION[User::SESSION]);
    }

    public static function listAll()
    {
        $sql = new Sql();

        return $sql->selectQuery(" SELECT * FROM tb_users users
                            INNER JOIN tb_persons persons
                            USING(idperson)
                            ORDER BY persons.desperson");
    }

    public function save()
    {
        $sql = new Sql();
        /*
        pdesperson VARCHAR(64),
        pdeslogin VARCHAR(64),
        pdespassword VARCHAR(256),
        pdesemail VARCHAR(128),
        pdesnrphone BIGINT,
        pdesinadmin TINYINT,
        */
        $results = $sql->selectQuery("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
            ":desperson" => $this->getdesperson(),
            ":deslogin" => $this->getdeslogin(),
            ":despassword" => $this->getdespassword(),
            ":desemail" => $this->getdesemail(),
            ":nrphone" => $this->getnrphone(),
            ":inadmin" => $this->getinadmin()
        ));

        $this->setData($results[0]);
    }

    public function get($iduser)
    {
        $sql = new Sql();

        $results = $sql->selectQuery("  SELECT * FROM tb_users users 
                                        INNER JOIN tb_persons persons
                                        USING(idperson) 
                                        WHERE users.iduser = :iduser",
            array(
                ":iduser" => $iduser
            )
        );

        $this->setData($results[0]);
    }

    public function update()
    {
        $sql = new Sql();
        /*
        pdesperson VARCHAR(64),
        pdeslogin VARCHAR(64),
        pdespassword VARCHAR(256),
        pdesemail VARCHAR(128),
        pdesnrphone BIGINT,
        pdesinadmin TINYINT,
        */
        $results = $sql->selectQuery("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
            ":iduser" => $this->getiduser(),
            ":desperson" => $this->getdesperson(),
            ":deslogin" => $this->getdeslogin(),
            ":despassword" => $this->getdespassword(),
            ":desemail" => $this->getdesemail(),
            ":nrphone" => $this->getnrphone(),
            ":inadmin" => $this->getinadmin()
        ));

        $this->setData($results[0]);
    }

    public function delete()
    {
        $sql = new Sql();

        $sql->runQuery("Call sp_users_delete(:iduser)", array(
            ":iduser"=>$this->getiduser()
        ));
    }
}
