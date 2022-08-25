<?php

namespace App\DB;

use PDO;

class Sql extends PDO
{

    private $conn;

    public function __construct()
    {
        if(!isset($this->conn)){
            $this->conn = new PDO(
                $_ENV['DB_CONNECTION'] .
                ":host=" . $_ENV['DB_HOST'] . 
                ";dbname=" . $_ENV['DB_DATABASE'],
                $_ENV['DB_USERNAME'],
                $_ENV['DB_PASSWORD']
            );
        }
        return $this->conn;
    }

    private function setParams($statement, $parameters = array())
    {

        foreach ($parameters as $key => $value) {
            $this->setParam($statement, $key, $value);
        }
    }

    private function setParam($statement, $key, $value)
    {
        $statement->bindParam($key, $value);
    }

    public function runQuery($rawQuery, $params = array())
    {
        $stmt = $this->conn->prepare($rawQuery);

        $this->setParams($stmt, $params);

        $stmt->execute();

        return $stmt;
    }

    public function selectQuery($rawQuery, $params = array()): array
    {
        $stmt = $this->runQuery($rawQuery, $params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
