<?php
/*
 * Генерацию HTML-таблицы с указанным количеством строк (передаётся как параметр командной строки),
 * цвет фона которых меняется от белого к чёрному (количество «шагов» такого изменения фона должно быть равно количеству строк таблицы).
 */

// Проверка параметров
if ($argc < 2 || !is_numeric($argv[1])) {
    die("Ошибка: Укажите количество строк как целое число.");
}

$rows = (int)$argv[1];
if ($rows <= 0) {
    die("Ошибка: Количество строк должно быть положительным числом.\n");
}

$html = '<!DOCTYPE html>
<html>
<head>
    <title>Градиентная таблица</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin: 20px 0;
        }
        td {
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <h2>Таблица с градиентом ('.$rows.' строк)</h2>
    <table>';

for ($i = 0; $i < $rows; $i++) {
    if ($rows === 1) {
        $gray = 127;
    } else {
        $gray = floor(255 * ($rows - $i - 1) / ($rows - 1));
    }

    // Форматируем цвет в HEX
    $color = sprintf("#%02x%02x%02x", $gray, $gray, $gray);

    // Выбираем цвет текста для контраста
    $textColor = $i < $rows / 2 ? 'black' : 'white';

    $html .= '<tr style="background-color: '.$color.'; color: '.$textColor.'">
            <td>Строка '.($i+1).'</td>
            <td>Цвет: '.$color.'</td>
            <td>RGB: '.$gray.','.$gray.','.$gray.'</td>
          </tr>';
}

$html .= '</table>
</body>
</html>';

// Сохраняем в файл
$filename = 'gradient_table_'.$rows.'.html';
file_put_contents($filename, $html);

exec("start $filename");

echo "Файл $filename успешно создан и открыт в браузере.\n";
?>