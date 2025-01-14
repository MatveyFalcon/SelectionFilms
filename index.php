<?php
require 'backend/functions.php';
?>

<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Подборка фильмов</title>
  <link rel="stylesheet" href="styles/styles.css" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="js/collections.js"></script>
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
          <a href="backend/logout.php" class="login-button">Выйти</a>
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
    <div class="container">
      <h2 class="recommendations-title">РЕКОМЕНДАЦИИ</h2>
      <?php if (!$userId || empty($films)): ?>
        <div class="login-section">
          <img src="images/Group 7.svg" alt="Рекомендации" class="login-img">
          <p class="login-prompt">Войдите, чтобы посмотреть рекомендации!</p>
          <a href="login.php" class="login-promt-button">Войти</a>
        </div>
      <?php endif; ?>
      <?php
      require 'backend/get_added_films.php'; // Подключаем новый файл

      $addedFilms = getAddedFilms($mysql, $userId); // Получаем добавленные фильмы
      ?>

      <div class="film-cards-container">
        <?php if ($userId && !empty($films)): ?>
          <?php foreach ($films as $film): ?>
            <div class="film-card" style="display: none;">
              <!-- Заглушка изображения -->
              <img src="images/Заглушка.svg" alt="Заглушка" style="pointer-events: none;">

              <div class="film-details">
                <h3 class="film-title"><?= htmlspecialchars($film['Название фильма']) ?></h3>
                <p class="film-genre"><strong>Жанр:</strong> <?= htmlspecialchars($film['Аннотация']) ?></p>
                <p class="film-type"><strong>Вид:</strong> <?= htmlspecialchars($film['Вид Фильма']) ?></p>
                <?php if ($userCluster == 10): ?>
                  <p class="film-series"><strong>Серии:</strong> <?= htmlspecialchars($film['Количество серий']) ?></p>
                <?php else: ?>
                  <p class="film-duration"><strong>Длительность:</strong> <?= htmlspecialchars($film['Продолжительность демонстрации, часы']) ?> ч <?= htmlspecialchars($film['Продолжительность демонстрации, минуты']) ?> мин</p>
                <?php endif; ?>
                <div class="heart-icon">
                  <img
                    src="images/<?= in_array($film['film_id'], $addedFilms) ? 'heartZaliv.svg' : 'heartContr.svg' ?>"
                    alt="Добавить в подборку"
                    id="heart-<?= $film['film_id'] ?>"
                    class="heart-icon-image"
                    onclick="toggleHeart(<?= $film['film_id'] ?>)">
                </div>


              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <?php if (!empty($films)): ?>
        <button id="load-more" class="show-more-button">Показать еще</button>
      <?php endif; ?>

      <div id="collectionModal" class="modal">
        <div class="modal-content">
          <span class="close" onclick="closeCollectionModal()">&times;</span>
          <form action="backend/add_to_collection.php" method="POST">
            <input type="hidden" name="film_id" id="selectedFilmId">
            <label for="collection">Выберите подборку:</label>
            <select name="collection_id" id="collection">
              <option value="" selected disabled>Загрузка подборок...</option>
            </select>
            <label for="new_collection">Или создайте новую:</label>
            <input type="text" name="new_collection" id="new_collection" placeholder="Название подборки">
            <div class="button-add">
              <button type="button" onclick="addFilmToCollection()">Добавить</button>
            </div>
          </form>
        </div>
      </div>

      <div id="removeModal" class="modal">
        <div class="modal-content">
          <span class="close" onclick="closeRemoveModal()">&times;</span>
          <form action="backend/remove_from_collection.php" method="POST">
            <input type="hidden" name="film_id" id="removeFilmId">
            <label for="remove_collection">Выберите подборку:</label>
            <select name="collection_id" id="remove_collection">
              <!-- Здесь будут загружены подборки -->
            </select>
            <div class="button-add">
              <button type="button" onclick="removeFilmFromCollection()">Удалить</button>
            </div>
          </form>
        </div>
      </div>


    </div>
  </section>
  
  <script src="js/main.js"></script>
</body>

</html>