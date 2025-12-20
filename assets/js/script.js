/**
 * Базовый JavaScript для прототипа системы поиска животных
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Система поиска животных загружена');
    
    // ========== ПРЕДПРОСМОТР ИЗОБРАЖЕНИЙ ==========
    
    // Предпросмотр для формы регистрации
    const registerPhotoInput = document.getElementById('photo');
    if (registerPhotoInput) {
        registerPhotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            const previewContainer = document.getElementById('preview-container');
            const previewImage = document.getElementById('photo-preview');
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.style.display = 'block';
                    
                    // Показываем информацию о файле
                    const fileInfo = document.createElement('div');
                    fileInfo.className = 'mt-2 small text-muted';
                    fileInfo.innerHTML = `
                        <div>Имя: ${file.name}</div>
                        <div>Размер: ${(file.size / 1024 / 1024).toFixed(2)} MB</div>
                    `;
                    
                    // Удаляем старую информацию, если есть
                    const oldInfo = previewContainer.querySelector('.file-info');
                    if (oldInfo) oldInfo.remove();
                    
                    fileInfo.className = 'file-info mt-2 small text-muted';
                    previewContainer.appendChild(fileInfo);
                };
                
                reader.readAsDataURL(file);
            } else {
                previewContainer.style.display = 'none';
            }
        });
    }
    
    // ========== ВАЛИДАЦИЯ ФОРМ ==========
    
    // Отключаем отправку форм, если есть невалидные поля
    const forms = document.querySelectorAll('.needs-validation');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
            
            // Дополнительная проверка для номера чипа
            const chipInput = form.querySelector('#chip_id');
            if (chipInput) {
                const chipValue = chipInput.value.trim();
                if (!/^\d{15}$/.test(chipValue)) {
                    chipInput.classList.add('is-invalid');
                    chipInput.nextElementSibling.textContent = 'Номер чипа должен содержать ровно 15 цифр';
                    event.preventDefault();
                }
            }
        });
    });
    
    // Динамическая валидация номера чипа
    const chipInputs = document.querySelectorAll('input[name="chip_id"]');
    chipInputs.forEach(input => {
        input.addEventListener('input', function() {
            const value = this.value.trim();
            
            // Удаляем все нецифровые символы
            const digitsOnly = value.replace(/\D/g, '');
            
            // Ограничиваем 15 цифрами
            if (digitsOnly.length > 15) {
                this.value = digitsOnly.substring(0, 15);
            } else {
                this.value = digitsOnly;
            }
            
            // Визуальная обратная связь
            if (this.value.length === 15) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
            }
        });
    });
    
    // ========== ИНИЦИАЛИЗАЦИЯ КОМПОНЕНТОВ BOOTSTRAP ==========
    
    // Инициализируем все всплывающие подсказки
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // ========== ПОЛЕЗНЫЕ ФУНКЦИИ ==========
    
    /**
     * Показывает уведомление
     */
    window.showNotification = function(message, type = 'info') {
        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        }[type] || 'alert-info';
        
        const alert = document.createElement('div');
        alert.className = `alert ${alertClass} alert-dismissible fade show`;
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Добавляем в начало body
        document.body.insertBefore(alert, document.body.firstChild);
        
        // Автоматически скрываем через 5 секунд
        setTimeout(() => {
            if (alert.parentNode) {
                alert.parentNode.removeChild(alert);
            }
        }, 5000);
    };
    
    /**
     * Форматирует телефонный номер
     */
    window.formatPhoneNumber = function(phone) {
        // Простой форматтер для российских номеров
        const cleaned = phone.replace(/\D/g, '');
        if (cleaned.length === 11) {
            return `+${cleaned[0]} (${cleaned.substring(1, 4)}) ${cleaned.substring(4, 7)}-${cleaned.substring(7, 9)}-${cleaned.substring(9, 11)}`;
        }
        return phone;
    };
});