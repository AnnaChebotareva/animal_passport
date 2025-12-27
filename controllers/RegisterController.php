<?php
// Подключаем конфигурацию БД
require_once '../config/database.php';
require_once 'ImageController.php';

// Включаем вывод ошибок для отладки
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Проверяем, что форма была отправлена методом POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. ВАЛИДАЦИЯ ВХОДНЫХ ДАННЫХ
    $errors = [];
    
    // Проверяем обязательные поля
    $required_fields = ['chip_id', 'name', 'species', 'gender', 'owner_name', 'owner_phone', 'owner_email'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = "Поле " . $field . " обязательно для заполнения";
        }
    }
    
    // Проверяем формат номера чипа (15 цифр)
    if (!empty($_POST['chip_id']) && !preg_match('/^[0-9]{15}$/', $_POST['chip_id'])) {
        $errors[] = "Номер чипа должен содержать ровно 15 цифр";
    }
    
    // Проверяем основное фото
    if (!isset($_FILES['main_photo']) || $_FILES['main_photo']['error'] != UPLOAD_ERR_OK) {
        $errors[] = "Необходимо загрузить основное фотографию животного";
    } else {
        // Проверяем тип файла
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        $file_type = $_FILES['main_photo']['type'];
        if (!in_array($file_type, $allowed_types)) {
            $errors[] = "Допустимы только файлы JPG и PNG";
        }
        
        // Проверяем размер файла (максимум 5MB)
        $max_size = 5 * 1024 * 1024;
        if ($_FILES['main_photo']['size'] > $max_size) {
            $errors[] = "Размер основного файла не должен превышать 5MB";
        }
    }
    
    // Проверяем дополнительные фото
    if (isset($_FILES['additional_photos'])) {
        foreach ($_FILES['additional_photos']['error'] as $key => $error) {
            if ($error == UPLOAD_ERR_OK) {
                $file_type = $_FILES['additional_photos']['type'][$key];
                if (!in_array($file_type, $allowed_types)) {
                    $errors[] = "Дополнительное фото #" . ($key + 1) . " должно быть JPG или PNG";
                }
                if ($_FILES['additional_photos']['size'][$key] > $max_size) {
                    $errors[] = "Размер дополнительного фото #" . ($key + 1) . " не должен превышать 5MB";
                }
            }
        }
    }
    
    // Если есть ошибки - показываем их
    if (!empty($errors)) {
        die(implode("<br>", $errors));
    }
    
    // 2. ПОДКЛЮЧЕНИЕ К БАЗЕ ДАННЫХ
    $database = new Database();
    $db = $database->getConnection();

    // 3. ПОДГОТОВКА ДАННЫХ ДЛЯ ВСТАВКИ

    // Сначала проверяем существование владельца
    $owner_sql = "SELECT id FROM owners WHERE email = :email OR phone = :phone LIMIT 1";
    $owner_check_data = [
        ':email' => htmlspecialchars(strip_tags($_POST['owner_email'])),
        ':phone' => htmlspecialchars(strip_tags($_POST['owner_phone']))
    ];

    try {
        // Начинаем транзакцию
        $db->beginTransaction();

        // Проверяем существование владельца
        $stmt = $db->prepare($owner_sql);
        $stmt->execute($owner_check_data);

        if ($stmt->rowCount() > 0) {
            // Владелец существует - используем существующий ID
            $owner = $stmt->fetch(PDO::FETCH_ASSOC);
            $owner_id = $owner['id'];

            // Обновляем информацию владельца (на случай если изменились данные)
            $update_owner_sql = "UPDATE owners SET 
                            full_name = :full_name, 
                            phone = :phone,
                            email = :email,
                            is_verified = 1
                            WHERE id = :id";

            $update_data = [
                ':full_name' => htmlspecialchars(strip_tags($_POST['owner_name'])),
                ':phone' => htmlspecialchars(strip_tags($_POST['owner_phone'])),
                ':email' => htmlspecialchars(strip_tags($_POST['owner_email'])),
                ':id' => $owner_id
            ];

            $stmt = $db->prepare($update_owner_sql);
            $stmt->execute($update_data);

        } else {
            // Владельца нет - создаем нового
            $owner_sql = "INSERT INTO owners (full_name, phone, email, is_verified) 
                      VALUES (:full_name, :phone, :email, 1)";

            $owner_data = [
                ':full_name' => htmlspecialchars(strip_tags($_POST['owner_name'])),
                ':phone' => htmlspecialchars(strip_tags($_POST['owner_phone'])),
                ':email' => htmlspecialchars(strip_tags($_POST['owner_email']))
            ];

            $stmt = $db->prepare($owner_sql);
            $stmt->execute($owner_data);
            $owner_id = $db->lastInsertId();
        }
        
        // 4. СОЗДАНИЕ ЗАПИСИ ЖИВОТНОГО
        $animal_sql = "INSERT INTO animals 
                      (chip_id, owner_id, name, species, breed, color, gender, birth_date, special_marks, status) 
                      VALUES 
                      (:chip_id, :owner_id, :name, :species, :breed, :color, :gender, :birth_date, :special_marks, 'active')";
        
        $animal_data = [
            ':chip_id' => htmlspecialchars(strip_tags($_POST['chip_id'])),
            ':owner_id' => $owner_id,
            ':name' => htmlspecialchars(strip_tags($_POST['name'])),
            ':species' => htmlspecialchars(strip_tags($_POST['species'])),
            ':breed' => !empty($_POST['breed']) ? htmlspecialchars(strip_tags($_POST['breed'])) : null,
            ':color' => !empty($_POST['color']) ? htmlspecialchars(strip_tags($_POST['color'])) : null,
            ':gender' => htmlspecialchars(strip_tags($_POST['gender'])),
            ':birth_date' => !empty($_POST['birth_date']) ? $_POST['birth_date'] : null,
            ':special_marks' => !empty($_POST['special_marks']) ? htmlspecialchars(strip_tags($_POST['special_marks'])) : null
        ];
        
        $stmt = $db->prepare($animal_sql);
        $stmt->execute($animal_data);
        $animal_id = $db->lastInsertId();
        
        // 5. СОХРАНЕНИЕ ФОТОГРАФИЙ
        $image_processor = new ImageProcessor();
        
        // Создаем директорию для загрузок если ее нет
        $upload_dir = '../assets/uploads/' . $animal_id . '/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Обработка основного фото
        $main_photo_data = processAndSavePhoto(
            $_FILES['main_photo'], 
            $animal_id, 
            $upload_dir, 
            $image_processor, 
            $db, 
            true
        );
        
        // Обработка дополнительных фото
        $additional_photos_data = [];
        if (isset($_FILES['additional_photos'])) {
            foreach ($_FILES['additional_photos']['name'] as $key => $name) {
                if ($_FILES['additional_photos']['error'][$key] == UPLOAD_ERR_OK) {
                    $file_data = [
                        'name' => $_FILES['additional_photos']['name'][$key],
                        'type' => $_FILES['additional_photos']['type'][$key],
                        'tmp_name' => $_FILES['additional_photos']['tmp_name'][$key],
                        'error' => $_FILES['additional_photos']['error'][$key],
                        'size' => $_FILES['additional_photos']['size'][$key]
                    ];
                    
                    $photo_data = processAndSavePhoto(
                        $file_data, 
                        $animal_id, 
                        $upload_dir, 
                        $image_processor, 
                        $db, 
                        false
                    );
                    
                    if ($photo_data) {
                        $additional_photos_data[] = $photo_data;
                    }
                }
            }
        }
        
        // Подтверждаем транзакцию
        $db->commit();
        
        // 7. ПЕРЕНАПРАВЛЕНИЕ НА СТРАНИЦУ УСПЕХА
        session_start();
        $_SESSION['registration_success'] = true;
        $_SESSION['registered_animal_id'] = $animal_id;
        $_SESSION['total_photos'] = 1 + count($additional_photos_data); // основное + дополнительные

        header('Location: ../views/pages/results.php?animal_id=' . $animal_id);
        exit();
        
    } catch (Exception $e) {
        // Откатываем транзакцию при ошибке
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        
        // Удаляем загруженные файлы если они были сохранены
        if (isset($upload_dir) && file_exists($upload_dir)) {
            deleteDirectory($upload_dir); // Убрано $this->
        }
        
        error_log("Registration error: " . $e->getMessage());
        header("Location: ../views/pages/register_form.php?error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    // Если кто-то попытался обратиться к файлу напрямую
    header('Location: ../views/register_form.php');
    exit();
}

/**
 * Обрабатывает и сохраняет фотографию
 */
function processAndSavePhoto($file_data, $animal_id, $upload_dir, $image_processor, $db, $is_primary = true) {
    // Создаем уникальное имя файла
    $file_extension = pathinfo($file_data['name'], PATHINFO_EXTENSION);
    $new_filename = 'photo_' . time() . '_' . uniqid() . '.' . $file_extension;
    $upload_path = $upload_dir . $new_filename;
    
    // Перемещаем файл
    if (!move_uploaded_file($file_data['tmp_name'], $upload_path)) {
        throw new Exception("Ошибка при сохранении файла: " . $file_data['name']);
    }
    
    // Обрабатываем изображение и получаем вектор признаков
    $embedding_data = $image_processor->processImage($upload_path);
    
    // Сохраняем информацию о фото в БД
    $photo_sql = "INSERT INTO animal_photos 
                 (animal_id, photo_path, is_primary, embedding_type, embedding_data) 
                 VALUES 
                 (:animal_id, :photo_path, :is_primary, 'color_histogram', :embedding_data)";
    
    $relative_path = '/assets/uploads/' . $animal_id . '/' . $new_filename;
    
    $photo_data = [
        ':animal_id' => $animal_id,
        ':photo_path' => $relative_path,
        ':is_primary' => $is_primary ? 1 : 0,
        ':embedding_data' => json_encode($embedding_data)
    ];
    
    $stmt = $db->prepare($photo_sql);
    $stmt->execute($photo_data);
    
    return [
        'path' => $relative_path,
        'is_primary' => $is_primary,
        'photo_id' => $db->lastInsertId()
    ];
}

/**
 * Рекурсивно удаляет директорию
 */
function deleteDirectory($dir) {
    if (!file_exists($dir)) {
        return true;
    }
    
    if (!is_dir($dir)) {
        return unlink($dir);
    }
    
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }
        
        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }
    
    return rmdir($dir);
}

/**
 * Проверяет корректность email адреса
 */
function validateEmail($email) {
    // 1. Базовая проверка PHP
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    
    // 2. Проверяем наличие @
    if (strpos($email, '@') === false) {
        return false;
    }
    
    // 3. Разделяем на части
    list($local, $domain) = explode('@', $email, 2);
    
    // 4. Проверяем длину частей
    if (strlen($local) < 1 || strlen($domain) < 3) {
        return false;
    }
    
    // 5. Проверяем наличие точки в домене
    if (strpos($domain, '.') === false) {
        return false;
    }
    
    // 6. Проверяем домен верхнего уровня
    $domainParts = explode('.', $domain);
    $tld = end($domainParts);
    
    // Должен быть хотя бы 2 символа
    if (strlen($tld) < 2) {
        return false;
    }
    
    // 7. Дополнительная проверка на русские буквы (если нужно их запретить)
    if (preg_match('/[а-яА-Я]/u', $email)) {
        return false;
    }
    
    return true;
}
?>