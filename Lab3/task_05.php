<?php

// Функция для генерации календаря
function generateCalendar($year)
{
    // Массив основных праздников (месяц => [день => название])
    $holidays = [
        1 => [1 => 'Новый год', 7 => 'Рождество Христово'],
        2 => [23 => 'День защитника Отечества'],
        3 => [8 => 'Международный женский день'],
        5 => [1 => 'Праздник весны и труда', 9 => 'День Победы'],
        11 => [4 => 'День народного единства']
    ];

    $calendar = '<!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <title>Календарь на ' . $year . ' год</title>
        <style>
            table { border-collapse: collapse; width: 100%; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
            th { background-color: #f2f2f2; }
            .holiday { color: red; font-weight: bold; }
            .hint { position: relative; display: inline-block; }
            .hint:hover:after { 
                content: attr(data-hint); 
                position: absolute; 
                left: 100%; 
                top: 0;
                background: #ffffcc;
                border: 1px solid #ccc;
                padding: 5px;
                z-index: 100;
                width: 150px;
            }
        </style>
    </head>
    <body>
        <h1>Календарь на ' . $year . ' год</h1>';

    // Генерация календаря по месяцам
    for ($month = 1; $month <= 12; $month++) {
        $firstDay = mktime(0, 0, 0, $month, 1, $year);
        $daysInMonth = date('t', $firstDay);
        $monthName = date('F', $firstDay);

        $calendar .= '<h2>' . $monthName . '</h2>
        <table>
            <tr>
                <th>Пн</th><th>Вт</th><th>Ср</th><th>Чт</th><th>Пт</th><th>Сб</th><th>Вс</th>
            </tr><tr>';

        // Пустые ячейки для первого дня месяца
        $firstDayOfWeek = date('N', $firstDay);
        for ($i = 1; $i < $firstDayOfWeek; $i++) {
            $calendar .= '<td></td>';
        }

        // Заполнение дней месяца
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDayOfWeek = date('N', mktime(0, 0, 0, $month, $day, $year));

            // Проверка на праздник
            $isHoliday = isset($holidays[$month][$day]);
            $class = $isHoliday ? ' class="holiday"' : '';
            $hint = $isHoliday ? ' data-hint="' . $holidays[$month][$day] . '"' : '';

            $calendar .= '<td' . $class . '><div class="hint"' . $hint . '>' . $day . '</div></td>';

            // Перенос строки в конце недели
            if ($currentDayOfWeek == 7 && $day != $daysInMonth) {
                $calendar .= '</tr><tr>';
            }
        }

        $calendar .= '</tr></table>';
    }

    $calendar .= '</body></html>';

    return $calendar;
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['year'])) {
    $year = (int)$_POST['year'];
    $calendar = generateCalendar($year);

    // Сохранение в файл
    $filename = "calendar_{$year}.html";
    file_put_contents($filename, $calendar);

    echo "Календарь успешно сгенерирован и сохранен в файл <a href='$filename'>$filename</a>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Генератор календаря</title>
</head>
<body>
<h1>Генератор календаря</h1>
<form method="post">
    <label for="year">Введите год:</label>
    <input type="number" id="year" name="year" min="1900" max="2100" value="<?= date('Y') ?>">
    <button type="submit">Сгенерировать календарь</button>
</form>
</body>
</html>