<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Class\Page;
use App\Model\Category;

$app->get('/', function (Request $req, Response $res, $args) {
    $page = new Page();
    $page->setTpl("index");
});

$app->get('/carrinho', function (Request $req, Response $res, $args) {
    $page = new Page();
    $page->setTpl('carrinho');
});

$app->get("/detalhes-produto", function (Request $req, Response $res, $args) {
    $page = new Page();
    $page->setTpl('detalhes-produto');
});

$app->get("/login", function (Request $req, Response $res, $args) {
    $page = new Page();
    $page->setTpl('login');
});

$app->get("/esqueci", function (Request $req, Response $res, $args) {
    $page = new Page();
    $page->setTpl('esqueci');
});

$app->get("/pagamento", function (Request $req, Response $res, $args) {
    $page = new Page();
    $page->setTpl('pagamento');
});

$app->get("/lista-produtos", function (Request $req, Response $res, $args) {
    $page = new Page();
    $page->setTpl('lista-produtos');
});

$app->get("/category/{id}", function (Request $req, Response $res, $args)
{
    $idCategory = $args['id'];

    $category = new Category();

    $category->get((int) $idCategory);

    $page = new Page();

    $page->setTpl("category", array(
        'category' => $category->getValues(),
        'products' => array()
    ));

});