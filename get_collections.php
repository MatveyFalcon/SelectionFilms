<?php
// Получаем подборки пользователя из базы данных
require 'db.php'; // подключаем файл с подключением к БД

// Проверяем, авторизован ли пользователь
if (isset($_SESSION['user'])) {
    $userId = $_SESSION['user'];

    // Запрос для получения подборок
    $collectionsQuery = $mysql->prepare("SELECT id, name FROM collections WHERE user_id = ?");
    $collectionsQuery->bind_param('i', $userId);
    $collectionsQuery->execute();
    $collectionsResult = $collectionsQuery->get_result();

    // Проверка наличия подборок
    if ($collectionsResult->num_rows > 0) {
        while ($row = $collectionsResult->fetch_assoc()) {
            echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['name']) . '</option>';
        }
    } else {
        echo '<option disabled>У вас нет подборок</option>';
    }

    // Закрытие соединения с БД
    $collectionsQuery->close();
}
