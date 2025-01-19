document.addEventListener("DOMContentLoaded", () => {
  const animatedElements = document.querySelectorAll(
    ".film-cards-container, .recommendations-title, .no-results, .attempt-title, .showButton, .collection-name, .no-films, .delete-collection"
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

// Скрипт для показа всех попыток
function showAllAttempts() {
  document.getElementById("all-attempts").style.display = "block";
  document.getElementById("show-all-button").style.display = "none";
  document.getElementById("collapse-button").style.display = "inline-block"; // Показываем кнопку "Свернуть все"
}

// Функция для сворачивания всех попыток
function collapseAttempts() {
  document.getElementById("all-attempts").style.display = "none";
  document.getElementById("show-all-button").style.display = "inline-block";
  document.getElementById("collapse-button").style.display = "none";
  window.scrollTo({
    top: 0,
    behavior: "smooth",
  });
}
