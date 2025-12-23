<?php
// Определяем базовый URL для корректных ссылок
$base_url = '/animal_passport';

// Получаем данные из сессии
$results = $_SESSION['search_results'] ?? [];
$search_type = $_SESSION['search_type'] ?? 'photo';
$error_message = $_SESSION['search_error'] ?? null;

// Очищаем данные сессии после использования
unset($_SESSION['search_results']);
unset($_SESSION['search_type']);
unset($_SESSION['search_error']);
?>

<div class="container mt-5">
    <h1 class="text-center mb-4">
        <i class="bi bi-search"></i> Результаты поиска
    </h1>
    
    <div class="row justify-content-center">
        <div class="col-md-10">
            <?php if ($error_message): ?>
                <!-- Показываем ошибку если есть -->
                <div class="alert alert-danger">
                    <h5><i class="bi bi-exclamation-triangle"></i> Ошибка</h5>
                    <p class="mb-0"><?php echo $error_message; ?></p>
                </div>
            <?php endif; ?>
            
            <?php if (empty($results)): ?>
                <!-- Если ничего не найдено -->
                <div class="card border-warning">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-emoji-frown display-1 text-warning mb-3"></i>
                        <h3>Совпадений не найдено</h3>
                        <p class="lead">Попробуйте сделать другое фото или проверьте номер чипа.</p>
                        <a href="<?php echo $base_url; ?>?page=search" class="btn btn-primary mt-3">
                            <i class="bi bi-search"></i> Попробовать снова
                        </a>
                    </div>
                </div>
                
            <?php else: ?>
                
                <?php if ($search_type === 'photo'): ?>
                    <!-- Результаты поиска по фото -->
                    <div class="alert alert-info mb-4">
                        <h5><i class="bi bi-info-circle"></i> Найдено совпадений: <?php echo count($results); ?></h5>
                        <p class="mb-0">Совпадения отсортированы по вероятности.</p>
                    </div>
                    
                    <?php foreach ($results as $index => $match): ?>
                        <div class="card mb-4 shadow">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    Совпадение #<?php echo $index + 1; ?>
                                    <?php if (isset($match['similarity'])): ?>
                                        <span class="badge bg-primary ms-2">
                                            <?php echo $match['similarity']; ?>% совпадение
                                        </span>
                                    <?php endif; ?>
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 text-center">
                                        <?php if (!empty($match['photo_path'])): ?>
                                            <img src="<?php echo $base_url . $match['photo_path']; ?>" 
                                                 class="img-fluid rounded shadow" 
                                                 style="max-height: 200px;">
                                        <?php else: ?>
                                            <div class="text-muted p-3 border rounded">
                                                <i class="bi bi-image display-6"></i>
                                                <p>Фото отсутствует</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-8">
                                        <h5 class="mb-3"><?php echo htmlspecialchars($match['name'] ?? ''); ?></h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Вид:</strong> 
                                                    <?php echo isset($match['species']) && $match['species'] == 'cat' ? 'Кошка' : 'Собака'; ?>
                                                </p>
                                                <p><strong>Порода:</strong> 
                                                    <?php echo htmlspecialchars($match['breed'] ?? 'не указана'); ?>
                                                </p>
                                                <p><strong>Окрас:</strong> 
                                                    <?php echo htmlspecialchars($match['color'] ?? 'не указан'); ?>
                                                </p>
                                                <p><strong>Номер чипа:</strong> 
                                                    <code><?php echo htmlspecialchars($match['chip_id'] ?? ''); ?></code>
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Владелец:</strong> 
                                                    <?php echo htmlspecialchars($match['owner_name'] ?? ''); ?>
                                                </p>
                                                <p><strong>Телефон:</strong> 
                                                    <a href="tel:<?php echo htmlspecialchars($match['owner_phone'] ?? ''); ?>">
                                                        <?php echo htmlspecialchars($match['owner_phone'] ?? ''); ?>
                                                    </a>
                                                </p>
                                                <p><strong>Email:</strong> 
                                                    <a href="mailto:<?php echo htmlspecialchars($match['owner_email'] ?? ''); ?>">
                                                        <?php echo htmlspecialchars($match['owner_email'] ?? ''); ?>
                                                    </a>
                                                </p>
                                            </div>
                                        </div>
                                    </div> 
                                </div>
                            </div>
                            <!-- Важная информация -->
                            <div class="alert alert-warning">
                                <h6><i class="bi bi-exclamation-triangle"></i> Важно:</h6>
                                <p class="mb-0">
                                    Пожалуйста, свяжитесь с владельцем как можно скорее, если нашли это животное.
                                    Сообщите, где и когда вы его обнаружили.
                                </p>
                            </div>
                            <div class="card-footer text-center">
                                <a href="<?php echo $base_url; ?>?page=search" class="btn btn-outline-secondary me-2">
                                    <i class="bi bi-search"></i> Новый поиск
                                </a>
                                <a href="<?php echo $base_url; ?>?page=home" class="btn btn-primary">
                                    <i class="bi bi-house-door"></i> На главную
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                <?php else: ?>
                    <!-- Результаты поиска по чипу -->
                    <?php foreach ($results as $animal): ?>
                        <div class="card shadow-lg">
                            <div class="card-header bg-success text-white">
                                <h3 class="mb-0">
                                    <i class="bi bi-check-circle"></i> Животное найдено!
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 text-center">
                                        <?php if (!empty($animal['photo_path'])): ?>
                                            <img src="<?php echo $base_url . $animal['photo_path']; ?>" 
                                                 class="img-fluid rounded shadow mb-3" 
                                                 style="max-height: 250px;">
                                        <?php else: ?>
                                            <div class="text-muted p-4 border rounded mb-3">
                                                <i class="bi bi-image display-4"></i>
                                                <p>Фото отсутствует</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-8">
                                        <h2 class="text-primary"><?php echo htmlspecialchars($animal['name'] ?? ''); ?></h2>
                                        
                                        <div class="alert alert-success">
                                            <h5><i class="bi bi-info-circle"></i> Данные животного:</h5>
                                            <div class="row mt-2">
                                                <div class="col-md-6">
                                                    <p><strong>Вид:</strong> 
                                                        <?php echo isset($animal['species']) && $animal['species'] == 'cat' ? 'Кошка' : 'Собака'; ?>
                                                    </p>
                                                    <p><strong>Порода:</strong> 
                                                        <?php echo htmlspecialchars($animal['breed'] ?? 'не указана'); ?>
                                                    </p>
                                                    <p><strong>Пол:</strong> 
                                                        <?php echo isset($animal['gender']) && $animal['gender'] == 'male' ? 'Мужской' : 'Женский'; ?>
                                                    </p>
                                                    <p><strong>Окрас:</strong> 
                                                        <?php echo htmlspecialchars($animal['color'] ?? 'не указан'); ?>
                                                    </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Номер чипа:</strong> 
                                                        <code class="fs-5"><?php echo htmlspecialchars($animal['chip_id'] ?? ''); ?></code>
                                                    </p>
                                                    <p><strong>Дата рождения:</strong> 
                                                        <?php echo !empty($animal['birth_date']) 
                                                            ? date('d.m.Y', strtotime($animal['birth_date'])) 
                                                            : 'не указана'; ?>
                                                    </p>
                                                    <p><strong>Дата регистрации:</strong> 
                                                        <?php echo !empty($animal['registration_date']) 
                                                            ? date('d.m.Y', strtotime($animal['registration_date'])) 
                                                            : ''; ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="alert alert-info">
                                            <h5><i class="bi bi-person-badge"></i> Контактные данные владельца:</h5>
                                            <div class="row mt-2">
                                                <div class="col-md-6">
                                                    <p><strong>ФИО:</strong> 
                                                        <?php echo htmlspecialchars($animal['owner_name'] ?? ''); ?>
                                                    </p>
                                                    <p><strong>Телефон:</strong> 
                                                        <a href="tel:<?php echo htmlspecialchars($animal['owner_phone'] ?? ''); ?>" 
                                                           class="text-decoration-none">
                                                            <?php echo htmlspecialchars($animal['owner_phone'] ?? ''); ?>
                                                        </a>
                                                    </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Email:</strong> 
                                                        <a href="mailto:<?php echo htmlspecialchars($animal['owner_email'] ?? ''); ?>" 
                                                           class="text-decoration-none">
                                                            <?php echo htmlspecialchars($animal['owner_email'] ?? ''); ?>
                                                        </a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Важная информация -->
                            <div class="alert alert-warning">
                                <h6><i class="bi bi-exclamation-triangle"></i> Важно:</h6>
                                <p class="mb-0">
                                    Пожалуйста, свяжитесь с владельцем как можно скорее, если нашли это животное.
                                    Сообщите, где и когда вы его обнаружили.
                                </p>
                            </div>
                            <div class="card-footer text-center">
                                <a href="<?php echo $base_url; ?>?page=search" class="btn btn-outline-secondary me-2">
                                    <i class="bi bi-search"></i> Новый поиск
                                </a>
                                <a href="<?php echo $base_url; ?>?page=home" class="btn btn-primary">
                                    <i class="bi bi-house-door"></i> На главную
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>