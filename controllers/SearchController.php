<?php
require_once '../config/database.php';
require_once 'ImageController.php';

// Включаем вывод ошибок для отладки
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

// Проверяем, что форма была отправлена
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $search_type = $_POST['search_type'] ?? '';
    $errors = [];
    
    if ($search_type === 'photo') {
        // ПОИСК ПО ФОТОГРАФИИ
        
        // Проверяем загруженное фото
        if (!isset($_FILES['search_photo']) || $_FILES['search_photo']['error'] != UPLOAD_ERR_OK) {
            $errors[] = "Необходимо загрузить фотографию животного";
        } else {
            // Проверяем тип файла
            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
            $file_type = $_FILES['search_photo']['type'];
            if (!in_array($file_type, $allowed_types)) {
                $errors[] = "Допустимы только файлы JPG и PNG";
            }
            
            // Проверяем размер файла (максимум 5MB)
            $max_size = 5 * 1024 * 1024;
            if ($_FILES['search_photo']['size'] > $max_size) {
                $errors[] = "Размер файла не должен превышать 5MB";
            }
        }
        
        if (empty($errors)) {
            searchByPhoto();
        }
        
    } elseif ($search_type === 'chip') {
        // ПОИСК ПО НОМЕРУ ЧИПА
        
        if (empty($_POST['chip_number'])) {
            $errors[] = "Введите номер микрочипа";
        } elseif (!preg_match('/^[0-9]{15}$/', $_POST['chip_number'])) {
            $errors[] = "Номер чипа должен содержать ровно 15 цифр";
        }
        
        if (empty($errors)) {
            searchByChip();
        }
        
    } else {
        $errors[] = "Неверный тип поиска";
    }
    
    // Если есть ошибки - показываем их в сессии
    if (!empty($errors)) {
        $_SESSION['search_error'] = implode("<br>", $errors);
        header("Location: ../index.php?page=search_results");
        exit();
    }
    
} else {
    // Если кто-то попытался обратиться к файлу напрямую
    header('Location: ../index.php?page=search');
    exit();
}

/**
 * Поиск животного по фотографии
 */
function searchByPhoto() {
    try {
        // 1. Создаем папку uploads если её нет
        $upload_dir = '../assets/uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // 2. Обрабатываем загруженное фото
        $image_processor = new ImageProcessor();
        
        // Создаем временный файл
        $tmp_filename = 'tmp_search_' . time() . '_' . rand(1000, 9999) . '.jpg';
        $tmp_path = $upload_dir . $tmp_filename;
        
        if (!move_uploaded_file($_FILES['search_photo']['tmp_name'], $tmp_path)) {
            throw new Exception("Ошибка при сохранении файла");
        }
        
        // Получаем вектор признаков для загруженного фото
        $search_embedding = $image_processor->processImage($tmp_path);
        
        // 3. Ищем совпадения в базе данных
        $database = new Database();
        $db = $database->getConnection();
        
        // Получаем все фото из базы данных
        $sql = "SELECT ap.id, ap.animal_id, ap.photo_path, ap.embedding_data, 
                       a.name, a.species, a.breed, a.chip_id, a.color, a.gender,
                       o.full_name as owner_name, o.phone as owner_phone, o.email as owner_email
                FROM animal_photos ap
                JOIN animals a ON ap.animal_id = a.id
                JOIN owners o ON a.owner_id = o.id
                WHERE a.status = 'active'";
        
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $matches = [];
        
        foreach ($photos as $photo) {
            // Десериализуем вектор признаков из базы
            $stored_embedding = json_decode($photo['embedding_data'], true);
            
            if ($stored_embedding) {
                // Сравниваем гистограммы
                $similarity = $image_processor->compareHistograms(
                    $search_embedding, 
                    $stored_embedding
                );
                
                // Если схожесть выше порога (понизим для теста)
                if ($similarity > 0.3) {
                    $matches[] = [
                        'animal_id' => $photo['animal_id'],
                        'name' => $photo['name'],
                        'species' => $photo['species'],
                        'breed' => $photo['breed'],
                        'color' => $photo['color'],
                        'gender' => $photo['gender'],
                        'chip_id' => $photo['chip_id'],
                        'owner_name' => $photo['owner_name'],
                        'owner_phone' => $photo['owner_phone'],
                        'owner_email' => $photo['owner_email'],
                        'photo_path' => $photo['photo_path'],
                        'similarity' => round($similarity * 100, 1) // в процентах
                    ];
                }
            }
        }
        
        // Сортируем по убыванию схожести
        usort($matches, function($a, $b) {
            return $b['similarity'] <=> $a['similarity'];
        });
        
        // 4. Удаляем временный файл
        if (file_exists($tmp_path)) {
            unlink($tmp_path);
        }
        
        // 5. Показываем результаты
        $_SESSION['search_results'] = $matches;
        $_SESSION['search_type'] = 'photo';
        $_SESSION['search_error'] = null;
        
        header("Location: ../index.php?page=search_results");
        exit();
        
    } catch (Exception $e) {
        // Удаляем временный файл при ошибке
        if (isset($tmp_path) && file_exists($tmp_path)) {
            unlink($tmp_path);
        }
        
        $_SESSION['search_error'] = "Ошибка при поиске: " . $e->getMessage();
        $_SESSION['search_results'] = [];
        header("Location: ../index.php?page=search_results");
        exit();
    }
}

/**
 * Поиск животного по номеру чипа
 */
function searchByChip() {
    try {
        $chip_number = htmlspecialchars(strip_tags($_POST['chip_number']));
        
        $database = new Database();
        $db = $database->getConnection();
        
        $sql = "SELECT a.*, 
                       o.full_name as owner_name, 
                       o.phone as owner_phone, 
                       o.email as owner_email,
                       ap.photo_path
                FROM animals a
                JOIN owners o ON a.owner_id = o.id
                LEFT JOIN animal_photos ap ON a.id = ap.animal_id AND ap.is_primary = 1
                WHERE a.chip_id = :chip_id AND a.status = 'active'";
        
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':chip_id', $chip_number, PDO::PARAM_STR);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $animal = $stmt->fetch(PDO::FETCH_ASSOC);
            $_SESSION['search_results'] = [$animal];
            $_SESSION['search_type'] = 'chip';
            $_SESSION['search_error'] = null;
        } else {
            $_SESSION['search_results'] = [];
            $_SESSION['search_type'] = 'chip';
            $_SESSION['search_error'] = "Животное с номером чипа <strong>{$chip_number}</strong> не найдено в базе данных.";
        }
        
        header("Location: ../index.php?page=search_results");
        exit();
        
    } catch (Exception $e) {
        $_SESSION['search_error'] = "Ошибка при поиске: " . $e->getMessage();
        $_SESSION['search_results'] = [];
        header("Location: ../index.php?page=search_results");
        exit();
    }
}
?>