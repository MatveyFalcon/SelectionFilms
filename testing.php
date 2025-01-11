<?php
require 'db.php';

// Проверка авторизации
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные пользователя
    $userId = $_SESSION['user'];
    $attemptNumber = 1;

    // Получение последнего номера попытки и текущего score для данного пользователя
    $stmt = $mysql->prepare("SELECT MAX(attempt_number) AS last_attempt FROM testresult WHERE id = ?");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $attemptNumber = $row['last_attempt'] + 1; // Увеличиваем номер попытки
    }

    // Сохранение данных из формы
    $cluster = (int)$_POST['cluster'];
    $vid_filma = $_POST['vid_filma'];
    $strana = (int)$_POST['strana'];
    $god = (int)$_POST['god'];
    $kolichestvo_seriy = isset($_POST['kolichestvo_seriy']) ? (int)$_POST['kolichestvo_seriy'] : 0;
    $prodolzhitelnost = isset($_POST['prodolzhitelnost']) ? (int)$_POST['prodolzhitelnost'] : 0;

    // Вставка новой строки с увеличенным score
    $stmt = $mysql->prepare("
        INSERT INTO testresult (id, attempt_number, Cluster, Вид_Фильма, Страна_производства, Год_производства, 
                                 Количество_серий, Продолжительность_демонстрации)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        'iiisisii',
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
        header("Location: index.php"); // Перенаправление после успешной вставки
        exit();
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
    <title>Прохождение теста</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Тестирование предпочтений</h1>
        <form method="POST" action="testing.php">
            <!-- Вопрос 1 -->
            <div class="mb-3">
                <label for="cluster" class="form-label">1. Выберите жанр фильма или сериала и возрастное ограничение:</label>
                <select id="cluster" name="cluster" class="form-select" required>
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

            <!-- Вопрос 2 -->
            <div class="mb-3">
                <label for="vid_filma" class="form-label">2. Выберите вид фильма или сериала:</label>
                <select id="vid_filma" name="vid_filma" class="form-select" required>
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

            <!-- Вопрос 3 -->
            <div class="mb-3">
                <label for="strana" class="form-label">3. Выберите страну производства:</label>
                <select id="strana" name="strana" class="form-select" required>
                    <option value="" selected disabled>Выберите вариант</option>
                    <option value="1">Отечественные</option>
                    <option value="2">Зарубежные</option>
                </select>
            </div>

            <!-- Вопрос 4 -->
            <div class="mb-3">
                <label for="god" class="form-label">4. Выберите год производства:</label>
                <select id="god" name="god" class="form-select" required>
                    <option value="" selected disabled>Выберите вариант</option>
                    <option value="1">До 2000</option>
                    <option value="2">От 2000 до 2018</option>
                    <option value="3">С 2018</option>
                </select>
            </div>

            <!-- Вопрос 5 -->
            <div class="mb-3">
                <label for="type_choice" class="form-label">5. Это фильм или сериал?</label>
                <select id="type_choice" class="form-select" required>
                    <option value="" selected disabled>Выберите вариант</option>
                    <option value="film">Фильм</option>
                    <option value="serial">Сериал</option>
                </select>
            </div>

            <!-- Вопрос 6 -->
            <div class="mb-3 d-none" id="film_question">
                <label for="prodolzhitelnost" class="form-label">Выберите продолжительность фильма:</label>
                <select id="prodolzhitelnost" name="prodolzhitelnost" class="form-select">
                    <option value="1">От 30 минут до 1 часа</option>
                    <option value="2">От 1 часа до 2 часов</option>
                    <option value="3">От 2 часов до 3 часов</option>
                    <option value="4">От 3 часов</option>
                </select>
            </div>

            <!-- Вопрос 7 -->
            <div class="mb-3 d-none" id="serial_question">
                <label for="kolichestvo_seriy" class="form-label">Выберите количество серий:</label>
                <select id="kolichestvo_seriy" name="kolichestvo_seriy" class="form-select">
                    <option value="1">От 2 до 10 серий</option>
                    <option value="2">От 10 до 50 серий</option>
                    <option value="3">От 50 серий</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100">Завершить тестирование</button>
        </form>
    </div>

    <script>
        const typeChoice = document.getElementById('type_choice');
        const filmQuestion = document.getElementById('film_question');
        const serialQuestion = document.getElementById('serial_question');
        const prodolzhitelnost = document.getElementById('prodolzhitelnost');
        const kolichestvoSeriy = document.getElementById('kolichestvo_seriy');

        typeChoice.addEventListener('change', (e) => {
            if (e.target.value === 'film') {
                // Показать вопрос про продолжительность фильма
                filmQuestion.classList.remove('d-none');
                serialQuestion.classList.add('d-none');

                // Установить значение 0 для количества серий
                kolichestvoSeriy.value = 0;
            } else if (e.target.value === 'serial') {
                // Показать вопрос про количество серий
                serialQuestion.classList.remove('d-none');
                filmQuestion.classList.add('d-none');

                // Установить значение 0 для продолжительности демонстрации
                prodolzhitelnost.value = 0;
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>