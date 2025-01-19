<?php
require 'db.php';

if (isset($_POST['film_id']) && isset($_SESSION['user'])) {
    $filmId = intval($_POST['film_id']);
    $userId = intval($_SESSION['user']);
    
    // Вызов хранимой процедуры
    $query = $mysql->prepare("CALL GetCollectionsByFilm(?, ?)");
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
