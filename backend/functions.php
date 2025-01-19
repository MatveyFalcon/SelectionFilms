<?php
require 'db.php';

// Получение данных пользователя из сессии
$userId = $_SESSION['user'] ?? null;

if ($userId) {
  // Получение кластера пользователя с использованием процедуры
  $userQuery = $mysql->prepare("CALL GetUserCluster(?)");
  $userQuery->bind_param('i', $userId);
  $userQuery->execute();
  $userResult = $userQuery->get_result();
  $userCluster = $userResult->fetch_assoc()['Cluster'];
  
  // Прокачиваем результат, чтобы избежать ошибки "Commands out of sync"
  $userQuery->next_result();

  if ($userCluster) {
    // Получение фильмов соответствующего кластера с использованием процедуры
    $filmQuery = $mysql->prepare("CALL GetFilmsByCluster(?)");
    $filmQuery->bind_param('i', $userCluster);
    $filmQuery->execute();
    $filmResult = $filmQuery->get_result();
    $films = $filmResult->fetch_all(MYSQLI_ASSOC);
    
    // Прокачиваем результат после выполнения запроса
    $filmQuery->next_result();
  } else {
    $films = [];
  }
} else {
  $films = [];
}

if ($userId) {
  // Получение максимального значения score для пользователя с использованием процедуры
  $testQuery = $mysql->prepare("CALL GetMaxTestScore(?)");
  $testQuery->bind_param('i', $userId);
  $testQuery->execute();
  $testResult = $testQuery->get_result();
  $testScore = $testResult->fetch_assoc()['max_score'] ?? 0; // Если данных нет, значение будет 0
  
  // Прокачиваем результат после выполнения запроса
  $testQuery->next_result();
}
?>
