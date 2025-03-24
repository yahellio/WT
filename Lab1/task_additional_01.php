<?php
/**
 * Скрипт анализирует GET-параметры и определяет тип каждого параметра:
 * - целое число
 * - дробное число
 * - строка
 */

header('Content-Type: text/html; charset=utf-8');

// Проверяем наличие GET-параметров
if (empty($_GET)) {
    die('<h2>Ошибка: не передано ни одного параметра</h2>');
}

function determineValueType($value) {
    // Проверяем, является ли значение числом
    if (is_numeric($value)) {

        if (strpos($value, '.') !== false) {
            return 'дробное число';
        }

        return 'целое число';
    }
    // Все остальное считаем строкой
    return 'строка';
}

echo '<h2>Анализ GET-параметров</h2>';
echo '<table border="1" cellpadding="5" cellspacing="0">';
echo '<tr><th>Параметр</th><th>Значение</th><th>Тип данных</th></tr>';

// Обрабатываем каждый параметр
foreach ($_GET as $param => $value) {
    // Экранируем специальные символы для безопасного вывода в HTML
    $safeParam = htmlspecialchars($param, ENT_QUOTES, 'UTF-8');
    $safeValue = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    $type = determineValueType($value);

    echo "<tr>
            <td>$safeParam</td>
            <td>$safeValue</td>
            <td>$type</td>
          </tr>";
}

echo '</table>';

// Пример для тестирования
echo '<h3>Примеры для тестирования:</h3>';
echo '<ul>
        <li><a href="?number=42&float=3.14&text=Test">Целое, дробное и строка</a></li>
        <li><a href="?a=100&b=100.00&c=100.01">Разные числовые форматы</a></li>
      </ul>';
?>