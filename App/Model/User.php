<?php

namespace App\Model;

use App\Class\Mailer;
use App\DB\Sql;
use App\Model\Model;
use Exception;

class User extends Model
{
    const SESSION = "user";
    const CIPHER = "AES-128-CBC";
    const SECRET = "TayouzaDev_secret";

    private Sql $sql;

    public function __construct()
    {
        $this->sql = new Sql();
    }

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
        /*
        pdesperson VARCHAR(64),
        pdeslogin VARCHAR(64),
        pdespassword VARCHAR(256),
        pdesemail VARCHAR(128),
        pdesnrphone BIGINT,
        pdesinadmin TINYINT,
        */
        $results = $this->sql->selectQuery("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
            ":desperson" => $this->getdesperson(),
            ":deslogin" => $this->getdeslogin(),
            ":despassword" => password_hash($this->getdespassword(), PASSWORD_BCRYPT, array("cost" => 12)),
            ":desemail" => $this->getdesemail(),
            ":nrphone" => $this->getnrphone(),
            ":inadmin" => $this->getinadmin()
        ));

        $this->setData($results[0]);
    }

    public function get($iduser)
    {
        $results = $this->sql->selectQuery(
            "   SELECT * FROM tb_users users 
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
        /*
        pdesperson VARCHAR(64),
        pdeslogin VARCHAR(64),
        pdespassword VARCHAR(256),
        pdesemail VARCHAR(128),
        pdesnrphone BIGINT,
        pdesinadmin TINYINT,
        */
        $results = $this->sql->selectQuery("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
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
        $this->sql->runQuery("Call sp_users_delete(:iduser)", array(
            ":iduser" => $this->getiduser()
        ));
    }

    public static function getForgot($email)
    {
        $sql = new Sql();

        $results = $sql->selectQuery("   SELECT * FROM tb_persons persons
                                        INNER JOIN tb_users users
                                        USING(idperson)
                                        WHERE persons.desemail = :EMAIL
        ", array(
            ":EMAIL" => $email
        ));

        if (count($results) === 0) {

            throw new Exception("Não foi possível recuperar a senha.");
        } else {

            $data = $results[0];

            $resultsRecovery = $sql->selectQuery("Call sp_userspasswordsrecoveries_create(:IDUSER, :DESIP)", array(
                ":IDUSER" => $data['iduser'],
                ":DESIP" => $_SERVER['REMOTE_ADDR']
            ));

            if (count($resultsRecovery) === 0) {

                throw new Exception("Não foi possível recuperar a senha");
            } else {

                $dataRecovery = $resultsRecovery[0];

                $code = User::encryptRecoveryCode($dataRecovery['idrecovery']);

                $link = "http://ecotayouza.com/admin/forgot/reset?code={$code}";

                $mailer = new Mailer($data['desemail'], $data['desperson'], "Redefinir senha TayouzaDev", "forgot", array(
                    "name" => $data['desperson'],
                    "link" => $link
                ));

                $mailer->sendMail();

                return $data;
            }
        }
    }

    public static function encryptRecoveryCode($code)
    {
        $ivLength = openssl_cipher_iv_length(User::CIPHER);
        $iv = openssl_random_pseudo_bytes($ivLength);

        $encrypt = openssl_encrypt(
            $code,
            User::CIPHER,
            User::SECRET,
            0,
            $iv
        );

        $encode64 = array(
            base64_encode($iv),
            base64_encode($encrypt)
        );

        return base64_encode(implode("-", $encode64));
    }

    public static function decryptRecoveryCode($code)
    {
        $mix = base64_decode($code);
        $divider = explode('-', $mix);
        $iv = base64_decode($divider[0]);
        $encrypt = base64_decode($divider[1]);

        return openssl_decrypt(
            $encrypt,
            User::CIPHER,
            User::SECRET,
            0,
            $iv
        );
    }

    public static function validForgotDecrypt($code)
    {
        $idrecovery = self::decryptRecoveryCode($code);

        $sql = new Sql();

        $results = $sql->selectQuery(" SELECT * FROM
                            tb_userspasswordsrecoveries a
                            INNER JOIN tb_users b USING(iduser)
                            INNER JOIN tb_persons c USING(idperson)
                            WHERE a.idrecovery = :IDRECOVERY
                            AND a.dtrecovery IS NULL
                            AND DATE_ADD(a.dtregister, INTERVAL 15 MINUTE) >= NOW()
                        ", array(
                            ":IDRECOVERY"=>$idrecovery
                        ));

        if(count($results) === 0){
            throw new Exception("Não foi possível redefinir a senha.");
        }

        return $results[0];

    }

    public static function setForgotUsed($idrecovery)
    {
        $sql = new Sql();

        $sql->selectQuery(" UPDATE tb_userspasswordsrecoveries 
                            SET dtrecovery = NOW() 
                            WHERE idrecovery = :IDRECOVERY"
                            , array(
                            ":IDRECOVERY"=>$idrecovery
                        ));
    }

    public function setPassword($pass)
    {
        $passHash = password_hash($pass, PASSWORD_BCRYPT, array("cost" => 12));

        $this->sql->runQuery("UPDATE tb_users 
                              SET despassword = :PASS 
                              WHERE iduser = :IDUSER",
                              array(
                                  ":PASS"=>$passHash,
                                  ":IDUSER"=>$this->getiduser()
                              ));
    }
}
