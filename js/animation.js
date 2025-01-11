document.addEventListener("DOMContentLoaded", () => {
  const animatedElements = document.querySelectorAll(
    ".text, .line, .arrow1, .sections, .plashka, .white-block h1, .line-text p, .groupFilms, .test-button, .text-login, .recommendations-title, .login-promt-button, .login-img, .login-prompt, .film-cards-container, .check-rst, .text-login1"
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

