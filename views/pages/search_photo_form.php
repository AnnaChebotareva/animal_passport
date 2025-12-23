<div class="container mt-5">
    <h1 class="text-center mb-4"><i class="bi bi-camera"></i> Поиск животного по фотографии</h1>
    
    <div class="row justify-content-center">
        <div class="col-md-8">        
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-upload"></i> Загрузите фотографию
                    </h4>
                </div>
                <div class="card-body">
                    <form id="searchForm" action="./controllers/SearchController.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="search_type" value="photo">
                        
                        <div class="mb-4">
                            <label for="search_photo" class="form-label h5">
                                Выберите фотографию животного <span class="text-danger">*</span>
                            </label>
                            <input type="file" class="form-control" id="search_photo" name="search_photo" 
                                   accept="image/jpeg,image/png" required>
                            <div class="form-text">
                                <i class="bi bi-info-circle"></i> JPG или PNG, до 5MB. Животное должно быть видно четко, желательно в анфас.
                            </div>
                        </div>
                        
                        <!-- Предпросмотр -->
                        <div class="text-center mb-4">
                            <div id="previewContainer" class="border rounded p-3" style="display: none;">
                                <p class="text-muted mb-2">Предпросмотр:</p>
                                <img id="imagePreview" class="img-fluid rounded shadow" style="max-height: 300px;">
                            </div>
                            <div id="noPreview" class="text-muted p-3 border rounded">
                                <i class="bi bi-image display-6"></i>
                                <p class="mt-2">Здесь появится предпросмотр выбранной фотографии</p>
                            </div>
                        </div>
                        
                        <!-- Советы по фото -->
                        <div class="alert alert-info">
                            <h6><i class="bi bi-tips"></i> Советы для лучшего результата:</h6>
                            <ul class="mb-0">
                                <li>Снимайте при хорошем освещении</li>
                                <li>Старайтесь запечатлеть морду животного в анфас</li>
                                <li>Избегайте размытых и темных фотографий</li>
                            </ul>
                        </div>
                        
                        <!-- Кнопки -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="?page=search" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Назад к выбору
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-search"></i> Начать поиск
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const photoInput = document.getElementById('search_photo');
    const previewContainer = document.getElementById('previewContainer');
    const noPreview = document.getElementById('noPreview');
    const previewImage = document.getElementById('imagePreview');
    
    if (photoInput) {
        photoInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.style.display = 'block';
                    noPreview.style.display = 'none';
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
});
</script>