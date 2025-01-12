const typeChoice = document.getElementById("type_choice");
const filmQuestions = document.getElementById("film_questions");
const serialQuestions = document.getElementById("serial_questions");

// Вопросы фильма
const filmProdolzhitelnost = document.getElementById("prodolzhitelnost");
const filmGenre = document.getElementById("film_cluster");
const filmVid = document.getElementById("film_vid_filma");
const filmStrana = document.getElementById("film_strana");
const filmGod = document.getElementById("film_god");

// Вопросы сериала
const serialKolichestvoSeriy = document.getElementById("kolichestvo_seriy");
const serialGenre = document.getElementById("serial_cluster");
const serialVid = document.getElementById("serial_vid_filma");
const serialStrana = document.getElementById("serial_strana");
const serialGod = document.getElementById("serial_god");

// Логика отображения вопросов
typeChoice.addEventListener("change", () => {
  hideAllQuestions();

  if (typeChoice.value === "film") {
    filmQuestions.classList.replace("d-none", "d-block");
    showNextFilmQuestion(1); // Показать первый вопрос для фильма
  } else if (typeChoice.value === "serial") {
    serialQuestions.classList.replace("d-none", "d-block");
    showNextSerialQuestion(1); // Показать первый вопрос для сериала
  }
});

// Логика отображения вопросов для фильма
filmProdolzhitelnost.addEventListener("change", () => showNextFilmQuestion(2));
filmGenre.addEventListener("change", () => showNextFilmQuestion(3));
filmVid.addEventListener("change", () => showNextFilmQuestion(4));
filmStrana.addEventListener("change", () => showNextFilmQuestion(5));

// Логика отображения вопросов для сериала
serialKolichestvoSeriy.addEventListener("change", () =>
  showNextSerialQuestion(2)
);
serialGenre.addEventListener("change", () => showNextSerialQuestion(3));
serialVid.addEventListener("change", () => showNextSerialQuestion(4));
serialStrana.addEventListener("change", () => showNextSerialQuestion(5));

// Функция для показа следующего вопроса для фильма
function showNextFilmQuestion(questionNumber) {
  document
    .getElementById(`film_question${questionNumber}`)
    .classList.replace("d-none", "d-block");
}

// Функция для показа следующего вопроса для сериала
function showNextSerialQuestion(questionNumber) {
  document
    .getElementById(`serial_question${questionNumber}`)
    .classList.replace("d-none", "d-block");
}

filmGod.addEventListener("change", () => showSubmitButton());
serialGod.addEventListener("change", () => showSubmitButton());

function showSubmitButton() {
  const submitButton = document.getElementById("submitButton");
  submitButton.classList.replace("d-none", "d-block");
}

// Функция для скрытия всех вопросов
function hideAllQuestions() {
  filmQuestions.classList.add("d-none");
  serialQuestions.classList.add("d-none");

  // Скрытие всех вопросов для фильмов
  for (let i = 2; i <= 5; i++) {
    const filmQuestion = document.getElementById(`film_question${i}`);
    if (filmQuestion) {
      filmQuestion.classList.add("d-none");
    }
  }

  // Скрытие всех вопросов для сериалов
  for (let i = 2; i <= 5; i++) {
    const serialQuestion = document.getElementById(`serial_question${i}`);
    if (serialQuestion) {
      serialQuestion.classList.add("d-none");
    }
  }
}
