<?php
require_once 'FileSystemObject.php';

// Получаем текущий каталог
$currentDirectory = getcwd();

// Получаем список файлов и папок
$contents = FileSystemObject::getDirectoryContents($currentDirectory);

// Выводим информацию о файлах в MB
echo "Содержимое каталога {$currentDirectory}:" . PHP_EOL;
echo str_repeat('-', 50) . PHP_EOL;
echo sprintf("%-30s %-10s %-10s", "Имя", "Тип", "Размер (MB)") . PHP_EOL;
echo str_repeat('-', 50) . PHP_EOL;

foreach ($contents as $item) {
    if ($item->getType() === 'file') {
        echo sprintf("%-30s %-10s %-10.2f",
                $item->getName(),
                $item->getType(),
                $item->getSize('B')
            ) . PHP_EOL;
    }
}