<?php

namespace App\Model;

use App\DB\Sql;
use App\Model\Model;

class Category extends Model
{
    private Sql $sql;

    public function __construct()
    {
        $this->sql = new Sql();
    }

    public static function listAll()
    {
        $sql = new Sql();

        return $sql->selectQuery("SELECT * FROM tb_categories");
    }

    public function save()
    {
        $results = $this->sql->selectQuery("CALL sp_categories_save(:IDCATEGORY, :DESCATEGORY)", array(
            ":IDCATEGORY" => $this->getidcategory(),
            ":DESCATEGORY" => $this->getdescategory()
        ));

        $this->setData($results[0]);
        
        self::updateFile();
    }

    public function get($idCategory)
    {
        $results = $this->sql->selectQuery("SELECT * FROM tb_categories WHERE idcategory = :IDCATEGORY", array(
            ":IDCATEGORY"=>$idCategory
        ));
        
        $this->setData($results[0]);
    }

    public function delete($idCategory)
    {
        $this->sql->runQuery("DELETE FROM tb_categories WHERE idcategory = :IDCATEGORY", array(
            ":IDCATEGORY"=>$idCategory
        ));

        self::updateFile();
    }

    public static function updateFile()
    {
        $categories = self::listAll();

        $html = array();

        foreach($categories as $row){
            array_push($html, "<li><a href='/categories/{$row['idcategory']}'>{$row['descategory']}</a></li>".PHP_EOL);
        }

        $path = $_SERVER['DOCUMENT_ROOT'].
                DIRECTORY_SEPARATOR."App".
                DIRECTORY_SEPARATOR."views".
                DIRECTORY_SEPARATOR."categories-menu.html";

        file_put_contents($path, implode("", $html));

    }

    public function getProducts($related = true)
    {
        if($related === true){
            return $this->sql->selectQuery("  SELECT * FROM tb_products
                                        WHERE idproduct IN(
                                            SELECT a.idproduct
                                            FROM tb_products a
                                            INNER JOIN tb_productscategories b
                                            ON a.idproduct = b.idproduct
                                            WHERE b.idcategory = :IDCATEGORY
                                        )
                                    ",
                                    array(
                                        ":IDCATEGORY"=>$this->getidcategory()
                                    )
                                );
        }else{
            
            return $this->sql->selectQuery("  SELECT * FROM tb_products
                                        WHERE idproduct NOT IN(
                                            SELECT a.idproduct
                                            FROM tb_products a
                                            INNER JOIN tb_productscategories b
                                            ON a.idproduct = b.idproduct
                                            WHERE b.idcategory = :IDCATEGORY
                                        )
                                    ",
                                    array(
                                        ":IDCATEGORY"=>$this->getidcategory()
                                    )
                                );
        }
    }

    public function addProduct(Product $product)
    {
        $this->sql->runQuery("INSERT INTO tb_productscategories(idcategory, idproduct) 
                        VALUES(:IDCATEGORY, :IDPRODUCT)",
                        array(
                            ':IDCATEGORY'=>$this->getidcategory(),
                            ':IDPRODUCT'=>$product->getidproduct()
                        ));
    }

    public function removeProduct(Product $product)
    {
        $this->sql->runQuery("DELETE FROM tb_productscategories WHERE idcategory = :IDCATEGORY AND idproduct = :IDPRODUCT",
                        array(
                            ':IDCATEGORY'=>$this->getidcategory(),
                            ':IDPRODUCT'=>$product->getidproduct()
                        ));
    }
}
