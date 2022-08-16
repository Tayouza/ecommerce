<?php
session_start();
require_once("vendor/autoload.php");

use Slim\App as Slim;
use Slim\Container;

$configContainer = [
    'settings' => [
        'displayErrorDetails' => true
    ]
];

$c = new Container($configContainer); //Create Your container

//Override the default Not Found Handler before creating App
$c['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        $file = $_SERVER['DOCUMENT_ROOT'] . "/App/views/404.html";
        return $response->withStatus(404)
            ->withHeader('Content-Type', 'text/html')
            ->write(file_get_contents($file));
    };
};

$app = new Slim($c);

//split routes for "admin" and "site"
require_once("site.php");
require_once("admin.php");
require_once("admin-users.php");
require_once("admin-categories.php");
require_once("admin-products.php");

$app->run();
