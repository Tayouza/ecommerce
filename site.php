<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Class\Page;
use App\Model\Category;
use App\Model\Product;

$app->get('/', function (Request $req, Response $res, $args) {
    $products = Product::listAll();

    $page = new Page();
    $page->setTpl("index", array(
        'products' => Product::checkList($products)
    ));
});

$app->get('/cart', function (Request $req, Response $res, $args) {
    $page = new Page();
    $page->setTpl('cart');
});

$app->get("/login", function (Request $req, Response $res, $args) {
    $page = new Page();
    $page->setTpl('login');
});

$app->get("/forget", function (Request $req, Response $res, $args) {
    $page = new Page();
    $page->setTpl('forget');
});

$app->get("/payment", function (Request $req, Response $res, $args) {
    $page = new Page();
    $page->setTpl('payment');
});

$app->get("/products", function (Request $req, Response $res, $args) {
    $page = new Page();
    $page->setTpl('products');
});

$app->get("/categories/{id}", function (Request $req, Response $res, $args)
{
    $idCategory = $args['id'];

    $category = new Category();

    $category->get((int) $idCategory);

    $page = new Page();

    $page->setTpl("category", array(
        'category' => $category->getValues(),
        'products' => Product::checkList($category->getProducts())
    ));

});

$app->get("/products/{desurl}", function(Request $req, Response $res, $args){
    $product = new Product();

    $product->getFromUrl($args['desurl']);
    
    $page = new Page();

    $page->setTpl("product-detail", [
        'product'    => $product->getValues(),
        'categories' => $product->getCategories(),
    ]);
});