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
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if (!empty($films)): ?>
            <button id="load-more" class="show-more-button">Показать еще</button>
        <?php endif; ?>
    </div>
</section>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const allFilms = [...document.querySelectorAll(".film-card")];
        const filmContainer = document.querySelector(".film-cards-container");
        const loadMoreButton = document.getElementById("load-more");

        const maxFilmsToShow = 36; // Максимальное количество фильмов
        const initialFilmsToShow = 6; // Количество фильмов по умолчанию

        let collapseButton = null; // Кнопка "Свернуть все"

        // Функция показа фильмов
        function showFilms(count) {
            allFilms.forEach((film, index) => {
                film.style.display = index < count ? "block" : "none";
            });
        }

        // Изначально показываем только первые фильмы
        showFilms(initialFilmsToShow);

        // Обработчик для кнопки "Показать еще"
        if (loadMoreButton) {
            loadMoreButton.addEventListener("click", () => {
                showFilms(maxFilmsToShow); // Показываем максимум фильмов
                loadMoreButton.style.display = "none"; // Скрываем кнопку "Показать еще"
                addCollapseButton(); // Добавляем кнопку "Свернуть все"
            });
        }

        // Функция добавления кнопки "Свернуть все"
        function addCollapseButton() {
            if (collapseButton) return; // Проверяем, что кнопка уже существует

            collapseButton = document.createElement("button");
            collapseButton.textContent = "Свернуть все";
            collapseButton.className = "collapse-button";

            // Добавляем кнопку "Свернуть все" непосредственно в тело документа
            document.body.appendChild(collapseButton);

            collapseButton.addEventListener("click", () => {
                showFilms(initialFilmsToShow); // Возвращаем отображение первых фильмов
                collapseButton.remove(); // Удаляем кнопку "Свернуть все"
                collapseButton = null; // Обнуляем ссылку на кнопку
                loadMoreButton.style.display = "block"; // Показываем кнопку "Показать еще"
            });

            // Добавляем анимацию для кнопки "Свернуть все"
            observer.observe(collapseButton);
        }

        // Настройки инициализации анимации для кнопок
        const observerOptions = {
            root: null,
            rootMargin: "0px",
            threshold: 0.2,
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = "running";
                } else {
                    entry.target.style.animationPlayState = "paused";
                }
            });
        }, observerOptions);

        // Инициализация анимации для кнопки "Показать еще"
        if (loadMoreButton) {
            loadMoreButton.style.animationPlayState = "paused";
            observer.observe(loadMoreButton);
        }
    });
</script>