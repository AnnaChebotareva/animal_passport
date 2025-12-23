<div class="container mt-5">
    <h1 class="text-center mb-4"><i class="bi bi-search"></i> Поиск животного по фотографии</h1>
    
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning">
                    <h4 class="mb-0">Загрузите фото найденного животного</h4>
                </div>
                <div class="card-body">
                    <form id="searchForm" action="../controllers/SearchController.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label for="search_photo" class="form-label h5">Выберите фотографию *</label>
                            <input type="file" class="form-control" id="search_photo" name="search_photo" 
                                   accept="image/jpeg,image/png" required>
                            <div class="form-text">Животное должно быть видно четко, желательно в анфас</div>
                        </div>
                        
                        <!-- Предпросмотр загружаемого фото -->
                        <div class="text-center mb-4">
                            <div id="previewContainer" style="display: none;">
                                <p class="text-muted">Предпросмотр:</p>
                                <img id="imagePreview" class="img-fluid rounded shadow" style="max-height: 300px;">
                            </div>
                        </div>
                        
                        <!-- Индикатор загрузки -->
                        <div id="loading" class="text-center mb-3" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Загрузка...</span>
                            </div>
                            <p class="mt-2">Идет поиск в базе данных...</p>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="index.php" class="btn btn-secondary me-md-2">На главную</a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-search"></i> Найти животное
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>