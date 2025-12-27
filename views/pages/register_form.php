<?php
/**
 * Форма регистрации нового животного с возможностью нескольких фото
 */

// Определяем скрипты для этой страницы
$page_scripts = ['register.js'];
?>
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h2 class="h4 mb-0">
                    <i class="bi bi-person-plus"></i> Регистрация нового питомца
                </h2>
            </div>
            
            <div class="card-body p-4">
                <!-- Инструкция -->
                <div class="alert alert-info mb-4">
                    <i class="bi bi-info-circle"></i> 
                    Заполните все обязательные поля (*). Фотографии требуются для биометрической идентификации.
                </div>
                
                <form id="registerForm" action="controllers/RegisterController.php" method="POST" enctype="multipart/form-data" novalidate>
                    <!-- Основная информация -->
                    <div class="mb-5">
                        <h4 class="border-bottom pb-2 mb-3">
                            <i class="bi bi-card-text"></i> Основная информация
                        </h4>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="chip_id" class="form-label">
                                    Номер микрочипа <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="chip_id" name="chip_id" 
                                       placeholder="15 цифр" pattern="\d{15}" required>
                                <div class="form-text">15 цифр, без пробелов и дефисов</div>
                                <div class="invalid-feedback">Введите 15-значный номер чипа</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="name" class="form-label">
                                    Кличка животного <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="species" class="form-label">Вид <span class="text-danger">*</span></label>
                                <select class="form-select" id="species" name="species" required>
                                    <option value="" selected disabled>Выберите...</option>
                                    <option value="cat">Кошка</option>
                                    <option value="dog">Собака</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="breed" class="form-label">Порода</label>
                                <input type="text" class="form-control" id="breed" name="breed">
                            </div>

                            <div class="col-md-4">
                                <label for="color" class="form-label">Окрас</label>
                                <input type="text" class="form-control" id="color" name="color">
                            </div>

                            <div class="col-md-4">
                                <label for="birth_date" class="form-label">Дата рождения</label>
                                <input type="date" class="form-control" id="birth_date" name="birth_date">
                            </div>
                            
                            <div class="col-md-4">
                                <label for="gender" class="form-label">Пол <span class="text-danger">*</span></label>
                                <select class="form-select" id="gender" name="gender" required>
                                    <option value="" selected disabled>Выберите...</option>
                                    <option value="male">Мужской</option>
                                    <option value="female">Женский</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="special_marks" class="form-label">Особенности</label>
                                <input type="text" class="form-control" id="special_marks" name="special_marks">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Фотографии -->
                    <div class="mb-5">
                        <h4 class="border-bottom pb-2 mb-3">
                            <i class="bi bi-camera"></i> Фотографии для биометрии
                        </h4>
                        
                        <!-- Основное фото -->
                        <div class="row align-items-center mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="main_photo" class="form-label">
                                        Основное фото (анфас) <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" class="form-control" id="main_photo" name="main_photo" 
                                           accept="image/jpeg,image/png" required>
                                    <div class="form-text">JPG или PNG, до 5MB. Животное должно быть видно четко.</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="text-center">
                                    <div id="mainPhotoPreview" class="mb-3" style="display: none;">
                                        <p class="small text-muted">Предпросмотр основного фото:</p>
                                        <img id="mainPreviewImage" class="img-thumbnail" style="max-width: 200px;">
                                    </div>
                                    <div id="noMainPhoto" class="text-muted">
                                        <i class="bi bi-image display-6"></i>
                                        <p class="small mt-2">Основное фото появится здесь</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Дополнительные фото -->
                        <div class="mb-4">
                            <label class="form-label">
                                Дополнительные фото
                            </label>
                            <div class="alert alert-secondary">
                                <i class="bi bi-info-circle"></i> 
                                Дополнительные фото с разных ракурсов улучшат точность поиска. 
                                Можно загрузить до 5 файлов.
                            </div>
                            
                            <div id="additionalPhotosContainer">
                                <!-- Первое дополнительное поле -->
                                <div class="input-group mb-2">
                                    <input type="file" class="form-control additional-photo" 
                                           name="additional_photos[]" accept="image/jpeg,image/png">
                                    <button type="button" class="btn btn-outline-secondary remove-photo-btn" style="display: none;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <button type="button" id="addMorePhotosBtn" class="btn btn-outline-primary btn-sm mt-2">
                                <i class="bi bi-plus-circle"></i> Добавить еще фото
                            </button>
                        </div>
                        
                        <!-- Галерея предпросмотра дополнительных фото -->
                        <div id="additionalPreviews" class="row mt-3" style="display: none;">
                            <div class="col-12">
                                <p class="small text-muted mb-2">Предпросмотр дополнительных фото:</p>
                                <div class="d-flex flex-wrap gap-2" id="additionalPreviewsContainer">
                                    <!-- Здесь будут появляться миниатюры -->
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Данные владельца -->
                    <div class="mb-5">
                        <h4 class="border-bottom pb-2 mb-3">
                            <i class="bi bi-person-badge"></i> Данные владельца
                        </h4>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="owner_name" class="form-label">
                                    ФИО владельца <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="owner_name" name="owner_name" required>
                            </div>

                            <div class="col-md-6">
                                <label for="owner_phone" class="form-label">
                                    Телефон <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="owner_phone" name="owner_phone" 
                                       placeholder="+7 (999) 123-45-67" pattern="\d{11}" required>
                                <div class="invalid-feedback">Введите 11-значный номер телефона</div>       
                            </div>
                            
                            <div class="col-md-12">
                                <label for="owner_email" class="form-label">Email<span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="owner_email" name="owner_email" required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Кнопки -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="?page=home" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Назад
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle"></i> Зарегистрировать питомца
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Скрипт для этой страницы -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Предпросмотр основного фото
    const mainPhotoInput = document.getElementById('main_photo');
    const mainPreviewContainer = document.getElementById('mainPhotoPreview');
    const noMainPhotoContainer = document.getElementById('noMainPhoto');
    const mainPreviewImage = document.getElementById('mainPreviewImage');
    
    if (mainPhotoInput) {
        mainPhotoInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    mainPreviewImage.src = e.target.result;
                    mainPreviewContainer.style.display = 'block';
                    noMainPhotoContainer.style.display = 'none';
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
    
    // Управление дополнительными фото
    const additionalPhotosContainer = document.getElementById('additionalPhotosContainer');
    const addMorePhotosBtn = document.getElementById('addMorePhotosBtn');
    const additionalPreviews = document.getElementById('additionalPreviews');
    const additionalPreviewsContainer = document.getElementById('additionalPreviewsContainer');
    
    let photoCounter = 1;
    const maxPhotos = 5;
    
    // Функция для создания превью дополнительного фото
    function createPhotoPreview(file, index) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewDiv = document.createElement('div');
            previewDiv.className = 'position-relative';
            previewDiv.style.width = '100px';
            previewDiv.innerHTML = `
                <img src="${e.target.result}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-preview-btn" 
                        data-index="${index}" style="transform: translate(30%, -30%);">
                    <i class="bi bi-x"></i>
                </button>
            `;
            additionalPreviewsContainer.appendChild(previewDiv);
            
            // Событие для удаления превью
            previewDiv.querySelector('.remove-preview-btn').addEventListener('click', function() {
                this.closest('.position-relative').remove();
                // Удаляем соответствующее поле ввода
                const inputs = document.querySelectorAll('.additional-photo');
                if (inputs[index]) {
                    inputs[index].value = '';
                }
            });
        };
        reader.readAsDataURL(file);
    }
    
    // Добавление нового поля для фото
    if (addMorePhotosBtn) {
        addMorePhotosBtn.addEventListener('click', function() {
            if (photoCounter < maxPhotos) {
                const newInputGroup = document.createElement('div');
                newInputGroup.className = 'input-group mb-2';
                newInputGroup.innerHTML = `
                    <input type="file" class="form-control additional-photo" 
                           name="additional_photos[]" accept="image/jpeg,image/png">
                    <button type="button" class="btn btn-outline-secondary remove-photo-btn">
                        <i class="bi bi-trash"></i>
                    </button>
                `;
                
                additionalPhotosContainer.appendChild(newInputGroup);
                photoCounter++;
                
                // Показываем кнопки удаления если больше 1 поля
                if (photoCounter > 1) {
                    document.querySelectorAll('.remove-photo-btn').forEach(btn => {
                        btn.style.display = 'block';
                    });
                }
                
                // Если достигли максимума - скрываем кнопку добавления
                if (photoCounter >= maxPhotos) {
                    addMorePhotosBtn.style.display = 'none';
                }
                
                // Добавляем обработчик изменения для нового поля
                const newInput = newInputGroup.querySelector('.additional-photo');
                newInput.addEventListener('change', function(e) {
                    if (this.files && this.files[0]) {
                        // Показываем контейнер превью
                        additionalPreviews.style.display = 'block';
                        
                        // Создаем превью
                        createPhotoPreview(this.files[0], photoCounter - 1);
                    }
                });
                
                // Добавляем обработчик удаления поля
                const removeBtn = newInputGroup.querySelector('.remove-photo-btn');
                removeBtn.addEventListener('click', function() {
                    this.closest('.input-group').remove();
                    photoCounter--;
                    
                    // Показываем кнопку добавления если не достигли максимума
                    if (photoCounter < maxPhotos) {
                        addMorePhotosBtn.style.display = 'block';
                    }
                    
                    // Скрываем кнопки удаления если осталось 1 поле
                    if (photoCounter <= 1) {
                        document.querySelectorAll('.remove-photo-btn').forEach(btn => {
                            btn.style.display = 'none';
                        });
                    }
                });
            }
        });
    }
    
    // Обработчик для первого дополнительного фото
    const firstAdditionalInput = document.querySelector('.additional-photo');
    if (firstAdditionalInput) {
        firstAdditionalInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                additionalPreviews.style.display = 'block';
                createPhotoPreview(this.files[0], 0);
            }
        });
    }
    
    // Валидация формы
    const form = document.getElementById('registerForm');
    if (form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
            
            // Дополнительная проверка размера файлов
            const maxSize = 5 * 1024 * 1024; // 5MB
            const mainPhoto = document.getElementById('main_photo').files[0];
            
            if (mainPhoto && mainPhoto.size > maxSize) {
                event.preventDefault();
                alert('Основное фото не должно превышать 5MB');
                return;
            }
            
            // Проверка дополнительных фото
            const additionalInputs = document.querySelectorAll('.additional-photo');
            additionalInputs.forEach(input => {
                if (input.files[0] && input.files[0].size > maxSize) {
                    event.preventDefault();
                    alert('Дополнительные фото не должны превышать 5MB');
                    return;
                }
            });
        });
    }
});
</script>

<style>
.remove-preview-btn {
    width: 24px;
    height: 24px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
}
</style>