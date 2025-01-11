<?php
require 'db.php';

// Проверяем авторизацию
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user'];

// Получение списка всех попыток пользователя
$attemptsQuery = $mysql->prepare("SELECT DISTINCT attempt_number FROM testresult WHERE id = ? ORDER BY attempt_number ASC");
$attemptsQuery->bind_param('i', $userId);
$attemptsQuery->execute();
$attemptsResult = $attemptsQuery->get_result();

$attempts = [];
while ($row = $attemptsResult->fetch_assoc()) {
    $attempts[] = $row['attempt_number'];
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Рекомендации</title>
    <link rel="stylesheet" href="styles/results.css">
</head>

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
        <h1 class="recommendations-title">Результаты</h1>

        <?php if (count($attempts) > 0): ?>
            <div class="attempts-container">
                <?php foreach ($attempts as $attempt): ?>
                    <?php
                    // Вызов процедуры для каждой попытки
                    $filmQuery = $mysql->prepare("CALL GetFilmRecommendations(?, ?)");
                    $filmQuery->bind_param('ii', $userId, $attempt);
                    $filmQuery->execute();
                    $filmResult = $filmQuery->get_result();
                    ?>


                    <h2 class="attempt-title">Попытка <?= htmlspecialchars($attempt) ?></h2>

                    <?php if ($filmResult && $filmResult->num_rows > 0): ?>
                        <div class="film-cards-container">
                            <?php while ($row = $filmResult->fetch_assoc()): ?>
                                <div class="film-card">
                                    <img src="images/Заглушка1.svg" alt="Заглушка" style="pointer-events: none;">
                                    <h2 class="film-title"><?= htmlspecialchars($row['Название фильма']) ?></h2>
                                    <p class="film-duration"><strong>Жанр:</strong> <?= htmlspecialchars($row['Аннотация']) ?></p>
                                    <p class="film-duration"><strong>Вид:</strong> <?= htmlspecialchars($row['Вид Фильма']) ?></p>
                                    <p class="film-duration"><strong>Длительность:</strong> <?= htmlspecialchars($row['Длительность'] ?? 'Не указана') ?></p>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p class="no-results">Результаты для попытки <?= htmlspecialchars($attempt) ?> не найдены.</p>
                    <?php endif; ?>

                    <?php $filmQuery->close(); // Закрытие курсора 
                    ?>

                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="no-results">У вас пока нет результатов.</p>
        <?php endif; ?>
    </div>
</body>

</html>