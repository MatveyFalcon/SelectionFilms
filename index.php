<?php
require 'db.php';

// Получение данных пользователя из сессии
$userId = $_SESSION['user'] ?? null;

if ($userId) {
  // Получение кластера пользователя
  $userQuery = $mysql->prepare("SELECT Cluster FROM users WHERE id = ?");
  $userQuery->bind_param('i', $userId);
  $userQuery->execute();
  $userResult = $userQuery->get_result();
  $userCluster = $userResult->fetch_assoc()['Cluster'];

  if ($userCluster) {
    // Получение фильмов соответствующего кластера
    $filmQuery = $mysql->prepare("
            SELECT 
                `id` AS film_id, 
                `Название фильма`, 
                `Аннотация`, 
                `Вид Фильма`, 
                `Продолжительность демонстрации, часы`, 
                `Продолжительность демонстрации, минуты`, 
                `Количество серий` 
            FROM films 
            WHERE Cluster = ?
        ");
    $filmQuery->bind_param('i', $userCluster);
    $filmQuery->execute();
    $filmResult = $filmQuery->get_result();
    $films = $filmResult->fetch_all(MYSQLI_ASSOC);
  } else {
    $films = [];
  }
} else {
  $films = [];
}

if ($userId) {
  // Получение максимального значения score для пользователя
  $testQuery = $mysql->prepare("SELECT MAX(attempt_number) AS max_score FROM testresult WHERE id = ?");
  $testQuery->bind_param('i', $userId);
  $testQuery->execute();
  $testResult = $testQuery->get_result();
  $testScore = $testResult->fetch_assoc()['max_score'] ?? 0; // Если данных нет, значение будет 0
}

?>

<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Подборка фильмов</title>
  <link rel="stylesheet" href="styles/styles.css" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="js/main.js"></script>
</head>

<body>
  <div class="banner">
    <img src="images/Фон.jpg" alt="Фон" class="background" />
    <div class="container">
      <img src="images/МS.svg" alt="Логотип" class="logo" />
      <div class="sections">
        <a href="#testing" class="section-link">Тестирование</a>
        <a href="#recommendations" class="section-link">Рекомендации</a>
        <a href="#genre-wheel" class="section-link">“Колесо” жанров</a>
      </div>
      <img src="images/Заголовок.svg" alt="Подборка фильмов" class="text" />
      <a href="#testing" class="arrow_a">
        <div class="arrow1">
          <img src="images/arrow-down.svg" alt="Вниз" class="arrow-icon" />
        </div>
      </a>
      <div class="auth-buttons">
        <?php if (isset($_SESSION['user'])): ?>
          <a href="logout.php" class="login-button">Выйти</a>
          <a href="cabinet.php" class="register-button">Личный кабинет</a>
        <?php else: ?>
          <a href="login.php" class="login-button">Войти</a>
          <a href="register.php" class="register-button">Регистрация</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <section id="testing">
    <div class="container">
      <div class="white-block">
        <h1>ТЕСТИРОВАНИЕ</h1>
        <div class="plashka">
          <h2>Найди свой идеальный фильм!</h2>
        </div>
        <div class="line-text">
          <img src="images/Line.svg" alt="Линия" class="line" />
          <p>
            Пройдите тест, чтобы получить подборку фильмов, идеально <br />
            подходящих вашим вкусам. Укажите предпочтения по жанру, <br />
            возрастному рейтингу, году выпуска, стране производства <br />
            и продолжительности фильма.
          </p>
        </div>
        <img src="images/Group 6.png" alt="Фильмы" class="groupFilms" />
        <?php if (!$userId): ?>
          <!-- Пользователь не авторизован -->
          <p class="text-login">Войдите, чтобы пройти тестирование!</p>
          <div class="center">
            <a href="login.php" class="login-promt-button">Войти</a>
          </div>
        <?php elseif ($testScore > 0): ?>
          <!-- Пользователь прошел тест хотя бы раз -->
          <div>
            <div class="center1">
              <a href="testing.php" class="test-button">Пройти тестирование</a>
            </div>
            <p class="text-login1">Вы прошли тестирование <?= $testScore ?> раз(а).</p>
            <div class="center1">
              <div class="check-rst">
                <a href="results.php" class="check-button">Посмотреть результаты!</a>
              </div>
            </div>
          </div>
        <?php else: ?>
          <!-- Пользователь авторизован, но не прошел тест -->
          <div class="center1">
            <a href="testing.php" class="test-button">Пройти тестирование</a>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </section>
  <div class="white-block1"></div>

  <section id="recommendations">
    <?php include 'recommendations.php'; ?>
  </section>

</body>

</html>