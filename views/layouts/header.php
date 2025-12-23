<?php
/**
 * Главный шаблон header (включает DOCTYPE, head и начало body)
 * Все пути относительные от корня проекта
 */

// Определяем базовый путь
$base_path = dirname(dirname(__DIR__)); // Поднимаемся на два уровня вверх от views/layouts
$base_url = '/animal_passport/';

// Если проект в подпапке, нужно изменить
// $base_url = '/your-project/';

// Определяем заголовок страницы
$page_title = isset($page_title) ? $page_title . ' - AnimalPassport' : 'AnimalPassport';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <!-- Наши стили -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/style.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo $base_url; ?>assets/favicon.ico">
</head>
<body>
    <!-- Навигация -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?php echo $base_url; ?>">
                <i class="bi bi-search-heart"></i>
                <span class="ms-2">AnimalPassport</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" 
                           href="<?php echo $base_url; ?>">
                            <i class="bi bi-house-door"></i> Главная
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo isset($_GET['page']) && $_GET['page'] == 'register' ? 'active' : ''; ?>" 
                           href="<?php echo $base_url; ?>/animal_passport/?page=register">
                            <i class="bi bi-person-plus"></i> Регистрация
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo isset($_GET['page']) && $_GET['page'] == 'search' ? 'active' : ''; ?>" 
                           href="<?php echo $base_url; ?>?page=search">
                            <i class="bi bi-search"></i> Поиск
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>



    <!-- Основной контент -->
    <main class="container my-4">