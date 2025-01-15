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
      } else {
        // Пользователь авторизован, но не прошел тест
        contentHTML = `
          <div class="center1">
            <a href="testing.php" class="test-button">Пройти тестирование</a>
          </div>
        `;
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
  const filmCardsContainer = document.getElementById("film-cards-container");
  const pointer = document.getElementById("wheel-pointer");
  const numSections = genres.length;
  const anglePerSection = 360 / numSections;

  // Начальное смещение для совмещения "Документальный" с указателем
  //const initialOffset = -141.43; начальное значение
  const initialOffset = -141.30;

  // Текущий угол вращения (с учётом начального смещения)
  let currentRotation = initialOffset;

  spinButton.addEventListener("click", async function () {
    const randomIndex = Math.floor(Math.random() * genres.length);

    // Генерация случайного угла для остановки
    const spins = Math.floor(Math.random() * 8) + 8; // От 8 до 15 оборотов
    const randomOffset = Math.random() * anglePerSection; // Смещение внутри сектора
    const finalAngle = randomIndex * anglePerSection + randomOffset;

    // Итоговый угол вращения
    const totalRotation = spins * 360 + finalAngle;

    // Сохраняем текущий угол
    currentRotation = (currentRotation + totalRotation) % 360;

    // Вращение колеса с яркой анимацией и легким масштабированием
    wheel.style.transition = "transform 2s cubic-bezier(0.25, 1, 0.5, 1.5), scale 0.5s ease-in-out";
    wheel.style.transform = `rotate(${currentRotation}deg) scale(1.05)`;

    setTimeout(() => {
      wheel.style.transform = `rotate(${currentRotation}deg) scale(1)`;
    }, 2000);

    // Анимация стрелки при вращении
    pointer.style.transition = "transform 0.1s ease-in-out";

    let isSpinning = true;

    const pointerAnimation = setInterval(() => {
      if (!isSpinning) return clearInterval(pointerAnimation);
      pointer.style.transform = "translateX(-50%) rotate(170deg) scale(1.1)";
      setTimeout(() => {
        pointer.style.transform = "translateX(-50%) rotate(190deg) scale(1.1)";
      }, 50);
    }, 100);


    // Ждём завершения вращения
    await new Promise((resolve) => setTimeout(resolve, 2000));

    // Останавливаем движение стрелки
    isSpinning = false;
    pointer.style.transition = "transform 0.2s ease-out";
    pointer.style.transform = "translateX(-50%) rotate(180deg) scale(1)"; // Возврат в исходное положение


    // Нормализация угла для получения жанра
    const normalizedAngle = (360 - ((currentRotation - initialOffset) % 360)) % 360;
    const genreIndex = Math.floor((normalizedAngle + anglePerSection / 2) % 360 / anglePerSection);

    // Получаем выпавший жанр
    const resultGenre = genres[genreIndex];

    alert(`Выпал жанр: ${resultGenre}`);

    // Загрузка фильмов
    filmCardsContainer.innerHTML = "";
    const cluster = genreIndex + 1; // Кластер совпадает с индексом + 1
    const response = await fetch(`backend/genre_wheel.php?cluster=${cluster}`);
    const films = await response.json();
    const limitedFilms = films.slice(0, 6);
    // Отображение карточек фильмов
    limitedFilms.forEach((film) => {
      const card = document.createElement("div");
      card.classList.add("film-card");
      card.innerHTML = `
        <img src="images/Заглушка.svg" alt="Заглушка">
        <div class="film-details">
          <h3 class="film-title">${film["Название фильма"]}</h3>
          <p class="film-genre"><strong>Жанр:</strong> ${film["Аннотация"]}</p>
          <p class="film-type"><strong>Вид:</strong> ${film["Вид Фильма"]}</p>
          ${
            film["Количество серий"] > 1
              ? `<p class="film-series"><strong>Серии:</strong> ${film["Количество серий"]}</p>`
              : `<p class="film-duration"><strong>Длительность:</strong> ${film["Продолжительность демонстрации, часы"]} ч ${film["Продолжительность демонстрации, минуты"]} мин</p>`
          }
        </div>`;
      filmCardsContainer.appendChild(card);
    });
  });

  // Эффект пульсации кнопки
  const style = document.createElement("style");
  style.innerHTML = `
    @keyframes pulse {
      0% {
        transform: scale(1);
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
      }
      50% {
        transform: scale(1.1);
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.4);
      }
      100% {
        transform: scale(1);
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
      }
    }
  `;
  document.head.appendChild(style);
});