<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Class\PageAdmin;
use App\Model\User;

$app->get("/admin", function (Request $req, Response $res, $args) {
    User::verifyLogin();

    $page = new PageAdmin(array(
        "header-data" => array(
            "user" => $_SESSION['user']
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

$app->get("/admin/forgot", function (Request $req, Response $res, $args) {

    $page = new PageAdmin(
        array(
            "header" => false,
            "footer" => false
        )
    );

    $page->setTpl("forgot");
});

$app->post("/admin/forgot", function (Request $req, Response $res, $args) {
    $email = $_POST['email'];

    User::getForgot($email);

    header("Location: /admin/forgot/sent");
    exit;
});

$app->get("/admin/forgot/sent", function (Request $req, Response $res, $args) {
    $page = new PageAdmin(array(
        "header" => false,
        "footer" => false
    ));
    $page->setTpl("forgot-sent");
});

$app->get("/admin/forgot/reset", function () {
    if (isset($_GET['code'])) {

        $code = $_GET['code'];

        $user = User::validForgotDecrypt($code);

        $page = new PageAdmin(array(
            "header" => false,
            "footer" => false
        ));

        $page->setTpl("forgot-reset", array(
            "name" => $user['desperson'],
            "code" => $code
        ));
    } else {

        header('Location: /admin/forgot');
        exit;
    }
});

$app->post("/admin/forgot/reset", function () {
    $code = $_POST['code'];

    $forgot = User::validForgotDecrypt($code);

    User::setForgotUsed($forgot['idrecovery']);

    $user = new User();

    $user->get((int) $forgot['iduser']);

    $user->setPassword($_POST['password']);

    $page = new PageAdmin(array(
        "header" => false,
        "footer" => false
    ));

    $page->setTpl("forgot-reset-success");
});
