<?php
/**
 * Главный шаблон footer (конец main, body и скрипты)
 */

// Определяем базовый путь
$base_path = dirname(dirname(__DIR__));
$base_url = '/';
?>
    </main> <!-- Закрываем main из header.php -->

    <!-- Подвал -->
    <footer class="bg-dark text-white mt-auto py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-3">
                        <i class="bi bi-search-heart"></i> AnimalPassport
                    </h5>
                    <p class="small mb-0">
                        Курсовой проект. Система поиска животных по цифровому паспорту.
                    </p>
                </div>
                
                <div class="col-md-3">
                    <h5 class="mb-3">Ссылки</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="<?php echo $base_url; ?>" class="text-white-50 text-decoration-none">
                                <i class="bi bi-arrow-right-short"></i> Главная
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo $base_url; ?>?page=register" class="text-white-50 text-decoration-none">
                                <i class="bi bi-arrow-right-short"></i> Регистрация
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo $base_url; ?>?page=search" class="text-white-50 text-decoration-none">
                                <i class="bi bi-arrow-right-short"></i> Поиск
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="col-md-3">
                    <h5 class="mb-3">Технологии</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-primary">PHP</span>
                        <span class="badge bg-success">MySQL</span>
                        <span class="badge bg-info">Bootstrap</span>
                    </div>
                </div>
            </div>
            
            <hr class="bg-light my-3">
            <div class="row">
                <div class="col-md-6">
                    <p class="small mb-0">
                        &copy; <?php echo date('Y'); ?> Курсовая работа
                    </p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="small mb-0">
                        <i class="bi bi-github"></i> 
                        <a href="https://github.com" class="text-white-50 text-decoration-none">
                            Исходный код
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Наши скрипты -->
    <script src="<?php echo $base_url; ?>assets/js/script.js"></script>
    
    <!-- Дополнительные скрипты страницы -->
    <?php if (isset($page_scripts) && is_array($page_scripts)): ?>
        <?php foreach ($page_scripts as $script): ?>
            <script src="<?php echo $base_url . 'assets/js/' . $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
</body>
</html>