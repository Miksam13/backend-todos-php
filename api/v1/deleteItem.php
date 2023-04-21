<?php
//$data = json_decode(file_get_contents('db.json'), true);
require 'db_conn.php';
global $conn;

$newData = file_get_contents('php://input');
//$newData = json_decode($_POST['request']);

/*$key = array_search($newData -> id, array_column($data['items'], 'id'));

if ($key !== false) {
    unset($data['items'][$key]);
}
$data['items'] = array_values($data['items']);*/
//echo json_encode($data['items']);
$id = mysqli_real_escape_string($conn, json_decode($newData) -> id);

$sql = "DELETE FROM todos WHERE id = $id";

if (!mysqli_query($conn, $sql)) {
    $error = mysqli_error($conn);
    header("HTTP/1.0 500 Internal Server Error");
    echo "{ 'error': 'Database error: $error' }";
} else {
    echo "{ 'ok' : true }";
}
die();

//file_put_contents('db.json', json_encode($data));
