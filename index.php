<?php

require_once("vendor/autoload.php");

use Slim\App as Slim;
use Slim\Container;
use App\Class\Page;
use App\Class\PageAdmin;
use App\Model\User;

$c = new Container(); //Create Your container

//Override the default Not Found Handler before creating App
$c['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        $file =$_SERVER['DOCUMENT_ROOT']."/App/views/404.html";
        return $response->withStatus(404)
            ->withHeader('Content-Type', 'text/html')
            ->write(file_get_contents($file));
    };
};

$app = new Slim($c);

$app->get('/', function () {
    $page = new Page();
    $page->setTpl("index");
});

$app->get('/carrinho', function () {
    $page = new Page();
    $page->setTpl('carrinho');
});

$app->get("/detalhes-produto", function () {
    $page = new Page();
    $page->setTpl('detalhes-produto');
});

$app->get("/login", function () {
    $page = new Page();
    $page->setTpl('login');
});

$app->get("/esqueci", function () {
    $page = new Page();
    $page->setTpl('esqueci');
});

$app->get("/pagamento", function () {
    $page = new Page();
    $page->setTpl('pagamento');
});

$app->get("/lista-produtos", function () {
    $page = new Page();
    $page->setTpl('lista-produtos');
});

$app->get("/admin/login", function () {
    $page = new PageAdmin(array(
        'header' => false,
        'footer' => false
    ));
    $page->setTpl('login');
});

$app->post("/admin/login", function () {
    User::login($_POST['login'], $_POST['password']);
    header("Location: /admin");
    exit;
});

$app->get("/admin", function () {
    $page = new PageAdmin();
    $page->setTpl('index');
});

$app->run();
