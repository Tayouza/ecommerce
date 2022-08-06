<?php

require_once("vendor/autoload.php");

use Slim\Slim;
use App\Class\Page;

$app = new Slim();

$app->config('debug', true);

$app->get('/', function () {
    $options = array(
        "data" => array(
            "title" => "Ecommerce",
            "h1" => "hello"
        )
    );
    $page = new Page($options);
    $page->setTpl("index");
});

$app->run();
