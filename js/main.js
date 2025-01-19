document.addEventListener("DOMContentLoaded", () => {
  const animatedElements = document.querySelectorAll(
    ".text, .line, .arrow1, .sections, .plashka, .white-block h1, .line-text p, .groupFilms, .test-button, .text-login, .recommendations-title, .login-promt-button, .login-img, .login-prompt, .film-cards-container, .check-rst, .text-login1, .recommendations-title, .no-results, .attempt-title, .wheel-title, .genre-wheel-container"
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

// Получаем элемент кнопки
const scrollToTopButton = document.getElementById("scroll-to-top");

// Флаг для предотвращения лишних операций
let isVisible = false;

// Функция для показа кнопки
function showButton() {
  if (!isVisible) {
    scrollToTopButton.style.display = "block"; // Устанавливаем display: block
    setTimeout(() => {
      scrollToTopButton.classList.add("visible"); // Добавляем класс для анимации
    }, 500);
    isVisible = true;
  }
}

// Функция для скрытия кнопки
function hideButton() {
  if (isVisible) {
    scrollToTopButton.classList.remove("visible"); // Убираем класс для анимации
    setTimeout(() => {
      scrollToTopButton.style.display = "none"; // Устанавливаем display: none после завершения анимации
    }, 500); // Совпадает с длительностью анимации
    isVisible = false;
  }
}

// Слушаем прокрутку страницы
window.addEventListener("scroll", () => {
  if (window.scrollY > 200) {
    showButton();
  }
  if (window.scrollY == 0) {
    hideButton();
  }
});

// Обработчик клика по кнопке
scrollToTopButton.addEventListener("click", () => {
  window.scrollTo({
    top: 0,
    behavior: "smooth",
  });

  // Принудительное скрытие кнопки после нажатия
  hideButton();

  // Обрабатываем быстрый скролл вниз после клика
  setTimeout(() => {
    if (window.scrollY > 200) {
      showButton();
    }
  }, 400); // Ждем завершения плавной прокрутки
});

document.addEventListener("DOMContentLoaded", () => {
  const allFilms = [...document.querySelectorAll(".film-card")];
  const filmContainer = document.querySelector(".film-cards-container");
  const loadMoreButton = document.getElementById("load-more");

  const maxFilmsToShow = 36; // Максимальное количество фильмов
  const initialFilmsToShow = 6; // Количество фильмов по умолчанию

  let collapseButton = null; // Кнопка "Свернуть все"
  let collapseButtonContainer = null; // Контейнер для кнопки

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

    // Создаем контейнер для кнопки и добавляем его в DOM
    collapseButtonContainer = document.createElement("div");
    collapseButtonContainer.className = "collapse-button-container";

    collapseButton = document.createElement("button");
    collapseButton.textContent = "Свернуть все";
    collapseButton.className = "collapse-button";

    // Добавляем кнопку в контейнер
    collapseButtonContainer.appendChild(collapseButton);

    // Добавляем контейнер с кнопкой после контейнера фильмов
    filmContainer.parentNode.appendChild(collapseButtonContainer);

    // Устанавливаем родительскому контейнеру стили для центрирования содержимого
    collapseButtonContainer.style.display = "flex";
    collapseButtonContainer.style.justifyContent = "center";
    collapseButtonContainer.style.alignItems = "center";

    collapseButton.addEventListener("click", () => {
      showFilms(initialFilmsToShow); // Возвращаем отображение первых фильмов
      collapseButtonContainer.remove(); // Удаляем кнопку "Свернуть все"
      collapseButton = null; // Обнуляем ссылку на кнопку
      loadMoreButton.style.display = "block"; // Показываем кнопку "Показать еще"

      // Плавно прокручиваем к секции "recommendations"
      const recommendationsSection = document.getElementById("recommendations");
      recommendationsSection.scrollIntoView({ behavior: "smooth" });
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

document.addEventListener("DOMContentLoaded", () => {
  fetch("backend/auth_status.php")
    .then((response) => response.json())
    .then((data) => {
      const authButtons = document.getElementById("auth-buttons");
      if (data.isLoggedIn) {
        authButtons.innerHTML = `
          <a href="backend/logout.php" class="login-button">Выйти</a>
          <a href="cabinet.php" class="register-button">Личный кабинет</a>
        `;
      } else {
        authButtons.innerHTML = `
          <a href="login.php" class="login-button">Войти</a>
          <a href="register.php" class="register-button">Регистрация</a>
        `;
      }
    })
    .catch((error) => {
      console.error("Ошибка при загрузке кнопок авторизации:", error);
    });
});

document.addEventListener("DOMContentLoaded", () => {
  const testingContent = document.getElementById("testing-content");
  const tilte = document.querySelector(".recommendations-title");

  // Загружаем данные с сервера
  fetch("backend/testing_data.php")
    .then((response) => response.json())
    .then((data) => {
      const { isLoggedIn, testScore } = data;

      let contentHTML = "";

      if (!isLoggedIn) {
        // Пользователь не авторизован
        contentHTML = `
          <p class="text-login">Войдите, чтобы пройти тестирование!</p>
          <div class="center">
            <a href="login.php" class="login-promt-button">Войти</a>
          </div>
        `;
        tilte.style.marginTop = "234px";
      } else if (testScore > 0) {
        // Пользователь прошел тест хотя бы раз
        contentHTML = `
          <div>
            <div class="center1">
              <a href="testing.php" class="test-button">Пройти тестирование</a>
            </div>
            <p class="text-login1">Вы прошли тестирование ${testScore} раз(а).</p>
            <div class="center1">
              <div class="check-rst">
                <a href="results.php" class="check-button">Посмотреть результаты!</a>
              </div>
            </div>
          </div>
        `;
        tilte.style.marginTop = "223px";
      } else {
        // Пользователь авторизован, но не прошел тест
        contentHTML = `
          <div class="center1">
            <a href="testing.php" class="test-button">Пройти тестирование</a>
          </div>
        `;
        tilte.style.marginTop = "166px";
      }

      // Вставляем HTML-контент
      testingContent.innerHTML = contentHTML;

      // Находим новые элементы для анимации и запускаем их
      const newAnimatedElements = testingContent.querySelectorAll(
        ".login-promt-button, .text-login, .center, .center1, .test-button, .text-login1, .check-rst"
      );

      newAnimatedElements.forEach((element) => {
        element.style.animationPlayState = "paused"; // Останавливаем анимации по умолчанию
        element.style.visibility = "hidden"; // Скрываем элемент, пока он не станет видимым
        observer.observe(element); // Добавляем элемент в наблюдатель
      });
    })
    .catch((error) => {
      console.error("Ошибка загрузки данных тестирования:", error);
      testingContent.innerHTML =
        "<p>Ошибка загрузки данных. Попробуйте позже.</p>";
    });

  // Создаем IntersectionObserver для анимации
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
});

document.addEventListener("DOMContentLoaded", function () {
  const genres = [
    "Документальный",
    "Анимационные, до 18 лет",
    "Анимационные, 18+",
    "Ужасы/Триллеры, 18+",
    "Ужасы/Триллеры, до 18 лет",
    "Комедии, 18+",
    "Комедии, до 18 лет",
    "Мелодрамы/Драмы, 18+",
    "Мелодрамы/Драмы, до 18 лет",
    "Сериалы",
    "Научно-популярные",
    "Боевики, 18+",
    "Боевики, до 18 лет",
    "Прочее",
  ];

  const wheel = document.getElementById("genre-wheel");
  const spinButton = document.getElementById("spin-button");
  const pointer = document.getElementById("wheel-pointer");
  const resultTextContainer = document.querySelector(".resultText");
  const numSections = genres.length;
  const anglePerSection = 360 / numSections;

  const initialOffset = -142;
  let currentRotation = initialOffset;
  let isSpinning = false;

  spinButton.addEventListener("click", async function () {
    if (isSpinning) return;

    isSpinning = true;
    spinButton.disabled = true;

    const randomIndex = Math.floor(Math.random() * genres.length);
    const spins = Math.floor(Math.random() * 2) + 2;
    const randomOffset = Math.random() * anglePerSection;
    const finalAngle = randomIndex * anglePerSection + randomOffset;
    const totalRotation = spins * 360 + finalAngle;

    currentRotation = currentRotation + totalRotation;

    wheel.style.transition = "transform 10s cubic-bezier(0.2, 1, 0.5, 1)";
    wheel.style.transform = `rotate(${currentRotation}deg)`;

    const pointerAnimation = setInterval(() => {
      pointer.style.transform = "translateX(-50%) rotate(175deg)";
      setTimeout(() => {
        pointer.style.transform = "translateX(-50%) rotate(175deg)";
      }, 50);
    }, 50);

    await new Promise((resolve) => setTimeout(resolve, 10000));
    clearInterval(pointerAnimation);
    pointer.style.transform = "translateX(-50%) rotate(180deg)";

    const normalizedAngle =
      (360 - ((currentRotation - initialOffset) % 360)) % 360;
    const genreIndex = Math.floor(
      ((normalizedAngle + anglePerSection / 2) % 360) / anglePerSection
    );

    const resultGenre = genres[genreIndex];

    // Удаляем предыдущий результат, если он существует
    const existingResult = document.getElementById("result-text");
    if (existingResult) {
      existingResult.remove();
    }

    // Создаём новый элемент для отображения результата
    const resultText = document.createElement("div");
    resultText.id = "result-text";
    resultText.classList.add("fade-in-up");
    resultText.textContent = `Вам выпал жанр: ${resultGenre}!`;

    resultTextContainer.appendChild(resultText);

    // Создание контейнера с карточками
    let filmCardsContainer = document.querySelector(".film-cards-container1");
    if (filmCardsContainer) {
      // Удаляем старый контейнер, если он существует
      filmCardsContainer.remove();
    }

    // Создаем новый контейнер
    filmCardsContainer = document.createElement("div");
    filmCardsContainer.id = "film-cards-container1";
    filmCardsContainer.classList.add("film-cards-container1");
    filmCardsContainer.style.opacity = 0; // Начальное состояние для анимации
    document.querySelector(".filmsWheel").appendChild(filmCardsContainer);

    // Добавляем анимацию контейнера
    setTimeout(() => {
      filmCardsContainer.classList.add("zoom-in-animation");
      filmCardsContainer.style.opacity = 1; // Устанавливаем видимость
    }, 50);

    // Загрузка фильмов
    const cluster = genreIndex + 1;
    const response = await fetch(`backend/genre_wheel.php?cluster=${cluster}`);
    const films = await response.json();
    const limitedFilms = films.slice(0, 6);

    limitedFilms.forEach((film) => {
      const card = document.createElement("div");
      card.classList.add("film-card");

      // Создание HTML-контента карточки фильма
      card.innerHTML = `
        <img src="images/Заглушка.svg" alt="Заглушка" style="pointer-events:none">
        <div class="film-details">
          <h3 class="film-title">${film["Название фильма"]}</h3>
          <p class="film-genre"><strong>Жанр:</strong> ${film["Аннотация"]}</p>
          <p class="film-type"><strong>Вид:</strong> ${film["Вид Фильма"]}</p>
          ${
            film["Количество серий"] > 1
              ? `<p class="film-series"><strong>Серии:</strong> ${film["Количество серий"]}</p>`
              : `<p class="film-duration"><strong>Длительность:</strong> ${film["Продолжительность демонстрации, часы"]} ч ${film["Продолжительность демонстрации, минуты"]} мин</p>`
          }
        </div>
        <div class="heart-icon">
          <img
            src="images/${
              addedFilms.includes(film["film_id"])
                ? "heartZaliv.svg"
                : "heartContr.svg"
            }"
            alt="Добавить в подборку"
            id="heart-${film["film_id"]}"
            class="heart-icon-image"
            onclick="toggleHeart(${film["film_id"]})">
        </div>`;
      filmCardsContainer.appendChild(card);

      // Анимация появления карточек
      filmCardsContainer.style.animation = `zoomIn 1.2s ease-in-out`;
    });

    isSpinning = false;
    spinButton.disabled = false;
  });
});

//const initialOffset = -141.43; начальное значение
