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
            <img src="images/icon-my-index.svg" alt="Логотип" class="logo" />
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
            $checkUser = $mysql->prepare("CALL CheckUserExists(?, @userExists)");
            $checkUser->bind_param('s', $username);
            $checkUser->execute();

            $resultCheck = $mysql->query("SELECT @userExists AS userExists");
            $userExists = $resultCheck->fetch_assoc()['userExists'];

            if ($userExists) {
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

                // Добавление пользователя
                $addUser = $mysql->prepare("CALL AddUser(?, ?, ?, ?, ?, @success)");
                $addUser->bind_param('ssisi', $username, $password, $age, $preferences, $cluster);
                $addUser->execute();

                $resultAdd = $mysql->query("SELECT @success AS success");
                $success = $resultAdd->fetch_assoc()['success'];

                if ($success) {
                    echo "<p class='success'>Регистрация успешна! <a href='login.php'>Войдите</a> для продолжения.</p>";
                } else {
                    echo "<p class='error'>Ошибка регистрации.</p>";
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
</body>

</html>