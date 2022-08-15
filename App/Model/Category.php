<?php

namespace App\Model;

use App\Class\Mailer;
use App\Db\Sql;
use App\Model\Model;
use Exception;

class Category extends Model
{

    public static function listAll()
    {
        $sql = new Sql();

        return $sql->selectQuery("SELECT * FROM tb_categories");
    }

    public function save()
    {
        
        $sql = new Sql();
        
        $results = $sql->selectQuery("CALL sp_categories_save(:IDCATEGORY, :DESCATEGORY)", array(
            ":IDCATEGORY" => $this->getidcategory(),
            ":DESCATEGORY" => $this->getdescategory()
        ));

        $this->setData($results[0]);
    }

    public function get($idCategory)
    {
        $sql = new Sql();

        $results = $sql->selectQuery("SELECT * FROM tb_categories WHERE idcategory = :IDCATEGORY", array(
            ":IDCATEGORY"=>$idCategory
        ));
        
        $this->setData($results[0]);
    }

    public function delete($idCategory)
    {
        $sql = new Sql();

        $sql->runQuery("DELETE FROM tb_categories WHERE idcategory = :IDCATEGORY", array(
            ":IDCATEGORY"=>$idCategory
        ));
    }
}
