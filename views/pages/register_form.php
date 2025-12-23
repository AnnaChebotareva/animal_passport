<?php
/**
 * Форма регистрации нового животного
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
                    Заполните все обязательные поля (*). Фото требуется для биометрической идентификации.
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
                                <input type="text" class="form-control" id="breed" name="breed" required>
                            </div>

                            <div class="col-md-4">
                                <label for="color" class="form-label">Окрас</label>
                                <input type="text" class="form-control" id="color" name="color" required>
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
                    
                    <!-- Фотография -->
                    <div class="mb-5">
                        <h4 class="border-bottom pb-2 mb-3">
                            <i class="bi bi-camera"></i> Фотография для биометрии
                        </h4>
                        
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="photo" class="form-label">
                                        Фото животного (анфас) <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" class="form-control" id="photo" name="photo" 
                                           accept="image/jpeg,image/png" required>
                                    <div class="form-text">JPG или PNG, до 5MB. Животное должно быть видно четко.</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="text-center">
                                    <div id="photoPreview" class="mb-3" style="display: none;">
                                        <p class="small text-muted">Предпросмотр:</p>
                                        <img id="previewImage" class="img-thumbnail" style="max-width: 200px;">
                                    </div>
                                    <div id="noPhoto" class="text-muted">
                                        <i class="bi bi-image display-6"></i>
                                        <p class="small mt-2">Фото появится здесь после выбора</p>
                                    </div>
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
    // Предпросмотр фото
    const photoInput = document.getElementById('photo');
    const previewContainer = document.getElementById('photoPreview');
    const noPhotoContainer = document.getElementById('noPhoto');
    const previewImage = document.getElementById('previewImage');
    
    if (photoInput) {
        photoInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.style.display = 'block';
                    noPhotoContainer.style.display = 'none';
                }
                
                reader.readAsDataURL(this.files[0]);
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
        });
    }
});
</script>