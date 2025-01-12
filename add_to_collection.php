<?php
require 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user'];
$filmId = $_POST['film_id'];
$collectionId = $_POST['collection_id'];
$newCollectionName = $_POST['new_collection'];


if ($newCollectionName) {
    // Создать новую подборку
    $query = $mysql->prepare("INSERT INTO collections (user_id, name) VALUES (?, ?)");
    $query->bind_param('is', $userId, $newCollectionName);
    $query->execute();
    $collectionId = $mysql->insert_id;
}

// Добавить фильм в подборку
$query = $mysql->prepare("INSERT INTO collection_films (collection_id, film_id) VALUES (?, ?)");
$query->bind_param('ii', $collectionId, $filmId);
$query->execute();

header("Location: index.php");
exit();
?>
