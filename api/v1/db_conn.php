<?php

$host = 'localhost';
$username = 'root';
$password = '';
$db_name = 'lvl_2_todos';

$conn = mysqli_connect($host, $username, $password, $db_name);

/*if ($conn){
    echo "connected!";
} else {
    echo "Connection failed: " . mysqli_connect_error();
}*/

$sqlTodos = 'SELECT * FROM todos';
$sqlUsers = 'SELECT * FROM users';
$resultTodos = mysqli_query($conn, $sqlTodos);
$resultUsers = mysqli_query($conn, $sqlUsers);
$todos = mysqli_fetch_all($resultTodos, MYSQLI_ASSOC);
$users = mysqli_fetch_all($resultUsers, MYSQLI_ASSOC);

