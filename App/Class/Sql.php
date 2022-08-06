<?php

namespace App\Class;

use PDO;

class Sql{
    private $instance;

    public function __construct()
    {
        $this->instance = new PDO(
            "mysql:
             host=".HOSTNAME.";
             dbname=".DBNAME,
            USERNAME,
            PASSWORD
        );
    }

    private function setParams($statement, $parameters = array())
    {
        foreach($parameters as $key => $value)
        {
            $this->bindParam($statement, $key, $value);
        }
    }

    private function bindParam($statement, $key, $value)
    {
        $statement->bindParam($key, $value);
    }

    public function runQuery($rawQuery, $parameters = array()):bool
    {
        $statement = $this->instance->prepare($rawQuery);
        $this->setParams($statement, $parameters);

        return $statement->execute();
    }

    public function select($rawQuery, $parameters = array()):array
    {
        $statement = $this->instance->prepare($rawQuery);
        $this->setParams($statement, $parameters);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}

