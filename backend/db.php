<?php
$host = 'localhost';
$db_user = 'root';
$db_password = '';
$db_name = 'selectionfilms';

$mysql = new mysqli($host, $db_user, $db_password, $db_name);

if ($mysql->connect_error) {
    die("Ошибка подключения: " . $mysql->connect_error);
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
