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
        <a href="#genre-wheel-section" class="section-link">“Колесо” жанров</a>
      </div>
      <img src="images/Заголовок.svg" alt="Подборка фильмов" class="text" />
      <a href="#testing" class="arrow_a">
        <div class="arrow1">
          <img src="images/arrow-down.svg" alt="Вниз" class="arrow-icon" />
        </div>
      </a>
      <div class="auth-buttons" id="auth-buttons">
        <!-- Кнопки будут загружены динамически -->
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
        <div id="testing-content">
          <!-- Контент будет загружен динамически -->
        </div>
      </div>
    </div>
  </section>

  <div class="white-block1"></div>

  <section id="recommendations">
    <?php
    require 'backend/functions.php';
    ?>

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

  <section id="genre-wheel-section">
  <div class="container">
    <h2 class="wheel-title">КОЛЕСО ЖАНРОВ</h2>
    <div id="genre-wheel-container">
      <div id="genre-wheel">
        <!-- Секции с текстами жанров -->
        <div id="genre-0" style="transform: rotate(0deg);">
          <span>Документальный</span>
        </div>
        <div id="genre-1" style="transform: rotate(25.71deg);">
          <span>Анимационные, до 18 лет</span>
        </div>
        <div id="genre-2" style="transform: rotate(51.42deg);">
          <span>Анимационные, 18+</span>
        </div>
        <div id="genre-3" style="transform: rotate(77.13deg);">
          <span>Ужасы/Триллеры, 18+</span>
        </div>
        <div id="genre-4" style="transform: rotate(102.84deg);">
          <span>Ужасы/Триллеры, до 18 лет</span>
        </div>
        <div id="genre-5" style="transform: rotate(128.55deg);">
          <span>Комедии, 18+</span>
        </div>
        <div id="genre-6" style="transform: rotate(154.26deg);">
          <span>Комедии, до 18 лет</span>
        </div>
        <div id="genre-7" style="transform: rotate(179.97deg);">
          <span>Мелодрамы/Драмы, 18+</span>
        </div>
        <div id="genre-8" style="transform: rotate(205.68deg);">
          <span>Мелодрамы/Драмы, до 18 лет</span>
        </div>
        <div id="genre-9" style="transform: rotate(231.39deg);">
          <span>Сериалы</span>
        </div>
        <div id="genre-10" style="transform: rotate(257.1deg);">
          <span>Научно-популярные</span>
        </div>
        <div id="genre-11" style="transform: rotate(282.81deg);">
          <span>Боевики, 18+</span>
        </div>
        <div id="genre-12" style="transform: rotate(308.52deg);">
          <span>Боевики, до 18 лет</span>
        </div>
        <div id="genre-13" style="transform: rotate(334.23deg);">
          <span>Прочее</span>
        </div>
      </div>
      <div id="wheel-pointer"></div>
      <button id="spin-button">Крутить</button>
    </div>
    <div class="filmsWheel">
      <div id="film-cards-container" class="film-cards-container"></div>
    </div>
  </div>
</section>






  <script src="js/main.js"></script>
</body>

</html>