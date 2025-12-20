<?php
/**
 * Главная страница
 */
?>
<div class="row">
    <div class="col-lg-8 mx-auto text-center">
        <h1 class="display-4 mb-4 text-primary">
            <i class="bi bi-search-heart"></i> Система цифровых паспортов животных
        </h1>
        <p class="lead mb-5">
            Современное решение для идентификации и поиска домашних питомцев. 
            Объединяем технологии микрочипирования и биометрического распознавания.
        </p>
        
        <div class="row g-4">
            <!-- Карточка регистрации -->
            <div class="col-md-6">
                <div class="card h-100 shadow border-primary">
                    <div class="card-body text-center p-4">
                        <div class="mb-4">
                            <i class="bi bi-person-plus display-2 text-primary"></i>
                        </div>
                        <h3 class="card-title h4 mb-3">Регистрация питомца</h3>
                        <p class="card-text mb-4">
                            Создайте цифровой паспорт для вашего животного. 
                            Привязка к микрочипу, фото для биометрии, контакты владельца.
                        </p>
                        <a href="?page=register" class="btn btn-primary btn-lg w-100">
                            <i class="bi bi-plus-circle"></i> Зарегистрировать
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Карточка поиска -->
            <div class="col-md-6">
                <div class="card h-100 shadow border-success">
                    <div class="card-body text-center p-4">
                        <div class="mb-4">
                            <i class="bi bi-search display-2 text-success"></i>
                        </div>
                        <h3 class="card-title h4 mb-3">Поиск животного</h3>
                        <p class="card-text mb-4">
                            Нашли животное? Загрузите фото для биометрического поиска 
                            или введите номер чипа для точной идентификации.
                        </p>
                        <a href="?page=search" class="btn btn-success btn-lg w-100">
                            <i class="bi bi-camera"></i> Найти животное
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Как это работает -->
        <div class="mt-5 p-4 bg-light rounded">
            <h3 class="h4 mb-4 text-center">
                <i class="bi bi-info-circle"></i> Три простых шага
            </h3>
            <div class="row text-center">
                <div class="col-md-4 mb-3">
                    <div class="p-3">
                        <span class="badge bg-primary rounded-circle p-3 mb-3" style="font-size: 1.5rem;">1</span>
                        <h5>Регистрация</h5>
                        <p class="small">Владелец регистрирует питомца в системе</p>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="p-3">
                        <span class="badge bg-success rounded-circle p-3 mb-3" style="font-size: 1.5rem;">2</span>
                        <h5>Идентификация</h5>
                        <p class="small">Система создает биометрический профиль</p>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="p-3">
                        <span class="badge bg-warning rounded-circle p-3 mb-3" style="font-size: 1.5rem;">3</span>
                        <h5>Поиск</h5>
                        <p class="small">Найденное животное можно идентифицировать</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>