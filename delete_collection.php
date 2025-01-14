<?php
require 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user'];
$collectionId = $_POST['collection_id'];

// Проверяем, принадлежит ли подборка пользователю
$query = $mysql->prepare("SELECT id FROM collections WHERE id = ? AND user_id = ?");
$query->bind_param('ii', $collectionId, $userId);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    http_response_code(403); // Доступ запрещен
    exit();
}

// Удаляем фильмы из подборки
$deleteFilms = $mysql->prepare("DELETE FROM collection_films WHERE collection_id = ?");
$deleteFilms->bind_param('i', $collectionId);
$deleteFilms->execute();
$deleteFilms->close();

// Удаляем саму подборку
$deleteCollection = $mysql->prepare("DELETE FROM collections WHERE id = ?");
$deleteCollection->bind_param('i', $collectionId);
$deleteCollection->execute();
$deleteCollection->close();

http_response_code(200); // Успешно
exit();
