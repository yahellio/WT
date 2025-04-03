<?php
// Функция для рекурсивного устранения дубликатов в многомерном массиве
function array_unique_recursive($array, &$processed = null) {
    // Инициализируем $processed только на первом уровне рекурсии
    if ($processed === null) {
        $processed = [];
    }

    $result = [];
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            // Передаем $processed по ссылке, чтобы все уровни использовали один и тот же массив
            $result[$key] = array_unique_recursive($value, $processed);
        } else {
            if (!in_array($value, $processed, true)) {
                $processed[] = $value;
                $result[$key] = $value;
            }
        }
    }
    return $result;
}

// Пример использования
$testArray = array(
    "a" => 100,
    "b" => array("x" => 100, "y" => 200, "z" => 100),
    "c" => 200,
    "d" => array(100, 200, 300, 100),
    "e" => 100
);

$uniqueArray = array_unique_recursive($testArray);

// Вывод результата
echo "Исходный массив:\n";
print_r($testArray);

echo "\nМассив без дубликатов:\n";
print_r($uniqueArray);
