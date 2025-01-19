<?php
require 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user'];
$filmId = $_POST['film_id'];
$collectionId = $_POST['collection_id'];

// Вызов хранимой процедуры для удаления фильма из подборки
$query = $mysql->prepare("CALL DeleteFilmFromCollection(?, ?)");
$query->bind_param('ii', $collectionId, $filmId);
$query->execute();

http_response_code(200); // Возвращаем успешный код ответа
exit();
