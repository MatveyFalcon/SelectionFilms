<?php
require 'backend/db.php';


// Проверяем авторизацию
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user'];

// Получение последней попытки пользователя
$lastAttemptQuery = $mysql->prepare("SELECT MAX(attempt_number) AS last_attempt FROM testresult WHERE id = ?");
$lastAttemptQuery->bind_param('i', $userId);
$lastAttemptQuery->execute();
$lastAttemptResult = $lastAttemptQuery->get_result();
$lastAttempt = $lastAttemptResult->fetch_assoc()['last_attempt'] ?? null;

// Если есть попытка
if ($lastAttempt) {
    // Проверяем, есть ли результаты в сессии для этой попытки
    if (!isset($_SESSION['film_results'][$lastAttempt])) {
        // Вызов процедуры для последней попытки
        $filmQuery = $mysql->prepare("CALL GetFilmRecommendations(?, ?)");
        $filmQuery->bind_param('ii', $userId, $lastAttempt);
        $filmQuery->execute();
        $filmResult = $filmQuery->get_result();

        // Сохраняем результаты в сессию
        $_SESSION['film_results'][$lastAttempt] = [];
        while ($row = $filmResult->fetch_assoc()) {
            $_SESSION['film_results'][$lastAttempt][] = $row;
        }

        $filmQuery->close(); // Закрытие курсора
    }

    // Получаем данные из сессии
    $savedFilms = $_SESSION['film_results'][$lastAttempt];
}

// Получение всех попыток пользователя
$attemptsQuery = $mysql->prepare("SELECT DISTINCT attempt_number FROM testresult WHERE id = ? ORDER BY attempt_number DESC");
$attemptsQuery->bind_param('i', $userId);
$attemptsQuery->execute();
$attemptsResult = $attemptsQuery->get_result();
$attempts = [];

while ($row = $attemptsResult->fetch_assoc()) {
    $attempts[] = $row['attempt_number'];
}

$attemptsQuery->close(); // Закрываем запрос


?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Рекомендации</title>
    <link rel="stylesheet" href="styles/results.css">
    <script src="js/results.js"></script>
    <script src="js/collections.js"></script>
</head>

<?php
require 'backend/get_added_films.php'; // Подключаем новый файл

$addedFilms = getAddedFilms($mysql, $userId); // Получаем добавленные фильмы
?>

<body>
    <div class="header">
        <div class="container">
            <a href="index.php" class="logo-link">
                <img src="images/МS.svg" alt="Логотип" class="logo" />
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
                        <h2 class="film-title"><?= htmlspecialchars($film['Название фильма']) ?></h2>
                        <p class="film-duration"><strong>Жанр:</strong> <?= htmlspecialchars($film['Аннотация']) ?></p>
                        <p class="film-duration"><strong>Вид:</strong> <?= htmlspecialchars($film['Вид Фильма']) ?></p>
                        <p class="film-duration"><strong>Длительность:</strong> <?= htmlspecialchars($film['Длительность'] ?? 'Не указана') ?></p>
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
                        // Проверяем, есть ли результаты в сессии для этой попытки
                        if (!isset($_SESSION['film_results'][$attempt])) {
                            // Вызов процедуры для текущей попытки
                            $filmQuery = $mysql->prepare("CALL GetFilmRecommendations(?, ?)");
                            $filmQuery->bind_param('ii', $userId, $attempt);
                            $filmQuery->execute();
                            $filmResult = $filmQuery->get_result();

                            // Сохраняем результаты в сессию
                            $_SESSION['film_results'][$attempt] = [];
                            while ($row = $filmResult->fetch_assoc()) {
                                $_SESSION['film_results'][$attempt][] = $row;
                            }

                            $filmQuery->close(); // Закрытие курсора
                        }

                        // Получаем данные из сессии
                        $savedFilms = $_SESSION['film_results'][$attempt];
                        ?>

                        <h2 class="attempt-title">Попытка <?= htmlspecialchars($attempt) ?></h2>

                        <?php if (!empty($savedFilms)): ?>
                            <div class="film-cards-container">
                                <?php foreach ($savedFilms as $film): ?>
                                    <div class="film-card">
                                        <img src="images/Заглушка1.svg" alt="Заглушка" style="pointer-events: none;">
                                        <h2 class="film-title"><?= htmlspecialchars($film['Название фильма']) ?></h2>
                                        <p class="film-duration"><strong>Жанр:</strong> <?= htmlspecialchars($film['Аннотация']) ?></p>
                                        <p class="film-duration"><strong>Вид:</strong> <?= htmlspecialchars($film['Вид Фильма']) ?></p>
                                        <p class="film-duration"><strong>Длительность:</strong> <?= htmlspecialchars($film['Длительность'] ?? 'Не указана') ?></p>
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

</body>

</html>