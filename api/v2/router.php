<?php
require '../v1/db_conn.php';
global $users, $conn, $data, $todos;

$action = $_GET['action'];
$IDs = file_get_contents('../v1/ids.txt');

$allowed_actions = ['login', 'logout', 'register', 'getItems', 'addItem', 'changeItem', 'deleteItem'];

if (!in_array($action, $allowed_actions)) {
    header("HTTP/1.0 404 Not Found");
    echo '{ "error": "Not right (or left) action query" }';
}

function getItems() {
    global $todos, $users;
    $todos = array_map(function($todo) {
        $todo['checked'] = $todo['checked'] == '1' ? true : false;
        return $todo;
    }, $todos);

    $users = array_filter($users, function($user) {
        $user_login = $_COOKIE["user_login"];
        return $user['login'] == $user_login;
    });

    usort($users, function($a, $b) {
        return $a['login'] <=> $b['login'];
    });

    $todos = array_filter($todos, function($item) {
        global $users;
        $user_id = $users[0]["id"];
        $user_id = intval($user_id);
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
}

function addItem() {
    global $conn, $IDs, $users;
    $newData = file_get_contents('php://input');
    $newData = json_decode($newData);
    $text = $newData -> text;

    $users = array_filter($users, function($user) {
        $user_login = $_COOKIE["user_login"];
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
}

function changeItem() {
    global $conn;
    $newData = file_get_contents('php://input');
    $newData = json_decode($newData);

    $id = $newData -> id;
    $newText = $newData -> text;
    $newChecked = $newData -> checked;
    if ($newChecked === false) {
        $newChecked = 0;
    } else {
        $newChecked = 1;
    }
    $sql = "UPDATE todos SET text='$newText', checked='$newChecked' WHERE id=$id";

    if (!mysqli_query($conn, $sql)) {
        $error = mysqli_error($conn);
        header("HTTP/1.0 500 Internal Server Error");
        echo "{ 'error': 'Database error: $error' }";
    } else {
        mysqli_query($conn, $sql);
        echo "{ 'ok' : true }";
    }
    die();
}

function deleteItem() {
    global $conn;
    $newData = file_get_contents('php://input');
    $newData = json_decode($newData);
    $id = $newData -> id;

    $sql = "DELETE FROM todos WHERE id = $id";

    if (!mysqli_query($conn, $sql)) {
        $error = mysqli_error($conn);
        header("HTTP/1.0 500 Internal Server Error");
        echo "{ 'error': 'Database error: $error' }";
    } else {
        echo "{ 'ok' : true }";
    }
    die();
}

function login() {
    global $users;
    $body = file_get_contents('php://input');
    $loginData = json_decode($body, true);

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
}

function register() {
    global $users, $conn;

    $body = file_get_contents('php://input');
    $loginData = json_decode($body, true);

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
}

function logout() {
    setcookie("user_login", '', 0, '/');
    echo '{ "ok": true }';
}

switch ($action) {
    case 'getItems':
        getItems();
        break;
    case 'addItem':
        addItem();
        break;
    case 'changeItem':
        changeItem();
        break;
    case 'deleteItem':
        deleteItem();
        break;
    case 'login':
        login();
        break;
    case 'register':
        register();
        break;
    case 'logout':
        logout();
        break;
    default:
        header("HTTP/1.0 404 Not Found");
        echo '{ "error": "Query error, action not found" }';
}