<?php
require 'backend/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем ID пользователя
    $userId = $_SESSION['user'];

    // Получение последнего номера попытки с помощью процедуры GetLastAttempt
    $stmt = $mysql->prepare("CALL GetLastAttempt(?, @lastAttempt)");
    $stmt->bind_param('i', $userId);
    $stmt->execute();

    // Получаем значение @lastAttempt
    $result = $mysql->query("SELECT @lastAttempt AS lastAttempt");
    $attemptNumber = 1; // Значение по умолчанию
    if ($row = $result->fetch_assoc()) {
        $attemptNumber = (int)$row['lastAttempt'] + 1; // Увеличиваем номер попытки
    }

    // Определяем данные для вставки
    $vid_filma = $_POST['vid_filma'];
    if ($vid_filma === 'film') {
        $cluster = (int)$_POST['film_cluster'];
        $vid_filma = $_POST['film_vid_filma'];
        $strana = (int)$_POST['film_strana'];
        $god = (int)$_POST['film_god'];
        $prodolzhitelnost = (int)$_POST['prodolzhitelnost'];
        $kolichestvo_seriy = 0;
    } elseif ($vid_filma === 'serial') {
        $cluster = (int)$_POST['serial_cluster'];
        $vid_filma = $_POST['serial_vid_filma'];
        $strana = (int)$_POST['serial_strana'];
        $god = (int)$_POST['serial_god'];
        $kolichestvo_seriy = (int)$_POST['kolichestvo_seriy'];
        $prodolzhitelnost = 0;
    }

    // Вызов процедуры InsertTestResult
    $stmt = $mysql->prepare("CALL InsertTestResult(?, ?, ?, ?, ?, ?, ?, ?, @success)");
    $stmt->bind_param(
        'iiisiiii',
        $userId,
        $attemptNumber,
        $cluster,
        $vid_filma,
        $strana,
        $god,
        $kolichestvo_seriy,
        $prodolzhitelnost
    );

    if ($stmt->execute()) {
        // Получаем значение @success
        $result = $mysql->query("SELECT @success AS success");
        $success = $result->fetch_assoc()['success'];

        if ($success) {
            header("Location: index.php");
            exit();
        } else {
            echo "Ошибка: Не удалось сохранить данные.";
        }
    } else {
        echo "Ошибка: " . $stmt->error;
    }
}
?>


<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/testing.css" />
    <title>Прохождение теста</title>
</head>

<body>
    <div class="header">
        <div class="container">
            <a href="index.php" class="logo-link">
                <img src="images/icon-my-index.svg" alt="Логотип" class="logo" />
            </a>
            <a href="index.php" class="back-button">Назад</a>
        </div>
    </div>
    <div class="content">
        <h1 class="text-center">ТЕСТИРОВАНИЕ</h1>
        <form method="POST" action="testing.php">
            <!-- Вопрос 1 -->
            <div id="question1">
                <label for="type_choice" class="form-label">1. Это фильм или сериал?</label>
                <select id="type_choice" name="vid_filma" class="form-select" required>
                    <option value="" selected disabled>Выберите вариант</option>
                    <option value="film">Фильм</option>
                    <option value="serial">Сериал</option>
                </select>
            </div>

            <!-- Вопросы для фильма -->
            <div id="film_questions" class="d-none">
                <div id="film_question1">
                    <label for="prodolzhitelnost" class="form-label">2. Выберите продолжительность фильма:</label>
                    <select id="prodolzhitelnost" name="prodolzhitelnost" class="form-select">
                        <option value="" selected disabled>Выберите вариант</option>
                        <option value="1">От 30 минут до 1 часа</option>
                        <option value="2">От 1 часа до 2 часов</option>
                        <option value="3">От 2 часов до 3 часов</option>
                        <option value="4">От 3 часов</option>
                    </select>
                </div>
                <div id="film_question2" class="d-none">
                    <label for="film_cluster" class="form-label">3. Выберите жанр фильма:</label>
                    <select id="film_cluster" name="film_cluster" class="form-select">
                        <option value="" selected disabled>Выберите вариант</option>
                        <option value="1">Документальный</option>
                        <option value="2">Анимационные, до 18 лет</option>
                        <option value="3">Анимационные, 18+</option>
                        <option value="4">Ужасы/Триллеры, 18+</option>
                        <option value="5">Ужасы/Триллеры, до 18 лет</option>
                        <option value="6">Комедии, 18+</option>
                        <option value="7">Комедии, до 18 лет</option>
                        <option value="8">Мелодрамы/Драмы, 18+</option>
                        <option value="9">Мелодрамы/Драмы, до 18 лет</option>
                        <option value="10">Сериалы</option>
                        <option value="11">Научно-популярные</option>
                        <option value="12">Боевики, 18+</option>
                        <option value="13">Боевики, до 18 лет</option>
                        <option value="14">Прочее</option>
                    </select>
                </div>
                <div id="film_question3" class="d-none">
                    <label for="film_vid_filma" class="form-label">4. Выберите вид фильма:</label>
                    <select id="film_vid_filma" name="film_vid_filma" class="form-select">
                        <option value="" selected disabled>Выберите вариант</option>
                        <option value="Художественный">Художественный</option>
                        <option value="Документальный">Документальный</option>
                        <option value="Анимационный">Анимационный</option>
                        <option value="Научно-популярный">Научно-популярный</option>
                        <option value="Прочее">Прочее</option>
                        <option value="Кинопериодика">Кинопериодика</option>
                        <option value="Музыкально-развлекательный">Музыкально-развлекательный</option>
                    </select>
                </div>
                <div id="film_question4" class="d-none">
                    <label for="film_strana" class="form-label">5. Выберите страну производства фильма:</label>
                    <select id="film_strana" name="film_strana" class="form-select">
                        <option value="" selected disabled>Выберите вариант</option>
                        <option value="1">Отечественные</option>
                        <option value="2">Зарубежные</option>
                    </select>
                </div>
                <div id="film_question5" class="d-none">
                    <label for="film_god" class="form-label">6. Выберите год производства фильма:</label>
                    <select id="film_god" name="film_god" class="form-select">
                        <option value="" selected disabled>Выберите вариант</option>
                        <option value="1">До 2000</option>
                        <option value="2">От 2000 до 2018</option>
                        <option value="3">С 2018</option>
                    </select>
                </div>
            </div>

            <!-- Вопросы для сериала -->
            <div id="serial_questions" class="d-none">
                <div id="serial_question1">
                    <label for="kolichestvo_seriy" class="form-label">2. Выберите количество серий:</label>
                    <select id="kolichestvo_seriy" name="kolichestvo_seriy" class="form-select">
                        <option value="" selected disabled>Выберите вариант</option>
                        <option value="1">От 2 до 10 серий</option>
                        <option value="2">От 10 до 50 серий</option>
                        <option value="3">От 50 серий</option>
                    </select>
                </div>
                <div id="serial_question2" class="d-none">
                    <label for="serial_cluster" class="form-label">3. Выберите жанр сериала:</label>
                    <select id="serial_cluster" name="serial_cluster" class="form-select">
                        <option value="" selected disabled>Выберите вариант</option>
                        <option value="1">Документальный</option>
                        <option value="2">Анимационные, до 18 лет</option>
                        <option value="3">Анимационные, 18+</option>
                        <option value="4">Ужасы/Триллеры, 18+</option>
                        <option value="5">Ужасы/Триллеры, до 18 лет</option>
                        <option value="6">Комедии, 18+</option>
                        <option value="7">Комедии, до 18 лет</option>
                        <option value="8">Мелодрамы/Драмы, 18+</option>
                        <option value="9">Мелодрамы/Драмы, до 18 лет</option>
                        <option value="10">Сериалы</option>
                        <option value="11">Научно-популярные</option>
                        <option value="12">Боевики, 18+</option>
                        <option value="13">Боевики, до 18 лет</option>
                        <option value="14">Прочее</option>
                    </select>
                </div>
                <div id="serial_question3" class="d-none">
                    <label for="serial_vid_filma" class="form-label">4. Выберите вид сериала:</label>
                    <select id="serial_vid_filma" name="serial_vid_filma" class="form-select">
                        <option value="" selected disabled>Выберите вариант</option>
                        <option value="Художественный">Художественный</option>
                        <option value="Документальный">Документальный</option>
                        <option value="Анимационный">Анимационный</option>
                        <option value="Научно-популярный">Научно-популярный</option>
                        <option value="Прочее">Прочее</option>
                        <option value="Кинопериодика">Кинопериодика</option>
                        <option value="Музыкально-развлекательный">Музыкально-развлекательный</option>
                    </select>
                </div>
                <div id="serial_question4" class="d-none">
                    <label for="serial_strana" class="form-label">5. Выберите страну производства сериала:</label>
                    <select id="serial_strana" name="serial_strana" class="form-select">
                        <option value="" selected disabled>Выберите вариант</option>
                        <option value="1">Отечественные</option>
                        <option value="2">Зарубежные</option>
                    </select>
                </div>
                <div id="serial_question5" class="d-none">
                    <label for="serial_god" class="form-label">6. Выберите год производства сериала:</label>
                    <select id="serial_god" name="serial_god" class="form-select">
                        <option value="" selected disabled>Выберите вариант</option>
                        <option value="1">До 2000</option>
                        <option value="2">От 2000 до 2018</option>
                        <option value="3">С 2018</option>
                    </select>
                </div>
            </div>

            <div style="text-align: center;">
                <button type="submit" class="d-none" id="submitButton">Завершить тестирование</button>
            </div>
        </form>
    </div>
    <footer>
        <div class="containerfot">

            <div class="footer-container">
                <div class="footer-section">
                    <h4>Источник открытых данных:</h4>
                    <p>
                        <a href="https://opendata.mkrf.ru/opendata/7705851331-register_movies" target="_blank" rel="noopener noreferrer">
                            https://opendata.mkrf.ru/opendata/<br>7705851331-register_movies
                        </a>
                    </p>
                </div>
                <div class="footer-section">
                    <h4>Контакты:</h4>
                    <p>Email: <a href="mailto:matveyfalcon@gmail.com">matveyfalcon@gmail.com</a></p>
                    <p>Телефон: <a href="tel:+79851856978">+7 985 185 69 78</a></p>
                </div>
                <div style="margin-right: 0px;">
                    <div class="footer-section">
                        <h4>Я в соцсетях:</h4>
                        <div class="social-icons">
                            <a href="https://vk.com/sokolstylz" target="_blank" aria-label="VK">
                                <img src="images/vk.svg" alt="VK">
                            </a>
                            <a href="https://t.me/sokolstylz" target="_blank" aria-label="Telegram">
                                <img src="images/tg.svg" alt="Telegram">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Подборка фильмов. Все права защищены.</p>
            </div>
        </div>
    </footer>

    <script src="js/testing.js"></script>
</body>

</html>