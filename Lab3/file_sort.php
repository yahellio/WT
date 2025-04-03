<?php

// Проверяем, передан ли параметр с именем файла
if (!isset($argv[1])) {
    die("Ошибка: Укажите имя файла как параметр.\nПример: php sort.php file.txt\n");
}

$filename = $argv[1];

// Проверяем существование файла
if (!file_exists($filename)) {
    die("Ошибка: Файл '$filename' не найден.\n");
}

// Читаем все строки файла в массив
$lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

if ($lines === false) {
    die("Ошибка: Не удалось прочитать файл.\n");
}

// Сортируем строки по алфавиту без учета регистра
sort($lines, SORT_STRING | SORT_FLAG_CASE);

// Записываем отсортированные строки обратно в файл
file_put_contents($filename, implode("\n", $lines) . "\n");

echo "Файл '$filename' успешно отсортирован.\n";
