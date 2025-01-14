<?php
require 'db.php';

if (isset($_POST['film_id']) && isset($_SESSION['user'])) {
    $filmId = intval($_POST['film_id']);
    $userId = intval($_SESSION['user']);
    
    // Запрос для получения подборок, содержащих указанный фильм
    $query = $mysql->prepare("
        SELECT c.id, c.name 
        FROM collections c
        INNER JOIN collection_films cf ON c.id = cf.collection_id
        WHERE c.user_id = ? AND cf.film_id = ?
    ");
    $query->bind_param('ii', $userId, $filmId);
    $query->execute();
    $result = $query->get_result();
    
    // Формируем HTML для выбора подборок
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['name']) . '</option>';
        }
    } else {
        echo '<option disabled>Фильм отсутствует в подборках</option>';
    }

    $query->close();
}
?>
