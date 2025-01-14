<?php
require 'db.php';

$cluster = $_GET['cluster'] ?? null;

if ($cluster) {
  $stmt = $mysql->prepare("
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
    LIMIT 6
  ");
  $stmt->bind_param('i', $cluster);
  $stmt->execute();
  $result = $stmt->get_result();
  $films = $result->fetch_all(MYSQLI_ASSOC);

  header('Content-Type: application/json');
  echo json_encode($films);
}
?>
