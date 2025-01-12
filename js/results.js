document.addEventListener("DOMContentLoaded", () => {
  const animatedElements = document.querySelectorAll(
    ".film-cards-container, .recommendations-title, .no-results, .attempt-title, .showButton, .collection-name"
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

// Скрипт для показа всех попыток
function showAllAttempts() {
  document.getElementById('all-attempts').style.display = 'block';
  document.getElementById('show-all-button').style.display = 'none';
  document.getElementById("collapse-button").style.display = "inline-block"; // Показываем кнопку "Свернуть все"
}

// Функция для сворачивания всех попыток
function collapseAttempts() {
  document.getElementById("all-attempts").style.display = "none";
  document.getElementById("show-all-button").style.display = "inline-block";
  document.getElementById("collapse-button").style.display = "none";
}

function openCollectionModal(filmId) {
  const modal = document.getElementById('collectionModal');
  modal.style.display = 'block';
  document.getElementById('selectedFilmId').value = filmId;
}

function closeCollectionModal() {
  const modal = document.getElementById('collectionModal');
  modal.style.display = 'none';
}
