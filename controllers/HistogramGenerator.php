<?php
/**
 * Класс для генерации визуализаций гистограмм
 */
class HistogramGenerator {
    
    /**
     * Генерирует HTML для отображения гистограммы
     */
    public static function generateHistogramHTML($histogram, $title = 'Гистограмма цветов') {
        $html = '<div class="histogram-container">';
        $html .= '<h6 class="histogram-title">' . $title . '</h6>';
        $html .= '<div class="histogram-chart">';
        
        // Создаем упрощенную визуализацию (группируем по 16 бинов)
        $grouped = [];
        $group_size = 32; // 512 / 16 = 32
        for ($i = 0; $i < 16; $i++) {
            $sum = 0;
            for ($j = 0; $j < $group_size; $j++) {
                $index = $i * $group_size + $j;
                if (isset($histogram[$index])) {
                    $sum += $histogram[$index] * 100;
                }
            }
            $grouped[] = $sum / $group_size;
        }
        
        // Находим максимальное значение для масштабирования
        $max = max($grouped);
        if ($max == 0) $max = 1;
        
        // Генерируем столбцы
        $html .= '<div class="histogram-bars">';
        foreach ($grouped as $index => $value) {
            $bar_height = ($value / $max) * 100;
            $bar_color = self::getBarColor($index);
            
            $html .= '<div class="histogram-bar" style="height: ' . $bar_height . '%; background-color: ' . $bar_color . ';"></div>';
        }
        $html .= '</div>';
        
        // Ось X с метками цветовых каналов
        $html .= '<div class="histogram-x-axis">';
        $html .= '<span>R</span><span>G</span><span>B</span><span>RGB</span>';
        $html .= '</div>';
        
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Определяет цвет столбца в зависимости от его позиции
     */
    private static function getBarColor($index) {
        if ($index < 4) {
            return '#ff6b6b';
        } elseif ($index < 8) {
            return '#51cf66';
        } elseif ($index < 12) {
            return '#339af0';
        } else {
            return '#845ef7';
        }
    }
    
    /**
     * Генерирует сравнение двух гистограмм
     */
    public static function generateComparisonHTML($hist1, $hist2, $similarity, $title1 = 'Загруженное фото', $title2 = 'База данных') {
        $html = '<div class="comparison-container">';
        $html .= '<div class="row g-3">';
        
        // Первая гистограмма (поиск)
        $html .= '<div class="col-md-6">';
        $html .= self::generateHistogramHTML($hist1, $title1);
        $html .= '</div>';
        
        // Вторая гистограмма (база)
        $html .= '<div class="col-md-6">';
        $html .= self::generateHistogramHTML($hist2, $title2);
        $html .= '</div>';
        
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Генерирует цветовую легенду
     */
    public static function generateColorLegend() {
        $html = '<div class="color-legend">';
        $html .= '<h6 class="mb-2"><i class="bi bi-palette"></i> Цветовые каналы:</h6>';
        $html .= '<div class="row g-2">';
        
        $colors = [
            ['name' => 'Красный', 'color' => '#ff6b6b', 'desc' => 'R канал'],
            ['name' => 'Зеленый', 'color' => '#51cf66', 'desc' => 'G канал'],
            ['name' => 'Синий', 'color' => '#339af0', 'desc' => 'B канал'],
            ['name' => 'Смешанные', 'color' => '#845ef7', 'desc' => 'RGB смеси']
        ];
        
        foreach ($colors as $item) {
            $html .= '<div class="col-6 col-md-3">';
            $html .= '<div class="d-flex align-items-center p-2 border rounded bg-white">';
            $html .= '<div class="color-box me-2" style="width: 20px; height: 20px; background-color: ' . $item['color'] . '; border-radius: 3px;"></div>';
            $html .= '<div>';
            $html .= '<small class="d-block"><strong>' . $item['name'] . '</strong></small>';
            $html .= '<small class="text-muted d-block">' . $item['desc'] . '</small>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }
        
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
}
?>