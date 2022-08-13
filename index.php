<?php
session_start();
require_once("vendor/autoload.php");

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
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

$app->get("/admin", function (Request $req, Response $res, $args) {
    User::verifyLogin();
    
    $page = new PageAdmin(array(
        "header-data" => array(
            "user"=>$_SESSION['user']
        )
    ));

    $page->setTpl("index");
});

$app->get("/admin/login", function (Request $req, Response $res, $args) {
    $page = new PageAdmin(array(
        'header' => false,
        'footer' => false
    ));
    $page->setTpl('login');
    return $res;
});

$app->post("/admin/login", function (Request $req, Response $res, $args) {
    try {
        User::login($_POST['login'], $_POST['password']);
        header("Location: /admin");
        exit;
    } catch (Exception $e) {
        header("Location: /admin/login");
        exit;
    }
});

$app->get("/admin/logout", function (Request $req, Response $res, $args) {
    User::logout();

    header("Location: /admin/login");
    exit;
});

$app->get("/admin/users", function (Request $req, Response $res, $args) {
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

$app->get("/admin/users/create", function (Request $req, Response $res, $args) {
    User::verifyLogin();

    $page = new PageAdmin(array(
        "header-data" => array(
            "user"=>$_SESSION['user']
        )
    ));

    $page->setTpl("users-create");
});

$app->post("/admin/users/create", function (Request $req, Response $res, $args) {

    User::verifyLogin();

    $user = new User();

    $_POST['inadmin'] = (isset($_POST['inadmin'])) ? 1 : 0;

    $user->setData($_POST);

    $user->save();

    header("Location: /admin/users");
    exit;
});

$app->get("/admin/users/{iduser}", function (Request $req, Response $res, $args) {
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

$app->post("/admin/users/{iduser}", function (Request $req, Response $res, $args) {

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

$app->get("/admin/users/delete/{iduser}", function (Request $req, Response $res, $args) {

    User::verifyLogin();

    $iduser = $args['iduser'];

    $user = new User();

    $user->get((int) $iduser);

    $user->delete();

    header("Location: /admin/users");
    exit;

});

$app->get("/admin/forgot", function (Request $req, Response $res, $args){

    $page = new PageAdmin(
        array(
            "header"=>false,
            "footer"=>false
        )
    );

    $page->setTpl("forgot");

});

$app->post("/admin/forgot", function (Request $req, Response $res, $args){
    $email = $_POST['email'];

    User::getForgot($email);

    header("Location: /admin/forgot/sent");
    exit;
});

$app->get("/admin/forgot/sent", function(Request $req, Response $res, $args){
    $page = new PageAdmin(array(
        "header"=>false,
        "footer"=>false
    ));
    $page->setTpl("forgot-sent");
});

$app->get("/admin/forgot/reset", function(){
    if(isset($_GET['code'])){

        $code = $_GET['code'];

        $user = User::validForgotDecrypt($code);
        
        $page = new PageAdmin(array(
            "header"=>false,
            "footer"=>false
        ));

        $page->setTpl("forgot-reset", array(
            "name"=>$user['desperson'],
            "code"=>$code
        ));


    }else{

        header('Location: /admin/forgot');
        exit;
    }
});

$app->post("/admin/forgot/reset", function(){
    $code = $_POST['code'];

    $forgot = User::validForgotDecrypt($code);

    User::setForgotUsed($forgot['idrecovery']);

    $user = new User();

    $user->get((int) $forgot['iduser']);

    $user->setPassword($_POST['password']);

    $page = new PageAdmin(array(
        "header"=>false,
        "footer"=>false
    ));

    $page->setTpl("forgot-reset-success");

});

$app->run();
