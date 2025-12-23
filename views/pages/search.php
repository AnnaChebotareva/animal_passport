<?php

$base_url = '/animal_passport'; 

?>

<div class="container mt-5">
    <h1 class="text-center mb-4"><i class="bi bi-search"></i> Поиск животного</h1>
    
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Информационный блок -->
            <div class="alert alert-info mb-4">
                <h5><i class="bi bi-info-circle"></i> Выберите способ поиска:</h5>
                <p class="mb-0">Вы можете найти животное по фотографии или по номеру микрочипа.</p>
            </div>
            
            <div class="row">
                <!-- Поиск по фотографии -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 shadow border-primary">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">
                                <i class="bi bi-camera"></i> Поиск по фото
                            </h4>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <p class="card-text flex-grow-1">
                                Загрузите фотографию найденного животного. Система сравнит её с 
                                фотографиями в базе данных и найдет совпадения.
                            </p>
                            <div class="text-center mb-3">
                                <i class="bi bi-image-fill text-primary" style="font-size: 4rem;"></i>
                            </div>
                            <a href="?page=search_photo" class="btn btn-primary btn-lg mt-auto">
                                <i class="bi bi-search me-2"></i> Поиск по фото
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Поиск по номеру чипа -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 shadow border-success">
                        <div class="card-header bg-success text-white">
                            <h4 class="mb-0">
                                <i class="bi bi-upc-scan"></i> Поиск по чипу
                            </h4>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <p class="card-text flex-grow-1">
                                Введите 15-значный номер микрочипа. Система найдет информацию 
                                о животном и его владельце по базе данных.
                            </p>
                            <div class="text-center mb-3">
                                <i class="bi bi-credit-card-2-front text-success" style="font-size: 4rem;"></i>
                            </div>
                            <a href="?page=search_chip" class="btn btn-success btn-lg mt-auto">
                                <i class="bi bi-search me-2"></i> Поиск по чипу
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Дополнительная информация -->
            <div class="card mt-4">
                <div class="card-body">
                    <h5><i class="bi bi-lightbulb-fill text-warning"></i> Советы по поиску:</h5>
                    <ul class="mb-0">
                        <li>Для поиска по фото делайте снимки животного в анфас при хорошем освещении</li>
                        <li>Номер микрочипа обычно указан на ошейнике или ветеринарном паспорте</li>
                        <li>Если животное найдено, свяжитесь с владельцем как можно скорее</li>
                    </ul>
                </div>
            </div>
            
            <!-- Кнопка возврата -->
            <div class="text-center mt-4">
                <a href="?page=home" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Вернуться на главную
                </a>
            </div>
        </div>
    </div>
</div>