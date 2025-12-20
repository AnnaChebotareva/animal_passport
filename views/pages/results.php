<?php
// Страница успешной регистрации животного

// Получаем ID животного из GET-параметра
$animal_id = isset($_GET['animal_id']) ? intval($_GET['animal_id']) : 0;

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Получаем информацию о зарегистрированном животном
$animal = null;
if ($animal_id > 0) {
    $sql = "SELECT a.*, o.full_name as owner_name 
            FROM animals a 
            LEFT JOIN owners o ON a.owner_id = o.id 
            WHERE a.id = :id";
    
    try {
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $animal_id, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $animal = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    } catch(PDOException $e) {
        // В случае ошибки просто продолжаем без данных животного
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация успешна - Система поиска животных</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
</head>
<body>
    <!-- Шапка -->
    <?php include 'header.php'; ?>
    
    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Карточка успеха -->
                <div class="card border-success shadow">
                    <div class="card-header bg-success text-white">
                        <h3 class="mb-0">
                            <i class="bi bi-check-circle-fill"></i> Регистрация успешно завершена!
                        </h3>
                    </div>
                    
                    <div class="card-body">
                        <!-- Иконка успеха -->
                        <div class="text-center mb-4">
                            <i class="bi bi-patch-check-fill text-success" style="font-size: 4rem;"></i>
                        </div>
                        
                        <?php if ($animal): ?>
                            <!-- Информация о зарегистрированном животном -->
                            <div class="alert alert-info">
                                <h5 class="alert-heading">Данные зарегистрированного животного:</h5>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <p><strong>Кличка:</strong> <?php echo htmlspecialchars($animal['name']); ?></p>
                                        <p><strong>Вид:</strong> <?php echo $animal['species'] == 'cat' ? 'Кошка' : 'Собака'; ?></p>
                                        <p><strong>Порода:</strong> <?php echo htmlspecialchars($animal['breed'] ?? 'не указана'); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Номер чипа:</strong> <code><?php echo htmlspecialchars($animal['chip_id']); ?></code></p>
                                        <p><strong>Владелец:</strong> <?php echo htmlspecialchars($animal['owner_name']); ?></p>
                                        <p><strong>Дата регистрации:</strong> <?php echo date('d.m.Y H:i', strtotime($animal['registration_date'])); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Важная информация -->
                            <div class="alert alert-warning">
                                <h6><i class="bi bi-exclamation-triangle"></i> Важно сохранить!</h6>
                                <p class="mb-0">
                                    Номер чипа <strong><?php echo htmlspecialchars($animal['chip_id']); ?></strong> является основным идентификатором вашего питомца.
                                    Сохраните его в надежном месте.
                                </p>
                            </div>
                        <?php else: ?>
                            <!-- Если не удалось получить данные животного -->
                            <div class="alert alert-warning">
                                <p>Животное успешно зарегистрировано, но подробная информация временно недоступна.</p>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Дальнейшие действия -->
                        <div class="mt-4">
                            <h5>Что делать дальше?</h5>
                            <ul>
                                <li>Сохраните номер чипа вашего питомца</li>
                                <li>Прикрепите контактные данные к ошейнику</li>
                                <li>Обновляйте информацию при смене адреса или телефона</li>
                            </ul>
                        </div>
                        
                        <!-- Кнопки действий -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="register_form.php" class="btn btn-outline-primary">
                                <i class="bi bi-plus-circle"></i> Зарегистрировать еще одного
                            </a>
                            <a href="../index.php" class="btn btn-primary">
                                <i class="bi bi-house-door"></i> На главную страницу
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Подвал -->
    <?php include 'footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>