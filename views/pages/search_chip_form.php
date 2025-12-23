<div class="container mt-5">
    <h1 class="text-center mb-4"><i class="bi bi-upc-scan"></i> Поиск животного по номеру чипа</h1>
    
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-credit-card-2-front"></i> Введите номер микрочипа
                    </h4>
                </div>
                <div class="card-body">
                    <form id="searchForm" action="./controllers/SearchController.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="search_type" value="chip">
                        
                        <div class="mb-4">
                            <label for="chip_number" class="form-label h5">
                                15-значный номер микрочипа <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-lg" 
                                   id="chip_number" name="chip_number" 
                                   placeholder="Введите 15 цифр без пробелов"
                                   pattern="\d{15}"
                                   maxlength="15"
                                   required>
                            <div class="form-text">
                                <i class="bi bi-info-circle"></i> Номер состоит из 15 цифр. Обычно указан на ошейнике или в ветеринарном паспорте.
                            </div>
                            <div class="invalid-feedback">Пожалуйста, введите 15-значный номер чипа</div>
                        </div>
                        
                        <!-- Информация о микрочипах -->
                        <div class="alert alert-warning">
                            <h6><i class="bi bi-exclamation-triangle"></i> Где найти номер чипа?</h6>
                            <ul class="mb-0">
                                <li>На ошейнике (часто на бирке)</li>
                                <li>В ветеринарном паспорте животного</li>
                                <li>В документах от ветеринарной клиники</li>
                                <li>С помощью специального сканера микрочипов</li>
                            </ul>
                        </div>
                        
                        <!-- Кнопки -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="?page=search" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Назад к выбору
                            </a>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-search"></i> Найти животное
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Что делать если нашел животное -->
            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-question-circle"></i> Что делать, если вы нашли животное?</h5>
                </div>
                <div class="card-body">
                    <ol>
                        <li>Проверьте наличие ошейника с контактами</li>
                        <li>Попробуйте найти владельца через этот поиск</li>
                        <li>Если номер чипа не найден, обратитесь в ближайшую ветклинику</li>
                        <li>Разместите объявление в местных группах соцсетей</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('chipSearchForm');
    const chipInput = document.getElementById('chip_number');
    
    if (form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    }
    
    if (chipInput) {
        // Разрешаем вводить только цифры
        chipInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
            
            // Автоматически форматируем в группы по 3 цифры
            if (this.value.length > 15) {
                this.value = this.value.substring(0, 15);
            }
        });
    }
});
</script>