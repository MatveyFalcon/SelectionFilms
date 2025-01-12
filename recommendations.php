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
                            <button class="add-to-collection" onclick="openCollectionModal(<?= $film['film_id'] ?>)">Добавить в подборку</button>
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
                <form action="add_to_collection.php" method="POST">
                    <input type="hidden" name="film_id" id="selectedFilmId">
                    <label for="collection">Выберите подборку:</label>
                    <select name="collection_id" id="collection">
                        <?php require 'get_collections.php'; ?>
                    </select>
                    <label for="new_collection">Или создайте новую:</label>
                    <input type="text" name="new_collection" id="new_collection" placeholder="Название подборки">
                    <button type="submit">Добавить</button>
                </form>
            </div>
        </div>
    </div>
</section>
