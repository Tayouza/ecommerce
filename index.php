<?php

require_once("vendor/autoload.php");

use Slim\Slim;
use App\Class\Page;

$app = new Slim();

$app->config('debug', true);

$app->get('/', function () {
    $options = array(
    );
    $page = new Page($options);
    $page->setTpl("index");
});

$app->get('/carrinho', function(){
    $page = new Page();
    $page->setTpl('carrinho');
});

$app->run();
