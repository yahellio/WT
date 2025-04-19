<?php
// Настройки подключения к БД
$db_host = '192.168.44.128:3306';
$db_name = 'testDB';
$db_user = 'phpuser';
$db_pass = '123456';

// Функция для форматирования размера памяти
function formatMemory($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $index = 0;
    while ($bytes >= 1024 && $index < count($units)) {  // Добавлена закрывающая скобка )
        $bytes /= 1024;
        $index++;
    }
    return round($bytes, 2) . ' ' . $units[$index];
}

try {
    // Подключение к БД
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // HTML-форма
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>SQL Query Executor</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            textarea { width: 100%; height: 100px; }
            table { border-collapse: collapse; width: 100%; margin-top: 20px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
            .stats { margin-top: 20px; padding: 10px; background: #f5f5f5; }
            .error { color: red; }
            .success { color: green; }
        </style>
    </head>
    <body>
        <h1>SQL Query Executor</h1>
        <form method="post">
            <textarea name="sql_query" placeholder="Введите SQL-запрос или несколько запросов через точку с запятой"></textarea><br>
            <input type="submit" value="Выполнить">
        </form>';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['sql_query'])) {
        $queries = explode(';', $_POST['sql_query']);
        $queries = array_filter(array_map('trim', $queries));

        foreach ($queries as $query){

            echo '<div class="query-block"><h3>Запрос:</h3><pre>' . htmlspecialchars($query) . '</pre>';

            try {
                // Замер времени и памяти
                $startTime = microtime(true);
                $startMemory = memory_get_usage();

                // Выполнение запроса
                $stmt = $conn->query($query);

                $endTime = microtime(true);
                $endMemory = memory_get_usage();

                // Статистика
                $executionTime = round(($endTime - $startTime) * 1000, 2); // в мс
                $memoryUsed = formatMemory($endMemory - $startMemory);

                echo '<div class="stats success">';
                echo 'Время выполнения: ' . $executionTime . ' мс<br>';
                echo 'Использовано памяти: ' . $memoryUsed . '<br>';
                echo 'Затронуто строк: ' . $stmt->rowCount() . '</div>';

                // Вывод результатов (если есть)
                if ($stmt->columnCount() > 0) {
                    echo '<table><tr>';
                    // Заголовки таблицы
                    for ($i = 0; $i < $stmt->columnCount(); $i++) {
                        $meta = $stmt->getColumnMeta($i);
                        echo '<th>' . htmlspecialchars($meta['name']) . '</th>';
                    }
                    echo '</tr>';

                    // Данные
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo '<tr>';
                        foreach ($row as $value) {
                            echo '<td>' . htmlspecialchars($value ?? 'NULL') . '</td>';
                        }
                        echo '</tr>';
                    }
                    echo '</table>';
                }
            } catch (PDOException $e) {
                echo '<div class="stats error">Ошибка: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }

            echo '</div><hr>';
        }
    }

    echo '</body></html>';

} catch (PDOException $e) {
    die('<div class="error">Ошибка подключения к БД: ' . htmlspecialchars($e->getMessage()) . '</div>');
}
?>