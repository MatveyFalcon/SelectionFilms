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
        <img src="images/icon-my-index.svg" alt="Логотип" class="logo" />
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

        // Вызов хранимой процедуры
        $stmt = $mysql->prepare("CALL GetUserByLogin(?)");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
          $user = $result->fetch_assoc();
          if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user['id'];
            header("Location: index.php");
            exit();
          } else {
            echo "<p class='error'>Неверный пароль!</p>";
          }
        } else {
          echo "<p class='error'>Пользователь не найден!</p>";
        }

        // Освобождение результата и завершение процедуры
        $result->free();
        while ($mysql->more_results()) {
          $mysql->next_result();
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