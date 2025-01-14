<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Регистрация</title>
    <link rel="stylesheet" href="styles/register.css" />
</head>

<body>
    <div class="header">
        <div class="container">
            <img src="images/МS.svg" alt="Логотип" class="logo" />
            <a href="index.php" class="back-button">Назад</a>
        </div>
    </div>
    <div class="container1 auth-container">
        <h1>Регистрация</h1>
        <?php
        require 'backend/db.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Хэширование пароля
            $age = (int) $_POST['age'];
            $preferences = $_POST['preferences']; // Получаем текст предпочтений из формы

            // Проверка на существующий логин
            $checkUser = $mysql->prepare("SELECT * FROM users WHERE login = ?");
            $checkUser->bind_param('s', $username);
            $checkUser->execute();
            $result = $checkUser->get_result();

            if ($result->num_rows > 0) {
                echo "<p class='error'>Пользователь с таким логином уже существует.</p>";
            } else {
                // Определяем кластер на основе возраста и предпочтений
                $cluster = null;
                if ($preferences === 'Анимационные' && $age <= 16) {
                    $cluster = 2;
                } elseif ($preferences === 'Анимационные' && $age >= 18) {
                    $cluster = 3;
                } elseif ($preferences === 'Ужасы/Триллеры' && $age >= 18) {
                    $cluster = 4;
                } elseif ($preferences === 'Ужасы/Триллеры' && $age < 18) {
                    $cluster = 5;
                } elseif ($preferences === 'Комедии' && $age >= 18) {
                    $cluster = 6;
                } elseif ($preferences === 'Комедии' && $age < 18) {
                    $cluster = 7;
                } elseif ($preferences === 'Мелодрамы/Драмы' && $age >= 18) {
                    $cluster = 8;
                } elseif ($preferences === 'Мелодрамы/Драмы' && $age < 18) {
                    $cluster = 9;
                } elseif ($preferences === 'Сериалы') {
                    $cluster = 10;
                } elseif ($preferences === 'Научно-популярные') {
                    $cluster = 11;
                } elseif ($preferences === 'Боевики' && $age >= 18) {
                    $cluster = 12;
                } elseif ($preferences === 'Боевики' && $age < 18) {
                    $cluster = 13;
                } elseif ($preferences === 'Прочее') {
                    $cluster = 14;
                } else {
                    $cluster = 1; // По умолчанию Документальный
                }

                // Добавление пользователя в БД
                $stmt = $mysql->prepare("INSERT INTO users (login, password, age, movie_preferences, cluster) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param('ssisi', $username, $password, $age, $preferences, $cluster);

                if ($stmt->execute()) {
                    echo "<p class='success'>Регистрация успешна! <a href='login.php'>Войдите</a> для продолжения.</p>";
                } else {
                    echo "<p class='error'>Ошибка регистрации: " . $stmt->error . "</p>";
                }
            }
        }
        ?>

        <form action="register.php" method="POST" class="auth-form">
            <label for="username">Логин</label>
            <input type="text" id="username" name="username" required />
            <label for="password">Пароль</label>
            <input type="password" id="password" name="password" required />
            <label for="age">Возраст</label>
            <input type="number" id="age" name="age" min="0" max="120" required />
            <label for="preferences">Предпочтения по фильму</label>
            <select id="preferences" name="preferences" required>
                <option value="Документальные">Документальные</option>
                <option value="Анимационные">Анимационные</option>
                <option value="Ужасы/Триллеры">Ужасы/Триллеры</option>
                <option value="Комедии">Комедии</option>
                <option value="Мелодрамы/Драмы">Мелодрамы/Драмы</option>
                <option value="Сериалы">Сериалы</option>
                <option value="Научно-популярные">Научно-популярные</option>
                <option value="Боевики">Боевики</option>
                <option value="Другое">Другое</option>
            </select>
            <button type="submit" class="auth-button">Зарегистрироваться</button>
        </form>
    </div>
</body>

</html>