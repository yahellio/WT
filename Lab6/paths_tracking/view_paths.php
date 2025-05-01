<?php
$trackingDataFile = __DIR__ . '/user_paths.json';

$data = [];
if (file_exists($trackingDataFile)) {
    $json = file_get_contents($trackingDataFile);
    $data = json_decode($json, true) ?: [];
}

function formatTime($timestamp) {
    return date('Y-m-d H:i:s', $timestamp);
}

function formatDuration($seconds) {
    $h = floor($seconds / 3600);
    $m = floor(($seconds % 3600) / 60);
    $s = $seconds % 60;
    return sprintf('%02dh %02dm %02ds', $h, $m, $s);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Пути пользователей</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f4f4f4; }
        h1 { color: #333; }
        .session {
            background: white;
            border: 1px solid #ccc;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 5px solid #4CAF50;
        }
        .session h2 { margin-top: 0; font-size: 1.1em; color: #4CAF50; }
        ul { padding-left: 20px; }
        li { margin-bottom: 5px; }
        .meta { font-size: 0.9em; color: #666; }
        .ref { font-size: 0.85em; color: #999; }
    </style>
</head>
<body>
<h1>Отслеженные пути пользователей</h1>

<?php foreach ($data as $session): ?>
    <div class="session">
        <h2>Посетитель: <?= htmlspecialchars($session['visitor_id'] ?? 'Неизвестен') ?></h2>
        <p class="meta">
            IP: <?= htmlspecialchars($session['ip_address'] ?? 'N/A') ?><br>
            Браузер: <?= htmlspecialchars($session['user_agent'] ?? 'N/A') ?><br>
            Сессия: <?= htmlspecialchars($session['session_id']) ?><br>
            Начало: <?= formatTime($session['start_time']) ?><br>
            Окончание: <?= formatTime($session['end_time']) ?><br>
            Длительность: <?= formatDuration($session['duration']) ?>
        </p>

        <h3>Посещённые страницы:</h3>
        <ul>
            <?php foreach ($session['pages'] as $page): ?>
                <li>
                    <strong><?= htmlspecialchars($page['url']) ?></strong>
                    <span class="meta">(<?= formatTime($page['time']) ?>)</span><br>
                    <?php if (!empty($page['referrer'])): ?>
                        <span class="ref">откуда: <?= htmlspecialchars($page['referrer']) ?></span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endforeach; ?>
</body>
</html>
