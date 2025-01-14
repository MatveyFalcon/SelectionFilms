function openCollectionModal(filmId) {
  const modal = document.getElementById("collectionModal");
  modal.style.display = "block";
  document.getElementById("selectedFilmId").value = filmId;
}

function closeCollectionModal() {
  const modal = document.getElementById("collectionModal");
  modal.style.display = "none";
}

function toggleHeart(filmId) {
  const heartIcon = document.getElementById(`heart-${filmId}`);
  const isAdded = heartIcon.src.includes("heartZaliv.svg");

  if (isAdded) {
    // Если фильм уже добавлен, открыть модальное окно для удаления
    openRemoveModal(filmId);
  } else {
    // Если фильм не добавлен, открыть модальное окно для добавления
    openCollectionModal(filmId);
  }
}

function openRemoveModal(filmId) {
  const modal = document.getElementById("removeModal");
  const select = document.getElementById("remove_collection");
  const removeFilmId = document.getElementById("removeFilmId");

  // Установить ID фильма в скрытое поле
  removeFilmId.value = filmId;

  // Очистить предыдущее содержимое выбора
  select.innerHTML = "<option>Загрузка...</option>";

  // Отправить запрос на сервер для получения подборок
  fetch("get_film_collections.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `film_id=${filmId}`,
  })
    .then((response) => response.text())
    .then((data) => {
      select.innerHTML = data; // Заполнить список подборок
    })
    .catch((error) => {
      console.error("Ошибка загрузки данных:", error);
      select.innerHTML = "<option disabled>Ошибка загрузки</option>";
    });

  // Показать модальное окно
  modal.style.display = "block";
}

function closeRemoveModal() {
  const modal = document.getElementById("removeModal");
  modal.style.display = "none";
}

function addFilmToCollection() {
  const filmId = document.getElementById("selectedFilmId").value;
  const collectionId = document.getElementById("collection").value;
  const newCollectionName = document.getElementById("new_collection").value;

  // Проверяем, что выбрана либо существующая подборка, либо введено название новой подборки
  if (!collectionId && !newCollectionName) {
    alert("Пожалуйста, выберите подборку или введите название новой подборки.");
    return; // Прерываем выполнение, если ничего не выбрано или не введено
  }

  // Отправляем запрос на сервер
  fetch("add_to_collection.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `film_id=${filmId}&collection_id=${collectionId}&new_collection=${encodeURIComponent(
      newCollectionName
    )}`,
  })
    .then((response) => {
      if (response.ok) {
        // Закрыть модальное окно
        closeCollectionModal();
        // Обновить сердечко на странице
        const heartIcon = document.getElementById(`heart-${filmId}`);
        heartIcon.src = "images/heartZaliv.svg"; // Установить "заполненное" сердечко

        // Добавить класс для анимации
        const heartContainer = heartIcon.parentElement;
        heartContainer.classList.add("active");

        // Удалить класс после завершения анимации, чтобы можно было повторно активировать
        setTimeout(() => heartContainer.classList.remove("active"), 1000); // Время соответствует длительности анимации
      } else {
        console.error("Ошибка при добавлении фильма в подборку");
      }
    })
    .catch((error) => console.error("Ошибка при добавлении:", error));
}

function removeFilmFromCollection() {
  const filmId = document.getElementById("removeFilmId").value;
  const collectionId = document.getElementById("remove_collection").value;

  // Отправляем запрос на сервер
  fetch("remove_from_collection.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `film_id=${filmId}&collection_id=${collectionId}`,
  })
    .then((response) => {
      if (response.ok) {
        // Закрыть модальное окно
        closeRemoveModal();
        // Обновить сердечко на странице
        const heartIcon = document.getElementById(`heart-${filmId}`);
        heartIcon.src = "images/heartContr.svg"; // Установить "пустое" сердечко

        // Добавить класс для анимации
        const heartContainer = heartIcon.parentElement;
        heartContainer.classList.add("removing");

        // Удалить класс после завершения анимации, чтобы можно было повторно активировать
        setTimeout(() => heartContainer.classList.remove("removing"), 1000); // Время соответствует длительности анимации
      } else {
        console.error("Ошибка при удалении фильма из подборки");
      }
    })
    .catch((error) => console.error("Ошибка при удалении:", error));
}

/*function removeFilmFromCollectionCabinet(filmId, collectionId) {
  // Отправляем запрос на сервер
  fetch("remove_from_collection.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `film_id=${filmId}&collection_id=${collectionId}`,
  })
    .then((response) => {
      if (response.ok) {
        // Найти и удалить карточку фильма из DOM
        const filmCard = document.getElementById(`film-card-${filmId}`);
        if (filmCard) {
          const filmContainer = filmCard.parentElement; // Контейнер с фильмами
          filmCard.remove();

          // Проверяем, остались ли фильмы в контейнере
          if (filmContainer && filmContainer.children.length === 0) {
            const collectionElement = filmContainer.parentElement; // Контейнер подборки
            const noFilmsMessage = document.createElement("p");
            noFilmsMessage.className = "no-films";
            noFilmsMessage.textContent = "Подборка пуста";
            noFilmsMessage.style.marginTop = "0px";

            // Добавляем сообщение в коллекцию
            collectionElement.appendChild(noFilmsMessage);
          }

          // Добавить класс для анимации
          const heartContainer = heartIcon.parentElement;
          heartContainer.classList.add("removing");

          // Удалить класс после завершения анимации, чтобы можно было повторно активировать
          setTimeout(() => heartContainer.classList.remove("removing"), 1000); // Время соответствует длительности анимации
        } else {
          console.warn(`Карточка фильма с ID ${filmId} не найдена.`);
        }
      } else {
        console.error("Ошибка при удалении фильма из подборки");
      }
    })
    .catch((error) => console.error("Ошибка при удалении:", error));
}*/

function removeFilmFromCollectionCabinet(filmId, collectionId) {
  // Отправляем запрос на сервер
  fetch("remove_from_collection.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `film_id=${filmId}&collection_id=${collectionId}`,
  })
    .then((response) => {
      if (response.ok) {
        // Найти и удалить карточку фильма из DOM
        const filmCard = document.getElementById(`film-card-${filmId}`);
        const heartIcon = document.getElementById(`heart-${filmId}`);

        if (filmCard && heartIcon) {
          const filmContainer = filmCard.parentElement; // Сохраняем ссылку на контейнер с фильмами
          const collectionElement = filmContainer.parentElement; // Родитель контейнера с фильмами

          heartIcon.src = "images/heartContr.svg"; // Установить "пустое" сердечко
          const heartContainer = heartIcon.parentElement;

          // Добавить класс для анимации удаления сердечка
          heartContainer.classList.add("removing");

          // Подождать завершения анимации (например, 1 секунда), а затем удалить карточку
          setTimeout(() => {
            // Удалить карточку фильма
            filmCard.remove();

            // Проверяем, остались ли фильмы в контейнере
            if (filmContainer && filmContainer.children.length === 0) {
              const noFilmsMessage = document.createElement("p");
              noFilmsMessage.className = "no-films";
              noFilmsMessage.textContent = "Подборка пуста";
              noFilmsMessage.style.marginTop = "0px";

              // Добавляем сообщение о пустой подборке в контейнер подборки
              collectionElement.appendChild(noFilmsMessage);
            }
          }, 1000); // Длительность анимации
        } else {
          console.warn(
            `Карточка фильма с ID ${filmId} или сердечко не найдено.`
          );
        }
      } else {
        console.error("Ошибка при удалении фильма из подборки");
      }
    })
    .catch((error) => console.error("Ошибка при удалении:", error));
}
