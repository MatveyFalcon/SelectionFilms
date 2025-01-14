<?php
require 'db.php';

// Получение данных пользователя из сессии
$userId = $_SESSION['user'] ?? null;

if ($userId) {
  // Получение кластера пользователя
  $userQuery = $mysql->prepare("SELECT Cluster FROM users WHERE id = ?");
  $userQuery->bind_param('i', $userId);
  $userQuery->execute();
  $userResult = $userQuery->get_result();
  $userCluster = $userResult->fetch_assoc()['Cluster'];

  if ($userCluster) {
    // Получение фильмов соответствующего кластера
    $filmQuery = $mysql->prepare("
            SELECT 
                `id` AS film_id, 
                `Название фильма`, 
                `Аннотация`, 
                `Вид Фильма`, 
                `Продолжительность демонстрации, часы`, 
                `Продолжительность демонстрации, минуты`, 
                `Количество серий` 
            FROM films 
            WHERE Cluster = ?
        ");
    $filmQuery->bind_param('i', $userCluster);
    $filmQuery->execute();
    $filmResult = $filmQuery->get_result();
    $films = $filmResult->fetch_all(MYSQLI_ASSOC);
  } else {
    $films = [];
  }
} else {
  $films = [];
}

if ($userId) {
  // Получение максимального значения score для пользователя
  $testQuery = $mysql->prepare("SELECT MAX(attempt_number) AS max_score FROM testresult WHERE id = ?");
  $testQuery->bind_param('i', $userId);
  $testQuery->execute();
  $testResult = $testQuery->get_result();
  $testScore = $testResult->fetch_assoc()['max_score'] ?? 0; // Если данных нет, значение будет 0
}

?>