// Функция для получения информации о статусе авторизации
function checkAuthStatus() {
  return fetch("backend/auth_status.php") // Делает запрос к auth_status.php
    .then((response) => response.json()) // Преобразует ответ в JSON
    .then((data) => {
      return data.isLoggedIn; // Возвращаем информацию об авторизации
    })
    .catch(() => {
      return false; // В случае ошибки возвращаем, что пользователь не авторизован
    });
}

function openCollectionModal(filmId) {
  const modal = document.getElementById("collectionModal");
  modal.style.display = "block";
  document.getElementById("selectedFilmId").value = filmId;

  document.getElementById("new_collection").value = "";

  loadCollections();
}

function loadCollections() {
  const select = document.getElementById("collection");

  // Отправляем запрос на сервер для получения подборок
  fetch("backend/get_collections.php")
    .then((response) => response.json()) // Ожидаем JSON-ответ
    .then((data) => {
      // Очищаем текущие опции
      select.innerHTML =
        '<option value="" selected disabled>Выберите вариант</option>';

      if (data.collections && data.collections.length > 0) {
        // Заполняем список подборок
        data.collections.forEach((collection) => {
          const option = document.createElement("option");
          option.value = collection.id;
          option.textContent = collection.name;
          select.appendChild(option);
        });
      } else {
        select.innerHTML = "<option disabled>У вас нет подборок</option>";
      }
    })
    .catch((error) => {
      console.error("Ошибка загрузки подборок:", error);
      select.innerHTML = "<option disabled>Ошибка загрузки</option>";
    });
}

function closeCollectionModal() {
  const modal = document.getElementById("collectionModal");
  modal.style.display = "none";
}

function toggleHeart(filmId) {
  checkAuthStatus().then((isAuthenticated) => {
    if (!isAuthenticated) {
      alert(
        "Пожалуйста, авторизуйтесь, чтобы создавать подборки и добавлять фильмы в них!"
      );
      return;
    }

    const heartIcons = document.querySelectorAll(`[id^="heart-${filmId}"]`);
    const isAdded = heartIcons[0]?.src.includes("heartZaliv.svg");

    if (isAdded) {
      // Если фильм уже добавлен, открыть модальное окно для удаления
      openRemoveModal(filmId);
    } else {
      // Если фильм не добавлен, открыть модальное окно для добавления
      openCollectionModal(filmId);
    }
  });
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
  fetch("backend/get_film_collections.php", {
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

  if (!collectionId && !newCollectionName) {
    alert("Пожалуйста, выберите подборку или введите название новой подборки.");
    return;
  }

  fetch("backend/add_to_collection.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `film_id=${filmId}&collection_id=${collectionId}&new_collection=${encodeURIComponent(
      newCollectionName
    )}`,
  })
    .then((response) => {
      if (response.ok) {
        closeCollectionModal();

        // Обновить все сердечки с данным filmId
        const heartIcons = document.querySelectorAll(`[id^="heart-${filmId}"]`);
        heartIcons.forEach((heartIcon) => {
          heartIcon.src = "images/heartZaliv.svg";
          const heartContainer = heartIcon.parentElement;
          heartContainer.classList.add("active");
          setTimeout(() => heartContainer.classList.remove("active"), 1000);
        });
      } else {
        console.error("Ошибка при добавлении фильма в подборку");
      }
    })
    .catch((error) => console.error("Ошибка при добавлении:", error));
}

function removeFilmFromCollection() {
  const filmId = document.getElementById("removeFilmId").value;
  const collectionId = document.getElementById("remove_collection").value;

  fetch("backend/remove_from_collection.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `film_id=${filmId}&collection_id=${collectionId}`,
  })
    .then((response) => {
      if (response.ok) {
        closeRemoveModal();

        // Обновить все сердечки с данным filmId
        const heartIcons = document.querySelectorAll(`[id^="heart-${filmId}"]`);
        heartIcons.forEach((heartIcon) => {
          heartIcon.src = "images/heartContr.svg";
          const heartContainer = heartIcon.parentElement;
          heartContainer.classList.add("removing");
          setTimeout(() => heartContainer.classList.remove("removing"), 1000);
        });
      } else {
        console.error("Ошибка при удалении фильма из подборки");
      }
    })
    .catch((error) => console.error("Ошибка при удалении:", error));
}

function removeFilmFromCollectionCabinet(filmId, collectionId) {
  // Отправляем запрос на сервер
  fetch("backend/remove_from_collection.php", {
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

function deleteCollection(collectionId) {
  if (
    !confirm("Вы уверены, что хотите удалить эту подборку вместе с фильмами?")
  ) {
    return;
  }

  fetch("backend/delete_collection.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `collection_id=${collectionId}`,
  })
    .then((response) => {
      if (response.ok) {
        // Удаляем элемент из DOM
        const collectionElement = document.getElementById(
          `collection-${collectionId}`
        );
        if (collectionElement) {
          collectionElement.remove();
        }

        // Проверяем количество оставшихся коллекций
        const collections = document.querySelectorAll(".collection");
        const contentElement = document.querySelector(".content"); // Находим контейнер .content внутри main
        if (collections.length === 0) {
          // Если коллекций нет, создаем сообщение
          const noCollectionsMessage = document.createElement("p");
          noCollectionsMessage.className = "no-collections";
          noCollectionsMessage.textContent = "У вас пока нет подборок.";

          // Добавляем сообщение в .content
          if (contentElement) {
            contentElement.appendChild(noCollectionsMessage);
          }
        }
      } else {
        console.error("Ошибка при удалении подборки");
      }
    })
    .catch((error) => console.error("Ошибка при удалении подборки:", error));
}
