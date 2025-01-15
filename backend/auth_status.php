<?php
require 'db.php';

header('Content-Type: application/json');

$response = [
    'isLoggedIn' => isset($_SESSION['user']),
    'userName' => $_SESSION['user']['name'] ?? null,
];

echo json_encode($response);
