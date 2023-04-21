<?php
require 'db_conn.php';
global $users;

$body = file_get_contents('php://input');
$loginData = json_decode($body, true);
//echo json_encode($loginData);
//$data = file_get_contents("http://localhost/php_projects/lvl%202/api/v1/getItems.php");
//$data = file_get_contents('db.json');

if (isset($loginData["login"]) && isset($loginData["pass"])) {
    $login = $loginData["login"];
    $pass = $loginData["pass"];
    foreach ($users as $user) {
        if ($user["login"] == $login && $user["password"] == $pass) {
            if ($user) {
                setcookie('user_login', $login, 0, '/');
                echo '{ "ok": true }';
                die();
            }
        }
    }
} else {
    header("HTTP/1.0 400 Bad Request");
    echo "{ 'error': 'Login and pass are required' }";
}
