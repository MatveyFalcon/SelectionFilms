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

if (empty($filmId) || (empty($collectionId) && empty($newCollectionName))) {
    http_response_code(400); // Неправильный запрос
    exit('Недостаточно данных');
}

if ($newCollectionName) {
    // Вызов процедуры для создания новой подборки
    $query = $mysql->prepare("CALL AddCollection(?, ?, @newCollectionId)");
    $query->bind_param('is', $userId, $newCollectionName);
    $query->execute();

    // Получение ID новой подборки
    $result = $mysql->query("SELECT @newCollectionId AS collectionId");
    $row = $result->fetch_assoc();
    $collectionId = $row['collectionId'];
}

// Вызов процедуры для добавления фильма в подборку
$query = $mysql->prepare("CALL AddFilmToCollection(?, ?)");
$query->bind_param('ii', $collectionId, $filmId);
$query->execute();

http_response_code(200); // Возвращаем успешный код ответа
exit();
