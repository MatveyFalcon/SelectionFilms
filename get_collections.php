<?php
require 'db.php';

header('Content-Type: application/json');

if (isset($_SESSION['user'])) {
    $userId = $_SESSION['user'];

    $collectionsQuery = $mysql->prepare("SELECT id, name FROM collections WHERE user_id = ?");
    $collectionsQuery->bind_param('i', $userId);
    $collectionsQuery->execute();
    $collectionsResult = $collectionsQuery->get_result();

    $collections = [];
    while ($row = $collectionsResult->fetch_assoc()) {
        $collections[] = [
            'id' => $row['id'],
            'name' => $row['name']
        ];
    }

    $collectionsQuery->close();

    echo json_encode(['collections' => $collections]);
} else {
    echo json_encode(['error' => 'User not authenticated']);
}
