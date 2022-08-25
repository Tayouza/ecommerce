<?php

use App\Class\Page;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Class\PageAdmin;
use App\Model\User;
use App\Model\Category;
use App\Model\Product;

$app->get("/admin/categories", function () {
    User::verifyLogin();

    $categories = Category::listAll();

    $page = new PageAdmin(array(
        "header-data" => array(
            "user" => $_SESSION['user']
        )
    ));

    $page->setTpl("categories", array(
        "categories" => $categories
    ));
});

$app->get("/admin/categories/create", function () {
    User::verifyLogin();

    $page = new PageAdmin(array(
        "header-data" => array(
            "user" => $_SESSION['user']
        )
    ));

    $page->setTpl("categories-create");
});

$app->post("/admin/categories/create", function () {
    User::verifyLogin();

    $category = new Category();

    $category->setData($_POST);

    $category->save();

    header("Location: /admin/categories");
    exit;
});

$app->get("/admin/categories/delete/{id}", function (Request $req, Response $res, $args) {
    User::verifyLogin();

    $idCategory = $args['id'];
    
    $category = new Category();

    $category->delete((int) $idCategory);
    
    header("Location: /admin/categories");
    exit;
});

$app->get("/admin/categories/{id}", function (Request $req, Response $res, $args) {
    User::verifyLogin();

    $idCategory = $args['id'];

    $category = new Category();

    $category->get((int) $idCategory);
    
    $page = new PageAdmin(array(
        "header-data" => array(
            "user" => $_SESSION['user']
        )
    ));

    $page->setTpl("categories-update", array(
        "category"=>$category->getValues()
    ));
});

$app->post("/admin/categories/{id}", function (Request $req, Response $res, $args) {
    User::verifyLogin();

    $idCategory = $args['id'];

    $category = new Category();

    $category->get((int) $idCategory);

    $category->setData($_POST);

    $category->save();

    header("Location: /admin/categories");
    exit;
});

$app->get("/admin/categories/{idCategory}/products", function(Request $req, Response $res, $args){
    User::verifyLogin();

    $idCategory = $args['idCategory'];
    
    $category = new Category();

    $category->get((int) $idCategory);

    $page = new PageAdmin(array(
        "header-data" => array(
            "user" => $_SESSION['user']
        )
    ));

    $page->setTpl("categories-products", array(
        'category'=>$category->getValues(),
        'productsRelated' => $category->getProducts(),
        'productsNotRelated' => $category->getProducts(false)
    ));

});

$app->get("/admin/categories/{idCategory}/products/{idProduct}/add", function(Request $req, Response $res, $args){
    User::verifyLogin();

    $idCategory = $args['idCategory'];

    $idProduct = $args['idProduct'];
    
    $category = new Category();

    $category->get((int) $idCategory);
    
    $product = new Product();

    $product->get((int) $idProduct);

    $category->addProduct($product);

    header("Location: /admin/categories/{$idCategory}/products");
    exit;

});

$app->get("/admin/categories/{idCategory}/products/{idProduct}/remove", function(Request $req, Response $res, $args){
    User::verifyLogin();

    $idCategory = $args['idCategory'];

    $idProduct = $args['idProduct'];
    
    $category = new Category();

    $category->get((int) $idCategory);
    
    $product = new Product();

    $product->get((int) $idProduct);

    $category->removeProduct($product);

    header("Location: /admin/categories/{$idCategory}/products");
    exit;
});
