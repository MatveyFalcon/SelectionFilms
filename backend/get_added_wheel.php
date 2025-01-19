<?php
function getAddedFilms($mysql, $userId) {
    $addedFilms = [];

    // Проверяем, передан ли корректный ID пользователя
    if ($userId) {
        // Подготовка SQL-запроса для получения ID фильмов из коллекций пользователя
        $query = $mysql->prepare("
            SELECT DISTINCT film_id 
            FROM collection_films 
            WHERE collection_id IN (
                SELECT id FROM collections WHERE user_id = ?
            )
        ");

        // Привязываем параметр user_id
        $query->bind_param('i', $userId);

        // Выполняем запрос
        $query->execute();

        // Получаем результаты
        $result = $query->get_result();

        // Формируем массив ID фильмов
        while ($row = $result->fetch_assoc()) {
            $addedFilms[] = $row['film_id'];
        }

        // Освобождаем ресурсы
        $query->close();
    }

    return $addedFilms;
}
?>
