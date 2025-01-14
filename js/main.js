document.addEventListener("DOMContentLoaded", () => {
  const animatedElements = document.querySelectorAll(
    ".text, .line, .arrow1, .sections, .plashka, .white-block h1, .line-text p, .groupFilms, .test-button, .text-login, .recommendations-title, .login-promt-button, .login-img, .login-prompt, .film-cards-container, .check-rst, .text-login1, .recommendations-title, .no-results, .attempt-title"
  );

  const observerOptions = {
    root: null, // Следим за видимостью относительно viewport
    rootMargin: "0px", // Без отступов
    threshold: 0.2, // Срабатываем, когда элемент виден хотя бы на 20%
  };

  const observer = new IntersectionObserver((entries, observer) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        // Добавляем стиль для запуска анимации
        entry.target.style.animationPlayState = "running";
        entry.target.style.visibility = "visible"; // Убедимся, что элемент виден
        observer.unobserve(entry.target); // Прекращаем наблюдение для анимированного элемента
      }
    });
  }, observerOptions);

  animatedElements.forEach((element) => {
    // Проверяем, что элемент существует и видим для запуска наблюдателя
    if (element) {
      element.style.animationPlayState = "paused"; // Останавливаем анимации по умолчанию
      element.style.visibility = "hidden"; // Скрываем элемент, пока он не станет видимым
      observer.observe(element); // Добавляем элемент в наблюдатель
    } else {
      console.warn("Element not found or could not be animated:", element);
    }
  });
});

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

/*document.getElementById("spin-button").addEventListener("click", function () {
  const wheel = document.getElementById("genre-wheel");
  const segments = wheel.querySelectorAll(".wheel-segment span");
  const totalSegments = segments.length;

  // Random selection
  const randomIndex = Math.floor(Math.random() * totalSegments);
  const selectedSegment = segments[randomIndex];
  const clusterId = selectedSegment.dataset.cluster;

  // Spin animation
  const rotation = randomIndex * (360 / totalSegments) + 360 * 3; // 3 full spins
  wheel.style.transition = "transform 3s ease-out";
  wheel.style.transform = `rotate(-${rotation}deg)`;

  // Load movies after animation ends
  setTimeout(() => {
    fetch(`fetch_movies.php?cluster=${clusterId}`)
      .then((response) => response.json())
      .then((data) => {
        const cardsContainer = document.getElementById("film-cards");
        cardsContainer.innerHTML = "";
        data.forEach((film) => {
          cardsContainer.innerHTML += `
            <div class="film-card">
              <img src="images/Заглушка.svg" alt="Заглушка" />
              <div class="film-details">
                <h3>${film["Название фильма"]}</h3>
                <p><strong>Жанр:</strong> ${film["Аннотация"]}</p>
                <p><strong>Вид:</strong> ${film["Вид Фильма"]}</p>
                <p><strong>Длительность:</strong> ${film["Продолжительность демонстрации, часы"]} ч ${film["Продолжительность демонстрации, минуты"]} мин</p>
              </div>
            </div>
          `;
        });
      });
  }, 3000);
});*/
