<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Class\PageAdmin;
use App\Model\Product;
use App\Model\User;

$app->get("/admin/products", function(){
    User::verifyLogin();

    $products = Product::listAll();

    $page = new PageAdmin(array(
        "header-data" => array(
        "user" => $_SESSION['user']
    )));

    $page->setTpl("products", array(
        'products' => $products
    ));

});

$app->get("/admin/products/create", function(){
    User::verifyLogin();

    $page = new PageAdmin(array(
        "header-data" => array(
            "user" => $_SESSION['user']
        )
    ));

    $page->setTpl("products-create");

});

$app->post("/admin/products/create", function(){
    User::verifyLogin();

    $products = new Product();

    $products->setData($_POST);
    
    $products->save();

    header("Location: /admin/products");
    exit;
});

$app->get("/admin/products/{id}", function(Request $req, Response $res, $args){
    User::verifyLogin();

    $idProduct = $args['id'];

    $product = new Product();

    $product->get((int) $idProduct);

    $page = new PageAdmin(array(
        "header-data" => array(
            "user" => $_SESSION['user']
        )
    ));

    $page->setTpl("products-update", array(
        'product' => $product->getValues()
    ));

});

$app->post("/admin/products/{id}", function(Request $req, Response $res, $args){
    User::verifyLogin();

    $idProduct = $args['id'];

    $product = new Product();

    $product->get((int) $idProduct);

    $product->setData($_POST);

    $product->setPhoto($_FILES['file']);

    header("Location: /admin/products");
    exit;

});

$app->get("/admin/products/delete/{id}", function(Request $req, Response $res, $args){
    User::verifyLogin();

    $idProduct = $args['id'];

    $products = new Product();

    $products->delete($idProduct);

    header("Location: /admin/products");
    exit;
});