<?php
// Подключаем конфигурацию БД
require_once '../config/database.php';
require_once 'ImageController.php';

// Включаем вывод ошибок для отладки (на время разработки)
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
    
    // Проверяем загруженное фото
    if (!isset($_FILES['photo']) || $_FILES['photo']['error'] != UPLOAD_ERR_OK) {
        $errors[] = "Необходимо загрузить фотографию животного";
    } else {
        // Проверяем тип файла
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        $file_type = $_FILES['photo']['type'];
        if (!in_array($file_type, $allowed_types)) {
            $errors[] = "Допустимы только файлы JPG и PNG";
        }
        
        // Проверяем размер файла (максимум 5MB)
        $max_size = 5 * 1024 * 1024; // 5MB в байтах
        if ($_FILES['photo']['size'] > $max_size) {
            $errors[] = "Размер файла не должен превышать 5MB";
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
    
    // Сначала создаем запись владельца
    $owner_sql = "INSERT INTO owners (full_name, phone, email, is_verified) 
                  VALUES (:full_name, :phone, :email, 1)";
    
    $owner_data = [
        ':full_name' => htmlspecialchars(strip_tags($_POST['owner_name'])),
        ':phone' => htmlspecialchars(strip_tags($_POST['owner_phone'])),
        ':email' => htmlspecialchars(strip_tags($_POST['owner_email']))
    ];
    
    try {
        // Начинаем транзакцию (чтобы либо все сохранится, либо ничего)
        $db->beginTransaction();
        
        // Вставляем владельца
        $stmt = $db->prepare($owner_sql);
        $stmt->execute($owner_data);
        $owner_id = $db->lastInsertId(); // Получаем ID нового владельца
        
        // 4. СОХРАНЕНИЕ ФОТОГРАФИИ И ГЕНЕРАЦИЯ ВЕКТОРА ПРИЗНАКОВ
        
        // Создаем уникальное имя файла
        $file_extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $new_filename = 'animal_' . $_POST['chip_id'] . '_' . time() . '.' . $file_extension;
        $upload_path = '../assets/uploads/' . $new_filename;
        
        // Перемещаем загруженный файл в папку uploads
        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
            throw new Exception("Ошибка при сохранении файла");
        }
        
        // Создаем экземпляр процессора изображений
        $image_processor = new ImageProcessor();
        
        // Обрабатываем изображение и получаем вектор признаков
        $embedding_data = $image_processor->processImage($upload_path);
        
        // 5. СОЗДАНИЕ ЗАПИСИ ЖИВОТНОГО
        $animal_sql = "INSERT INTO animals 
                      (chip_id, owner_id, name, species, breed, color, gender, status) 
                      VALUES 
                      (:chip_id, :owner_id, :name, :species, :breed, :color, :gender, 'active')";
        
        $animal_data = [
            ':chip_id' => htmlspecialchars(strip_tags($_POST['chip_id'])),
            ':owner_id' => $owner_id,
            ':name' => htmlspecialchars(strip_tags($_POST['name'])),
            ':species' => htmlspecialchars(strip_tags($_POST['species'])),
            ':breed' => !empty($_POST['breed']) ? htmlspecialchars(strip_tags($_POST['breed'])) : null,
            ':color' => !empty($_POST['color']) ? htmlspecialchars(strip_tags($_POST['color'])) : null,
            ':gender' => htmlspecialchars(strip_tags($_POST['gender']))
        ];
        
        $stmt = $db->prepare($animal_sql);
        $stmt->execute($animal_data);
        $animal_id = $db->lastInsertId();
        
        // 6. СОХРАНЕНИЕ ФОТОГРАФИИ В БАЗЕ ДАННЫХ
        $photo_sql = "INSERT INTO animal_photos 
                     (animal_id, photo_path, is_primary, embedding_type, embedding_data) 
                     VALUES 
                     (:animal_id, :photo_path, 1, 'color_histogram', :embedding_data)";
        
        $photo_data = [
            ':animal_id' => $animal_id,
            ':photo_path' => '/assets/uploads/' . $new_filename, // Сохраняем относительный путь
            ':embedding_data' => json_encode($embedding_data) // Сериализуем массив в JSON
        ];
        
        $stmt = $db->prepare($photo_sql);
        $stmt->execute($photo_data);
        
        // Подтверждаем транзакцию
        $db->commit();
        
        // 7. ПЕРЕНАПРАВЛЕНИЕ НА СТРАНИЦУ УСПЕХА
        session_start();
        $_SESSION['registration_success'] = true;
        $_SESSION['registered_animal_id'] = $animal_id;

        header('Location: ../views/pages/results.php?animal_id=' . $animal_id);
        exit();
        
    } catch (Exception $e) {
        // Откатываем транзакцию при ошибке
        $db->rollBack();
         // Обработка ошибки
        error_log("Registration error: " . $e->getMessage());
        header("Location: ../views/pages/register_form.php?error=1");
        exit();
        
        // Удаляем загруженный файл, если он был сохранен
        if (file_exists($upload_path)) {
            unlink($upload_path);
        }
        
        die("Ошибка при регистрации: " . $e->getMessage());
    }
} else {
    // Если кто-то попытался обратиться к файлу напрямую
    header('Location: ../views/register_form.php');
    exit();
}
?>