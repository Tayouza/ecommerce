<?php
session_start();
require_once("vendor/autoload.php");

use Slim\App as Slim;
use Slim\Container;
use App\Class\Page;
use App\Class\PageAdmin;
use App\Model\User;

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

$app->get("/admin", function () {
    User::verifyLogin();
    
    $page = new PageAdmin(array(
        "header-data" => array(
            "user"=>$_SESSION['user']
        )
    ));

    $page->setTpl("index");
});

$app->get("/admin/login", function () {
    $page = new PageAdmin(array(
        'header' => false,
        'footer' => false
    ));
    $page->setTpl('login');
});

$app->post("/admin/login", function () {
    try {
        User::login($_POST['login'], $_POST['password']);
        header("Location: /admin");
        exit;
    } catch (Exception $e) {
        header("Location: /admin/login");
        exit;
        //echo $e->getMessage();
    }
});

$app->get("/admin/logout", function () {
    User::logout();

    header("Location: /admin/login");
    exit;
});

$app->get("/admin/users", function () {
    User::verifyLogin();

    $users = User::listAll();

    $page = new PageAdmin(array(
        "header-data" => array(
            "user"=>$_SESSION['user']
        )
    ));

    $page->setTpl("users", array(
        "users" => $users
    ));
});

$app->get("/admin/users/create", function () {
    User::verifyLogin();

    $page = new PageAdmin(array(
        "header-data" => array(
            "user"=>$_SESSION['user']
        )
    ));

    $page->setTpl("users-create");
});

$app->post("/admin/users/create", function () {

    User::verifyLogin();

    $user = new User();

    $_POST['inadmin'] = (isset($_POST['inadmin'])) ? 1 : 0;

    $user->setData($_POST);

    $user->save();

    header("Location: /admin/users");
    exit;
});

$app->get("/admin/users/{iduser}", function ($req, $res, $args) {
    User::verifyLogin();

    $iduser = $args['iduser'];

    $user = new User();

    $user->get((int) $iduser);

    $page = new PageAdmin(array(
        "header-data" => array(
            "user"=>$_SESSION['user']
        )
    ));

    $page->setTpl("users-update", array(
        "user"=>$user->getValues()
    ));
});

$app->post("/admin/users/{iduser}", function ($req, $res, $args) {

    User::verifyLogin();

    $iduser = $args['iduser'];

    $_POST['inadmin'] = (isset($_POST['inadmin'])) ? 1 : 0;

    $user = new User();

    $user->get((int) $iduser);

    $user->setData($_POST);

    $user->update();

    header("Location: /admin/users");
    exit;

});

$app->get("/admin/users/delete/{iduser}", function ($req, $res, $args) {

    User::verifyLogin();

    $iduser = $args['iduser'];

    $user = new User();

    $user->get((int) $iduser);

    $user->delete();

    header("Location: /admin/users");
    exit;

});

$app->run();
