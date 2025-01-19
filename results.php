<?php
require 'backend/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user'];

// Получение последней попытки пользователя
$lastAttemptQuery = $mysql->prepare("CALL GetLastAttempt(?, @lastAttempt)");
$lastAttemptQuery->bind_param('i', $userId);
$lastAttemptQuery->execute();
$lastAttemptQuery->close();

// Извлечение результата из переменной
$lastAttemptResult = $mysql->query("SELECT @lastAttempt AS last_attempt");
$lastAttempt = $lastAttemptResult->fetch_assoc()['last_attempt'] ?? null;

if ($lastAttempt) {
    // Проверка наличия записей в таблице user_attempt_results
    $checkResultsQuery = $mysql->prepare("CALL CheckUserAttemptResults(?, ?, @resultCount)");
    $checkResultsQuery->bind_param('ii', $userId, $lastAttempt);
    $checkResultsQuery->execute();
    $checkResultsQuery->close();

    $checkResults = $mysql->query("SELECT @resultCount AS result_count");
    $resultCount = $checkResults->fetch_assoc()['result_count'];

    if ($resultCount == 0) {
        // Вызов процедуры для вставки фильмов
        $insertFilmsQuery = $mysql->prepare("CALL InsertFilmRecommendations(?, ?)");
        $insertFilmsQuery->bind_param('ii', $userId, $lastAttempt);
        $insertFilmsQuery->execute();
        $insertFilmsQuery->close();
    }

    // Получение данных из таблицы user_attempt_results
    $savedFilmsQuery = $mysql->prepare("CALL GetSavedFilms(?, ?)");
    $savedFilmsQuery->bind_param('ii', $userId, $lastAttempt);
    $savedFilmsQuery->execute();
    $savedFilmsResult = $savedFilmsQuery->get_result();
    $savedFilms = $savedFilmsResult->fetch_all(MYSQLI_ASSOC);
    $savedFilmsQuery->close();
}

// Получение всех попыток пользователя
$attemptsQuery = $mysql->prepare("CALL GetUserAttempts(?)");
$attemptsQuery->bind_param('i', $userId);
$attemptsQuery->execute();
$attemptsResult = $attemptsQuery->get_result();
$attempts = [];

while ($row = $attemptsResult->fetch_assoc()) {
    $attempts[] = $row['attempt_number'];
}
$attemptsQuery->close();
?>


<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Рекомендации</title>
    <link rel="stylesheet" href="styles/results.css">
</head>

<?php
require 'backend/get_added_films.php';

$addedFilms = getAddedFilms($mysql, $userId);
?>

<body>
    <div id="scroll-to-top" alt="Scroll to top" style="display: none;"></div>
    <div class="header">
        <div class="container">
            <a href="index.php" class="logo-link">
                <img src="images/icon-my-index.svg" alt="Логотип" class="logo" />
            </a>
            <a href="index.php" class="back-button">Назад</a>
        </div>
    </div>

    <div class="content">
        <h1 class="recommendations-title">РЕЗУЛЬТАТЫ</h1>

        <?php if (!empty($savedFilms)): ?>
            <h2 class="attempt-title">Текущий результат (Попытка <?= htmlspecialchars($lastAttempt) ?>)</h2>
            <div class="film-cards-container">
                <?php foreach ($savedFilms as $film): ?>
                    <div class="film-card">
                        <img src="images/Заглушка1.svg" alt="Заглушка" style="pointer-events: none;">
                        <h2 class="film-title"><?= htmlspecialchars($film['film_name']) ?></h2>
                        <p class="film-duration"><strong>Жанр:</strong> <?= htmlspecialchars($film['genre']) ?></p>
                        <p class="film-duration"><strong>Вид:</strong> <?= htmlspecialchars($film['type']) ?></p>
                        <p class="film-duration"><strong>Длительность:</strong> <?= htmlspecialchars($film['duration'] ?? 'Не указана') ?></p>
                        <div class="heart-icon">
                            <img
                                src="images/<?= in_array($film['film_id'], $addedFilms) ? 'heartZaliv.svg' : 'heartContr.svg' ?>"
                                alt="Добавить в подборку"
                                id="heart-<?= $film['film_id'] ?>"
                                class="heart-icon-image"
                                onclick="toggleHeart(<?= $film['film_id'] ?>)">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <h2 class="attempt-title">Текущий результат (Попытка <?= htmlspecialchars($lastAttempt) ?>)</h2>
            <p class="no-results">Результаты для текущей попытки не найдены.</p>
        <?php endif; ?>

        <?php if (count($attempts) > 1): ?>
            <div class="centre">
                <button id="show-all-button" class="showButton" onclick="showAllAttempts()">Показать все попытки</button>
            </div>

            <div id="all-attempts" style="display: none;">
                <?php foreach ($attempts as $attempt): ?>
                    <?php if ($attempt != $lastAttempt): ?>
                        <?php
                        $previousResultsQuery = $mysql->prepare("CALL GetFilmsByAttempt(?, ?)");
                        $previousResultsQuery->bind_param('ii', $userId, $attempt);
                        $previousResultsQuery->execute();
                        $previousResults = $previousResultsQuery->get_result();
                        $previousFilms = $previousResults->fetch_all(MYSQLI_ASSOC);
                        $previousResultsQuery->close();
                        ?>


                        <h2 class="attempt-title">Попытка <?= htmlspecialchars($attempt) ?></h2>

                        <?php if (!empty($previousFilms)): ?>
                            <div class="film-cards-container">
                                <?php foreach ($previousFilms as $film): ?>
                                    <div class="film-card">
                                        <img src="images/Заглушка1.svg" alt="Заглушка" style="pointer-events: none;">
                                        <h2 class="film-title"><?= htmlspecialchars($film['film_name']) ?></h2>
                                        <p class="film-duration"><strong>Жанр:</strong> <?= htmlspecialchars($film['genre']) ?></p>
                                        <p class="film-duration"><strong>Вид:</strong> <?= htmlspecialchars($film['type']) ?></p>
                                        <p class="film-duration"><strong>Длительность:</strong> <?= htmlspecialchars($film['duration'] ?? 'Не указана') ?></p>
                                        <div class="heart-icon">
                                            <img
                                                src="images/<?= in_array($film['film_id'], $addedFilms) ? 'heartZaliv.svg' : 'heartContr.svg' ?>"
                                                alt="Добавить в подборку"
                                                id="heart-<?= $film['film_id'] ?>"
                                                class="heart-icon-image"
                                                onclick="toggleHeart(<?= $film['film_id'] ?>)">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="no-results">Результаты для попытки <?= htmlspecialchars($attempt) ?> не найдены.</p>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <div class="centre">
                <button id="collapse-button" class="showButton" onclick="collapseAttempts()" style="display: none;">Свернуть все</button>
            </div>
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
    <script src="js/collections.js"></script>
    <script src="js/results.js"></script>
</body>

</html>