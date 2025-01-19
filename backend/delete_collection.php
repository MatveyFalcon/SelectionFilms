<?php
require 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user'];
$collectionId = $_POST['collection_id'];

if (empty($collectionId)) {
    http_response_code(400); // Неправильный запрос
    exit('Не указан ID подборки');
}

// Проверяем, принадлежит ли подборка пользователю через процедуру
$isOwned = false;
$query = $mysql->prepare("CALL CheckCollectionOwnership(?, ?, @is_owned)");
$query->bind_param('ii', $collectionId, $userId);
$query->execute();
$query->close();

$result = $mysql->query("SELECT @is_owned AS isOwned");
$row = $result->fetch_assoc();
$isOwned = $row['isOwned'];

if (!$isOwned) {
    http_response_code(403); // Доступ запрещен
    exit();
}

// Удаляем фильмы из подборки через процедуру
$deleteFilms = $mysql->prepare("CALL DeleteFilmsFromCollection(?)");
$deleteFilms->bind_param('i', $collectionId);
$deleteFilms->execute();
$deleteFilms->close();

// Удаляем саму подборку через процедуру
$deleteCollection = $mysql->prepare("CALL DeleteCollection(?)");
$deleteCollection->bind_param('i', $collectionId);
$deleteCollection->execute();
$deleteCollection->close();

http_response_code(200); // Успешно
exit();
