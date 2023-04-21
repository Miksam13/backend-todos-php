<?php
//$data = json_decode(file_get_contents('db.json'), true);
global $users;
require 'db_conn.php';
global $data, $conn;
$IDs = file_get_contents('ids.txt');
//$newData['id'] = $IDs+1;
//$newData['text'] = htmlspecialchars($_POST["text"]);
//$newData['checked'] = false;

$newData = file_get_contents('php://input');
$newData = json_decode($newData);

//$text = mysqli_real_escape_string($conn, $newData->text);
//$checked = mysqli_real_escape_string($conn, false);

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

$sql = "INSERT INTO todos(text, checked, user_id) VALUES('$newData->text', false, $user_id)";


if (!mysqli_query($conn, $sql)) {
    $error = mysqli_error($conn);
    header("HTTP/1.0 500 Internal Server Error");
    echo "{ 'error': 'Database error: $error' }";
} else {
    file_put_contents('ids.txt', $IDs+1);
    echo "{ id: " . ($IDs + 1) . " }";
}
die();
//array_push($data['items'], $newData);
//file_put_contents('db.json', json_encode($data));
