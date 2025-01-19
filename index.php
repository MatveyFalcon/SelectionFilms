<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CокТека — Подборка фильмов или сериалов на любой вкус!</title>
  <link rel="stylesheet" href="styles/styles.css" />
  <link rel="icon" href="images/icon-my.svg" type="image/svg+xml">
</head>

<body>
  <div id="scroll-to-top" alt="Scroll to top" style="display: none;"></div>

  <div class="banner">
    <img src="images/Фон.jpg" alt="Фон" class="background" />
    <div class="container">
      <img src="images/icon-my-index.svg" alt="Логотип" class="logo" />
      <div class="sections">
        <a href="#testing" class="section-link">Тестирование</a>
        <a href="#recommendations" class="section-link">Рекомендации</a>
        <a href="#genre-wheel-section" class="section-link">“Колесо” жанров</a>
        <a href="#footer" class="section-link">Контакты</a>
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
      require 'backend/get_added_films.php';

      $addedFilms = getAddedFilms($mysql, $userId);
      ?>

      <div class="film-cards-container">
        <?php if ($userId && !empty($films)): ?>
          <?php foreach ($films as $film): ?>
            <div class="film-card" style="display:none">
              <img src="images/Заглушка.svg" alt="Заглушка" style="pointer-events:none">

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
    <script>
      const addedFilms = <?= json_encode($addedFilms) ?>;
    </script>
    <div class="container">
      <h2 class="wheel-title">КОЛЕСО ЖАНРОВ</h2>
      <div id="genre-wheel-container" class="genre-wheel-container">
        <div id="genre-wheel">
          <div id="genre-0" style="transform: rotate(6deg);">
            <span>Документальный</span>
          </div>
          <div id="genre-1" style="transform: rotate(31.71deg);">
            <span>Анимационные</span>
          </div>
          <div id="genre-2" style="transform: rotate(57.42deg);">
            <span>Анимационные 18+</span>
          </div>
          <div id="genre-3" style="transform: rotate(83.13deg);">
            <span>Ужасы/Триллеры 18+</span>
          </div>
          <div id="genre-4" style="transform: rotate(108.84deg);">
            <span>Ужасы/Триллеры</span>
          </div>
          <div id="genre-5" style="transform: rotate(134.55deg);">
            <span>Комедии, 18+</span>
          </div>
          <div id="genre-6" style="transform: rotate(160.26deg);">
            <span>Комедии</span>
          </div>
          <div id="genre-7" style="transform: rotate(185.97deg);">
            <span>Мелодрамы/Драмы 18+</span>
          </div>
          <div id="genre-8" style="transform: rotate(211.68deg);">
            <span>Мелодрамы/Драмы</span>
          </div>
          <div id="genre-9" style="transform: rotate(237.39deg);">
            <span>Сериалы</span>
          </div>
          <div id="genre-10" style="transform: rotate(263.1deg);">
            <span>Научно-популярные</span>
          </div>
          <div id="genre-11" style="transform: rotate(288.81deg);">
            <span>Боевики 18+</span>
          </div>
          <div id="genre-12" style="transform: rotate(314.52deg);">
            <span>Боевики</span>
          </div>
          <div id="genre-13" style="transform: rotate(340.23deg);">
            <span>Прочее</span>
          </div>
        </div>
        <div id="wheel-pointer"></div>
        <button id="spin-button">Крутить!</button>
      </div>
      <div class="resultText"></div>
      <div class="filmsWheel"></div>
    </div>
  </section>
  <footer id="footer">
    <div class="container">
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
  <script src="js/collections.js"></script>
  <script src="js/main.js"></script>
</body>

</html>