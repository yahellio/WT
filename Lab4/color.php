<?php
// Обработка данных формы
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['text'])) {
    $originalText = $_POST['text'];
    $formattedText = $originalText;

    // 1. Форматирование целых чисел (синий цвет)
    $formattedText = preg_replace_callback(
        '/(?<!\d|\.)\d+(?!\d|\.)/',
        function($matches) {
            return '<span class="integer">'.$matches[0].'</span>';
        },
        $formattedText
    );

    // 2. Форматирование дробей (красный цвет + округление)
    $formattedText = preg_replace_callback(
        '/\d+\.\d+/',
        function($matches) {
            $rounded = round($matches[0], 1);
            return '<span class="fraction">'.$rounded.'</span>';
        },
        $formattedText
    );

    // 3. Форматирование слов с заглавной буквы (зеленый цвет)
    $formattedText = preg_replace_callback(
        '/\b[A-ZА-ЯЁ][a-zа-яё]*\b/u',
        function($matches) {
            return '<span class="capital">'.$matches[0].'</span>';
        },
        $formattedText
    );
}

// Вывод HTML
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Форматирование текста</title>
    <style>
        .integer { color: blue; }
        .fraction { color: red; }
        .capital { color: green; }
        .result {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <h1>Форматирование текста</h1>
    <form method="post">
        <textarea name="text" rows="10" cols="50" placeholder="Введите текст..."><?php
            echo isset($originalText) ? htmlspecialchars($originalText) : '';
        ?></textarea><br>
        <button type="submit">Форматировать</button>
    </form>

    <?php if (isset($formattedText)): ?>
    <div class="result">
        <h3>Результат:</h3>
        <?php echo $formattedText; ?>
    </div>
    <?php endif; ?>
</body>
</html>