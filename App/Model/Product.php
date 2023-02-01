<?php

namespace App\Model;

use App\DB\Sql;
use App\Model\Model;
use Exception;

class Product extends Model
{
    private Sql $sql;

    public function __construct()
    {
        $this->sql = new Sql();
    }

    public static function listAll()
    {
        $sql = new Sql();

        return $sql->selectQuery("SELECT * FROM tb_products");
    }

    public static function checkList($list)
    {
        foreach($list as &$row){
            $p = new Product();
            $p->setData($row);
            $row = $p->getValues();
        }

        return $list;
    }

    public function save()
    {
        $results = $this->sql->selectQuery(
            "  CALL sp_products_save(
                :IDPRODUCT, 
                :DESPRODUCT, 
                :VLPRICE, 
                :VLWIDTH, 
                :VLHEIGHT, 
                :VLLENGTH, 
                :VLWEIGHT, 
                :DESURL)",
            array(
                ":IDPRODUCT" => $this->getidproduct(),
                ":DESPRODUCT" => $this->getdesproduct(),
                ":VLPRICE" => $this->getvlprice(),
                ":VLWIDTH" => $this->getvlwidth(),
                ":VLHEIGHT" => $this->getvlheight(),
                ":VLLENGTH" => $this->getvllength(),
                ":VLWEIGHT" => $this->getvlweight(),
                ":DESURL" => $this->getdesurl(),
            )
        );

        $this->setData($results[0]);
    }

    public function get($idProduct)
    {
        $results = $this->sql->selectQuery("SELECT * FROM tb_products WHERE idproduct = :IDPRODUCT", array(
            ":IDPRODUCT" => $idProduct
        ));

        $this->setData($results[0]);
    }

    public function delete($idProduct)
    {
        $this->sql->runQuery("DELETE FROM tb_products WHERE idproduct = :IDPRODUCT", array(
            ":IDPRODUCT" => $idProduct
        ));
    }

    public function checkPhoto()
    {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] .
            DIRECTORY_SEPARATOR . "res" .
            DIRECTORY_SEPARATOR . "img" .
            DIRECTORY_SEPARATOR . "products" .
            DIRECTORY_SEPARATOR . $this->getidproduct() . ".webp")) {

            $url = "/res/img/products/" . $this->getidproduct() . ".webp";
        } else {

            $url = "/res/img/placeholder.webp";
        }

        $this->setdesphoto($url);
    }

    public function getValues()
    {
        $this->checkPhoto();

        $values = parent::getValues();

        return $values;
    }

    public function setPhoto($file)
    {
        $extension = explode('.', $file['name']);
        $extension = end($extension);

        switch ($extension) {
            case "jpg":
            case "jpeg":
                $image = imagecreatefromjpeg($file['tmp_name']);
                break;

            case "gif":
                $image = imagecreatefromgif($file['tmp_name']);
                break;

            case "png":
                $image = imagecreatefrompng($file['tmp_name']);
                break;

            case "webp":
                $image = imagecreatefromwebp($file['tmp_name']);
                break;

            default:
                throw new Exception("Não são aceitas imagens/arquivos neste formato");
                break;
        }

        $dist = $_SERVER['DOCUMENT_ROOT'] .
            DIRECTORY_SEPARATOR . "res" .
            DIRECTORY_SEPARATOR . "img" .
            DIRECTORY_SEPARATOR . "products" .
            DIRECTORY_SEPARATOR . $this->getidproduct() . ".webp";

        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);
        imagewebp($image, $dist);
        imagedestroy($image);

        $this->checkPhoto();
    }

    public function getFromUrl($desurl)
    {
        $result = $this->sql->selectQuery("SELECT * FROM tb_products WHERE desurl = :DESURL LIMIT 1", [
            ':DESURL' => $desurl
        ]);

        $this->setData($result[0]);
    }

    public function getCategories()
    {
        return $this->sql->selectQuery('SELECT * FROM tb_categories as cat 
                                                INNER JOIN tb_productscategories as prodcat
                                                ON cat.idcategory = prodcat.idcategory
                                                WHERE prodcat.idproduct = :IDPRODUCT', [
                                                    ':IDPRODUCT' => $this->getidproduct()
                                                ]);
    }
}
