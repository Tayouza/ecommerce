<?php 

require_once("vendor/autoload.php");

use Slim\Slim;
use App\Class\Sql;

$app = new Slim();

$app->config('debug', true);

$app->get('/', function(){
    $sql = new Sql;
    $result = $sql->select("SELECT * FROM tb_users");
    echo json_encode($result);
});

$app->get('/pessoas', function(){
    return '/tayouza/views/pessoas.php';
});

$app->run();

?>