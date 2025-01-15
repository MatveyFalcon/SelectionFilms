<?php
// backend/genre_wheel.php
require 'db.php';

header('Content-Type: application/json');

// Получаем кластер из запроса
$cluster = $_GET['cluster'] ?? null;

if ($cluster) {
    // Получение фильмов соответствующего кластера
    $filmQuery = $mysql->prepare("SELECT 
            id AS film_id, 
            `Название фильма`, 
            Аннотация, 
            `Вид Фильма`, 
            `Продолжительность демонстрации, часы`, 
            `Продолжительность демонстрации, минуты`, 
            `Количество серий` 
        FROM films 
        WHERE Cluster = ? 
        LIMIT 6");
    $filmQuery->bind_param('i', $cluster);
    $filmQuery->execute();
    $filmResult = $filmQuery->get_result();
    $films = $filmResult->fetch_all(MYSQLI_ASSOC);

    echo json_encode($films);
} else {
    echo json_encode([]);
}