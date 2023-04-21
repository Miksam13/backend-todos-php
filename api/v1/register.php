<?php
require 'db_conn.php';
global $users, $conn;

$body = file_get_contents('php://input');
$loginData = json_decode($body, true);
//echo json_encode($loginData);
//$data = file_get_contents("http://localhost/php_projects/lvl%202/api/v1/getItems.php");
//$data = file_get_contents('db.json');

if (isset($loginData["login"]) && isset($loginData["pass"])) {
    $login = $loginData["login"];
    $pass = $loginData["pass"];
    foreach ($users as $user) {
        if ($user["login"] == $login) {
            if ($user) {
                header("HTTP/1.0 400 Bad Request");
                echo '{ "error": "This login already register" }';
                die();
            }
        } else {
            $sql = "INSERT INTO users(login, password) VALUES('$login', '$pass')";
            if (!mysqli_query($conn, $sql)) {
                $error = mysqli_error($conn);
                header("HTTP/1.0 500 Internal Server Error");
                echo "{ 'error': 'Database error: $error' }";
            } else {
                echo '{ "ok": true }';
            }
            die();
        }
    }
} else {
    header("HTTP/1.0 400 Bad Request");
    echo '{ "error": "login and pass are required" }';
}
die();
