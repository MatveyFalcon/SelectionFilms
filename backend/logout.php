<?php

require 'db.php';

session_destroy(); // Завершаем сессию
header("Location: ../index.php"); // Возврат на главную страницу
?>
