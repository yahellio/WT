<?php
$servername = "192.168.142.128:3306";
$username = "phpuser";
$password = "123456";
$dbname = "testDB";

try {
    // Подключение к базе данных
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Создание таблиц
    $sqlAuthors = "CREATE TABLE IF NOT EXISTS authors (
        id INT(11) NOT NULL AUTO_INCREMENT,
        name VARCHAR(50) NOT NULL,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
    $conn->exec($sqlAuthors);

    $sql = "CREATE TABLE IF NOT EXISTS titles (
        id INT(11) NOT NULL AUTO_INCREMENT,
        author_id INT(11) NOT NULL,
        title VARCHAR(255) NOT NULL,
        text TEXT NOT NULL,
        image VARCHAR(255) DEFAULT NULL,
        date_publication TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        hide TINYINT(1) NOT NULL DEFAULT 0,
        opinion ENUM('positive', 'neutral', 'negative') DEFAULT 'neutral',
        PRIMARY KEY (id),
        UNIQUE KEY title_unique (title),
        CONSTRAINT fk_author FOREIGN KEY (author_id) 
        REFERENCES authors (id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
    $conn->exec($sql);

    // Вывод данных в HTML-таблицу
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Данные из базы</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
            h2 { margin-top: 30px; }
            .hidden { background-color: #ffecec; }
        </style>
    </head>
    <body>";

    // 1. Вывод таблицы authors
    echo "<h2>Авторы</h2>";
    $stmt = $conn->query("SELECT * FROM authors ORDER BY name");
    echo "<table>
            <tr>
                <th>ID</th>
                <th>Имя автора</th>
            </tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>
                <td>" . htmlspecialchars($row['id']) . "</td>
                <td>" . htmlspecialchars($row['name']) . "</td>
              </tr>";
    }
    echo "</table>";

    // 2. Вывод таблицы titles
    echo "<h2>Статьи</h2>";
    $stmt = $conn->query("SELECT * FROM titles ORDER BY date_publication DESC");
    echo "<table>
            <tr>
                <th>ID</th>
                <th>ID автора</th>
                <th>Заголовок</th>
                <th>Дата публикации</th>
                <th>Статус</th>
                <th>Мнение</th>
            </tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $rowClass = $row['hide'] ? 'class="hidden"' : '';
        echo "<tr $rowClass>
                <td>" . htmlspecialchars($row['id']) . "</td>
                <td>" . htmlspecialchars($row['author_id']) . "</td>
                <td>" . htmlspecialchars($row['title']) . "</td>
                <td>" . htmlspecialchars($row['date_publication']) . "</td>
                <td>" . ($row['hide'] ? 'Скрыта' : 'Опубликована') . "</td>
                <td>" . htmlspecialchars($row['opinion']) . "</td>
              </tr>";
    }
    echo "</table>";

    // 3. Вывод объединённых данных (авторы и их статьи)
    echo "<h2>Авторы и их статьи</h2>";
    $stmt = $conn->query("
        SELECT 
            a.id as author_id, 
            a.name as author_name, 
            t.id as title_id, 
            t.title, 
            t.date_publication,
            t.hide
        FROM 
            authors a
        LEFT JOIN 
            titles t ON a.id = t.author_id
        ORDER BY 
            a.name, t.date_publication DESC
    ");

    echo "<table>
            <tr>
                <th>ID автора</th>
                <th>Имя автора</th>
                <th>ID статьи</th>
                <th>Заголовок</th>
                <th>Дата публикации</th>
                <th>Статус</th>
            </tr>";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $rowClass = ($row['hide'] ?? false) ? 'class="hidden"' : '';
        echo "<tr $rowClass>
                <td>" . htmlspecialchars($row['author_id']) . "</td>
                <td>" . htmlspecialchars($row['author_name']) . "</td>
                <td>" . ($row['title_id'] ? htmlspecialchars($row['title_id']) : '—') . "</td>
                <td>" . ($row['title'] ? htmlspecialchars($row['title']) : 'Нет статей') . "</td>
                <td>" . ($row['date_publication'] ? htmlspecialchars($row['date_publication']) : '—') . "</td>
                <td>" . (isset($row['hide']) ? ($row['hide'] ? 'Скрыта' : 'Опубликована') : '—') . "</td>
              </tr>";
    }
    echo "</table>";

    echo "</body></html>";

} catch (PDOException $e) {
    echo "<p style='color: red;'>Ошибка: " . $e->getMessage() . "</p>";
}

$conn = null;
?>