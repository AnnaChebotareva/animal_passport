<?php
/**
 * Класс для обработки изображений и извлечения признаков
 * Реализует упрощенный алгоритм на основе цветовой гистограммы
 */
require_once '../config/database.php';
class ImageProcessor {
    
    // Размер изображения для обработки (уменьшаем для скорости)
    const TARGET_SIZE = 200;
    
    /**
     * Основной метод обработки изображения
     * @param string $image_path - путь к файлу изображения
     * @return array - вектор признаков (цветовая гистограмма)
     */
    public function processImage($image_path) {
        
        // Проверяем существование файла
        if (!file_exists($image_path)) {
            throw new Exception("Файл изображения не найден: " . $image_path);
        }
        
        // Определяем тип изображения и создаем ресурс
        $image_type = exif_imagetype($image_path);
        $image = null;
        
        switch ($image_type) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($image_path);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($image_path);
                break;
            default:
                throw new Exception("Неподдерживаемый формат изображения");
        }
        
        if (!$image) {
            throw new Exception("Не удалось загрузить изображение");
        }
        
        // Изменяем размер изображения
        $resized_image = $this->resizeImage($image);
        
        // Генерируем цветовую гистограмму
        $histogram = $this->generateColorHistogram($resized_image);
        
        // Очищаем память
        imagedestroy($image);
        imagedestroy($resized_image);
        
        return $histogram;
    }
    
    /**
     * Изменяет размер изображения до целевого размера
     */
    private function resizeImage($image) {
        $width = imagesx($image);
        $height = imagesy($image);
        
        // Создаем новое изображение с целевым размером
        $new_image = imagecreatetruecolor(self::TARGET_SIZE, self::TARGET_SIZE);
        
        // Копируем и изменяем размер с ресемплированием
        imagecopyresampled(
            $new_image, $image,
            0, 0, 0, 0,
            self::TARGET_SIZE, self::TARGET_SIZE,
            $width, $height
        );
        
        return $new_image;
    }
    
    /**
     * Генерирует упрощенную цветовую гистограмму
     * Делит RGB пространство на 8x8x8 = 512 корзин
     */
    private function generateColorHistogram($image) {
        $width = imagesx($image);
        $height = imagesy($image);
        
        // Инициализируем гистограмму (512 корзин)
        $histogram = array_fill(0, 512, 0);
        $total_pixels = $width * $height;
        
        // Проходим по всем пикселям изображения
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                // Получаем цвет пикселя
                $rgb = imagecolorat($image, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                
                // Квантуем цвет (делим на 32, так как 256/8 = 32)
                $r_index = intval($r / 32); // 0-7
                $g_index = intval($g / 32); // 0-7
                $b_index = intval($b / 32); // 0-7
                
                // Вычисляем индекс в гистограмме
                $hist_index = ($r_index * 64) + ($g_index * 8) + $b_index;
                
                $histogram[$hist_index]++;
            }
        }
        
        // Нормализуем гистограмму (делим на общее количество пикселей)
        for ($i = 0; $i < 512; $i++) {
            $histogram[$i] = $histogram[$i] / $total_pixels;
        }
        
        return $histogram;
    }
    
    /**
     * Сравнивает две гистограммы и возвращает оценку схожести
     * @param array $hist1 - первая гистограмма
     * @param array $hist2 - вторая гистограмма
     * @return float - оценка схожести от 0 до 1
     */
    public function compareHistograms($hist1, $hist2) {
        $similarity = 0;
        
        // Используем метрику пересечения гистограмм
        for ($i = 0; $i < min(count($hist1), count($hist2)); $i++) {
            $similarity += min($hist1[$i], $hist2[$i]);
        }
        
        return $similarity;
    }
}
?>