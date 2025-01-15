<?php
require 'functions.php';

header('Content-Type: application/json');

$response = [
    'isLoggedIn' => (bool) $userId,
    'testScore' => $testScore ?? 0
];

echo json_encode($response);
