<?php
require 'db_conn.php';
global $todos, $users;
//$data = file_get_contents("http://localhost/php_projects/lvl%202/api/v1/getItems.php");
//$data = file_get_contents('db.json');

$todos = array_map(function($todo) {
    $todo['checked'] = $todo['checked'] == '1' ? true : false;
    return $todo;
}, $todos);

$user_login = $_COOKIE["user_login"];

$users = array_filter($users, function($user) {
    global $user_login;
    return $user['login'] == $user_login;
});

usort($users, function($a, $b) {
    return $a['login'] <=> $b['login'];
});

$user_id = $users[0]["id"];
$user_id = intval($user_id);

$todos = array_filter($todos, function($item) {
    global $user_id;
    return $item['user_id'] == $user_id;
});

usort($todos, function($a, $b) {
    return $a['user_id'] <=> $b['user_id'];
});

if ($todos) {
    echo json_encode($todos);
} else {
    header("HTTP/1.0 500 Internal Server Error");
    echo "{ 'error': 'Server bad work' }";
}

