<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Class\PageAdmin;
use App\Model\User;

$app->get("/admin/users", function (Request $req, Response $res, $args) {
    User::verifyLogin();

    $users = User::listAll();

    $page = new PageAdmin(array(
        "header-data" => array(
            "user" => $_SESSION['user']
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
            "user" => $_SESSION['user']
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
            "user" => $_SESSION['user']
        )
    ));

    $page->setTpl("users-update", array(
        "user" => $user->getValues()
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
