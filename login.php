<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Вход</title>
    <link rel="stylesheet" href="styles/login.css" />
</head>

<body>
    <div class="banner">
        <div class="header">
            <div class="container">
                <img src="images/МS.svg" alt="Логотип" class="logo" />
                <a href="index.php" class="back-button">Назад</a>
            </div>
        </div>
        <div class="container1 auth-container">
            <h1>Вход</h1>
            <?php
            require 'backend/db.php';

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $username = $_POST['username'];
                $password = $_POST['password'];

                // Поиск пользователя в базе данных
                $stmt = $mysql->prepare("SELECT * FROM users WHERE login = ?");
                $stmt->bind_param('s', $username);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 1) {
                    $user = $result->fetch_assoc();
                    if (password_verify($password, $user['password'])) {
                        $_SESSION['user'] = $user['id']; // Сохранение пользователя в сессию

                        

                        header("Location: index.php");
                        exit();
                    } else {
                        echo "<p class='error'>Неверный пароль!</p>";
                    }
                } else {
                    echo "<p class='error'>Пользователь не найден!</p>";
                }
            }
            ?>
            <form action="login.php" method="POST" class="auth-form">
                <label for="username">Логин</label>
                <input type="text" id="username" name="username" required />
                <label for="password">Пароль</label>
                <input type="password" id="password" name="password" required />
                <button type="submit" class="auth-button">Войти</button>
            </form>
        </div>
    </div>
</body>

</html>