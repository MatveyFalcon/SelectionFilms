<?php
// backend/genre_wheel.php
require 'db.php';

header('Content-Type: application/json');

// Получаем кластер из запроса
$cluster = $_GET['cluster'] ?? null;

if ($cluster) {
    // Использование хранимой процедуры для получения фильмов
    $filmQuery = $mysql->prepare("CALL GetFilmsByClusterLimited(?)");
    $filmQuery->bind_param('i', $cluster);
    $filmQuery->execute();
    $filmResult = $filmQuery->get_result();
    $films = $filmResult->fetch_all(MYSQLI_ASSOC);

    // Возвращаем результат в формате JSON
    echo json_encode($films);
} else {
    // Если кластер не передан, возвращаем пустой массив
    echo json_encode([]);
}
?>
