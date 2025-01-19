<?php
require 'db.php';

header('Content-Type: application/json');

// Проверяем, авторизован ли пользователь
if (isset($_SESSION['user'])) {
    $userId = $_SESSION['user'];

    try {
        // Вызов хранимой процедуры
        $collectionsQuery = $mysql->prepare("CALL GetUserCollections(?)");
        $collectionsQuery->bind_param('i', $userId);
        $collectionsQuery->execute();

        // Получение результата из процедуры
        $collectionsResult = $collectionsQuery->get_result();

        $collections = [];
        while ($row = $collectionsResult->fetch_assoc()) {
            $collections[] = [
                'id' => $row['id'],
                'name' => $row['name']
            ];
        }

        // Закрытие результата и запроса
        $collectionsResult->free();
        $collectionsQuery->close();

        echo json_encode(['collections' => $collections]);
    } catch (mysqli_sql_exception $e) {
        // Обработка ошибок
        http_response_code(500); // Внутренняя ошибка сервера
        echo json_encode(['error' => 'Ошибка на сервере: ' . $e->getMessage()]);
    }
} else {
    http_response_code(403); // Доступ запрещен
    echo json_encode(['error' => 'Пользователь не авторизован']);
}
?>
