<?php
$base_url = '/animal_passport';
require_once './controllers/HistogramGenerator.php';

// Получаем данные из сессии
$results = $_SESSION['search_results'] ?? [];
$search_type = $_SESSION['search_type'] ?? 'photo';
$error_message = $_SESSION['search_error'] ?? null;
$search_histogram = $_SESSION['search_histogram'] ?? null;

// Очищаем данные сессии после использования
unset($_SESSION['search_results']);
unset($_SESSION['search_type']);
unset($_SESSION['search_error']);
unset($_SESSION['search_histogram']);
?>

<div class="container mt-5">
    <h1 class="text-center mb-4">
        <i class="bi bi-search"></i> Результаты поиска
    </h1>
    
    <div class="row justify-content-center">
        <div class="col-md-12">
            <?php if ($error_message): ?>
                <!-- Показываем ошибку если есть -->
                <div class="alert alert-danger">
                    <h5><i class="bi bi-exclamation-triangle"></i> Ошибка</h5>
                    <p class="mb-0"><?php echo $error_message; ?></p>
                </div>
            <?php endif; ?>
            
            <?php if ($search_type === 'photo' && !empty($results)): ?>
                <!-- Блок с информацией о методе поиска -->
                <div class="card border-info mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-graph-up"></i> Метод биометрического поиска
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-8">
                                <h6 class="mb-3">Как работает сравнение:</h6>
                                <ul class="mb-4">
                                    <li><strong>Анализ цветов:</strong> Система анализирует распределение цветов на фотографиях</li>
                                    <li><strong>Гистограмма:</strong> Каждое изображение преобразуется в 512-мерную цветовую гистограмму</li>
                                    <li><strong>Сравнение:</strong> Схожесть вычисляется на основе пересечения гистограмм</li>
                                    <li><strong>Результат:</strong> Чем больше столбцы похожи по высоте, тем выше совпадение</li>
                                </ul>
                                
                                <?php echo HistogramGenerator::generateColorLegend(); ?>
                            </div>
                            <div class="col-lg-4">
                                <?php if ($search_histogram): ?>
                                    <!-- Показываем гистограмму загруженного фото -->
                                    <?php echo HistogramGenerator::generateHistogramHTML(
                                        $search_histogram, 
                                        'Ваше фото'
                                    ); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
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
                                                 class="img-fluid rounded shadow mb-3" 
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
                                
                                <!-- Блок сравнения гистограмм -->
                                <?php if (isset($search_histogram) && isset($match['stored_histogram'])): ?>
                                    <hr class="my-4">
                                    <div class="row align-items-center mb-3">
                                        <div class="col-md-12">
                                            <h6 class="mb-0">
                                                <i class="bi bi-bar-chart"></i> Сравнение гистограмм
                                            </h6>
                                        </div>
                                    </div>
                                    <?php echo HistogramGenerator::generateComparisonHTML(
                                        $search_histogram,
                                        $match['stored_histogram'],
                                        $match['similarity'] ?? 'N/A',
                                        'Ваше фото',
                                        htmlspecialchars($match['name'] ?? '')
                                    ); ?>
                                <?php endif; ?>
                            </div>
                            <!-- Важная информация -->
                            <div class="alert alert-warning">
                                <h6><i class="bi bi-exclamation-triangle"></i> Важно:</h6>
                                <p class="mb-0">
                                    Пожалуйста, свяжитесь с владельцем как можно скорее, если нашли это животное.
                                    Сообщите, где и когда вы его обнаружили.
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="card-footer text-center">
                        <a href="<?php echo $base_url; ?>?page=search" class="btn btn-outline-secondary me-2">
                            <i class="bi bi-search"></i> Новый поиск
                        </a>
                        <a href="<?php echo $base_url; ?>?page=home" class="btn btn-primary">
                            <i class="bi bi-house-door"></i> На главную
                        </a>
                    </div>    
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
                                        <?php if (!empty($animal['main_photo'])): ?>
                                            <img src="<?php echo $base_url . $animal['main_photo']; ?>" class="img-fluid rounded shadow mb-3"
                                                style="max-height: 250px; object-fit: cover;">
                                            <p class="small text-muted mt-1">Основное фото животного</p>
                                        <?php elseif (!empty($animal['photo_path'])): ?>
                                            <!-- Для обратной совместимости, если photo_path еще используется -->
                                            <img src="<?php echo $base_url . $animal['photo_path']; ?>" class="img-fluid rounded shadow mb-3"
                                                style="max-height: 250px; object-fit: cover;">
                                            <p class="small text-muted mt-1">Фото животного</p>
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

<style>
/* Гистограмма - базовые стили */
.histogram-container {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    height: 200px;
    display: flex;
    flex-direction: column;
}

.histogram-title {
    text-align: center;
    margin-bottom: 10px;
    font-size: 14px;
    font-weight: 600;
    color: #495057;
    flex-shrink: 0;
}

.histogram-chart {
    flex: 1;
    position: relative;
    display: flex;
    flex-direction: column;
}

.histogram-bars {
    flex: 1;
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    border-bottom: 1px solid #ced4da;
    position: relative;
    padding: 0 5px;
}

.histogram-bar {
    width: 5.5%;
    border-radius: 3px 3px 0 0;
    transition: height 0.3s ease;
    background-color: #4a6baf;
    min-height: 2px;
}

.histogram-bar:hover {
    opacity: 0.8;
}

.histogram-x-axis {
    display: flex;
    justify-content: space-between;
    padding: 5px 10px 0 10px;
    font-size: 11px;
    color: #6c757d;
    flex-shrink: 0;
}

/* Контейнер сравнения */
.comparison-container {
    margin-top: 10px;
}

/* Цветовая легенда */
.color-legend {
    margin-bottom: 20px;
}

.color-legend .color-box {
    border: 1px solid rgba(0,0,0,0.1);
    border-radius: 3px;
}

/* Адаптивность */
@media (max-width: 768px) {
    .histogram-container {
        height: 180px;
        padding: 12px;
    }
    
    .histogram-title {
        font-size: 13px;
    }
}

@media (max-width: 576px) {
    .histogram-container {
        height: 160px;
        padding: 10px;
    }
    
    .histogram-x-axis {
        font-size: 10px;
        padding: 3px 5px 0 5px;
    }
}
</style>