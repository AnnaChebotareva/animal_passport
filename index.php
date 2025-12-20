<?php
/**
 * Единая точка входа (Front Controller)
 * Обрабатывает все запросы через GET-параметр ?page=
 */

session_start();

// Определяем базовый путь для include
define('BASE_PATH', __DIR__);
define('VIEWS_PATH', BASE_PATH . '/views');

// Определяем, какую страницу показывать
$page = $_GET['page'] ?? 'home';

// Подключаем конфиг БД только если нужно
$require_db = in_array($page, ['register', 'search', 'results']);

if ($require_db) {
    require_once BASE_PATH . '/config/database.php';
}

// Массив допустимых страниц (защита от path traversal)
$allowed_pages = [
    'home' => 'pages/home.php',
    'register' => 'pages/register_form.php',
    'search' => 'pages/search_form.php',
    'results' => 'pages/results.php'
];

// Получаем путь к файлу шаблона
$template_file = $allowed_pages[$page] ?? $allowed_pages['home'];

// Проверяем существование файла
if (!file_exists(VIEWS_PATH . '/' . $template_file)) {
    $template_file = $allowed_pages['home'];
}

// Устанавливаем заголовок страницы
$page_titles = [
    'home' => 'Главная',
    'register' => 'Регистрация животного',
    'search' => 'Поиск животного',
    'results' => 'Результаты'
];

$page_title = $page_titles[$page] ?? 'Главная';

// Подключаем header
require_once VIEWS_PATH . '/layouts/header.php';

// Подключаем основной контент
require_once VIEWS_PATH . '/' . $template_file;

// Подключаем footer
require_once VIEWS_PATH . '/layouts/footer.php';