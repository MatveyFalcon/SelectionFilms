<?php
require 'backend/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}

$userId = $_SESSION['user'];
$query = $mysql->prepare("
    SELECT 
        c.id AS collection_id, 
        c.name AS collection_name, 
        f.id AS film_id, 
        f.`Название фильма` AS film_name, 
        f.`Аннотация` AS film_annotation, 
        f.`Вид Фильма` AS film_type, 
        CASE 
            WHEN `f`.`Количество серий` > 1 THEN 
                CONCAT(`f`.`Количество серий`, ' серий')
            ELSE 
                CONCAT(`f`.`Продолжительность демонстрации, часы`, ' ч ', `f`.`Продолжительность демонстрации, минуты`, ' мин')
        END AS `film_duration`
    FROM collections c
    LEFT JOIN collection_films cf ON c.id = cf.collection_id
    LEFT JOIN films f ON cf.film_id = f.id
    WHERE c.user_id = ?
    ORDER BY c.created_at DESC, cf.added_at DESC
");
$query->bind_param('i', $userId);
$query->execute();
$result = $query->get_result();
$collections = [];
while ($row = $result->fetch_assoc()) {
    $collectionId = $row['collection_id'];
    if (!isset($collections[$collectionId])) {
        $collections[$collectionId] = [
            'name' => $row['collection_name'],
            'films' => []
        ];
    }
    if ($row['film_id']) {
        $collections[$collectionId]['films'][] = [
            'id' => $row['film_id'],
            'name' => $row['film_name'],
            'annotation' => $row['film_annotation'],
            'type' => $row['film_type'],
            'duration' => $row['film_duration']
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
    <link rel="stylesheet" href="styles/cabinet.css">
</head>

<body>
    <div id="scroll-to-top" alt="Scroll to top" style="display: none;"></div>
    <div class="header">
        <div class="container">
            <a href="index.php" class="logo-link">
                <img src="images/МS.svg" alt="Логотип" class="logo" />
            </a>
            <a href="index.php" class="back-button">Назад</a>
        </div>
    </div>

    <?php
    require 'backend/get_added_films.php';

    $addedFilms = getAddedFilms($mysql, $userId);
    ?>

    <main class="content">
        <h1 class="recommendations-title">МОИ ПОДБОРКИ</h1>
        <section class="collections">
            <?php if (!empty($collections)): ?>
                <?php foreach ($collections as $collectionId => $collection): ?>
                    <div class="collection" id="collection-<?= htmlspecialchars($collectionId) ?>">
                        <div class="collection-header">
                            <h3 class="collection-name"><?= htmlspecialchars($collection['name']) ?></h3>
                            <span class="delete-collection" onclick="deleteCollection(<?= $collectionId ?>)">×</span>
                        </div>
                        <?php if (!empty($collection['films'])): ?>
                            <div class="film-cards-container">
                                <?php foreach ($collection['films'] as $film): ?>
                                    <?php if (isset($film['id'])):
                                    ?>
                                        <div class="film-card" id="film-card-<?= htmlspecialchars($film['id']) ?>">
                                            <img src="images/Заглушка1.svg" alt="Заглушка" style="pointer-events: none;">
                                            <h2 class="film-title"><?= htmlspecialchars($film['name']) ?></h2>
                                            <p class="film-duration"><strong>Жанр:</strong> <?= htmlspecialchars($film['annotation'] ?? 'Не указан') ?></p>
                                            <p class="film-duration"><strong>Вид:</strong> <?= htmlspecialchars($film['type'] ?? 'Не указан') ?></p>
                                            <p class="film-duration"><strong>Длительность:</strong> <?= htmlspecialchars($film['duration'] ?? 'Не указана') ?></p>
                                            <div class="heart-icon">
                                                <img
                                                    src="images/<?= in_array($film['id'], $addedFilms) ? 'heartZaliv.svg' : 'heartContr.svg' ?>"
                                                    alt="Удалить из подборки"
                                                    id="heart-<?= $film['id'] ?>"
                                                    class="heart-icon-image"
                                                    onclick="removeFilmFromCollectionCabinet(<?= $film['id'] ?>, <?= $collectionId ?>)">
                                            </div>

                                        </div>
                                    <?php else: ?>
                                        <p class="no-film-warning">Ошибка: Данные о фильме отсутствуют.</p>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="no-films">Подборка пуста</p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-collections">У вас пока нет подборок.</p>
            <?php endif; ?>
        </section>
    </main>

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
    <script src="js/results.js"></script>
    <script src="js/collections.js"></script>
</body>

</html>