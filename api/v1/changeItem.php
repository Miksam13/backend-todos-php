<?php
//$data = json_decode(file_get_contents('db.json'), true);
require 'db_conn.php';
global $data, $conn;
//$newData = json_decode($_POST['request']);
$newData = file_get_contents('php://input');
$newData = json_decode($newData);
//echo json_encode($newData);

//echo json_encode($newData);
/*
$indexNewData = array_search($newData -> id, array_column($data['items'], 'id'));

if ($indexNewData) {
    $data['items'][$indexNewData] = $newData;
}*/
$id = $newData -> id;
$newText = $newData -> text;
$newChecked = $newData -> checked;
if ($newChecked === false) {
    $newChecked = 0;
} else {
    $newChecked = 1;
}
$sql = "UPDATE todos SET text='$newText', checked='$newChecked' WHERE id=$id";
//echo json_encode($data['items']);

if (!mysqli_query($conn, $sql)) {
    $error = mysqli_error($conn);
    header("HTTP/1.0 500 Internal Server Error");
    echo "{ 'error': 'Database error: $error' }";
} else {
    mysqli_query($conn, $sql);
    echo "{ 'ok' : true }";
}
die();

//file_put_contents('db.json', json_encode($data));
