<?php
function getAddedFilms($mysql, $userId) {
    $addedFilms = [];
    if ($userId) {
        // Вызов хранимой процедуры
        $query = $mysql->prepare("CALL GetAddedFilmsByUser(?)");
        $query->bind_param('i', $userId);
        $query->execute();
        
        // Получение результата из процедуры
        $result = $query->get_result();
        while ($row = $result->fetch_assoc()) {
            $addedFilms[] = $row['film_id'];
        }
        
        $query->close(); // Закрываем запрос
    }
    return $addedFilms;
}
?>
