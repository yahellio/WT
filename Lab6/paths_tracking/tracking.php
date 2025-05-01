<?php
$trackingDataFile = __DIR__ . '/user_paths.json';
$sessionDuration = 1800; // 30 минут неактивности
$minPageInterval = 5; // Минимальный интервал между записями одной страницы (сек)

session_start();

// Улучшенная функция установки cookie
function setPersistentCookie($name, $value, $days = 30) {
    $expire = time() + $days * 86400;
    $options = [
        'expires' => $expire,
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ];
    setcookie($name, $value, $options);
    $_COOKIE[$name] = $value; // Немедленно доступно для текущего скрипта
}

// Функция для очистки URL
function cleanUrl($url) {
    if (empty($url)) return null;
    $url = parse_url($url, PHP_URL_PATH) ?: $url;
    $url = preg_replace('/\/{2,}/', '/', $url);
    return rtrim($url, '/') ?: '/';
}

$currentUrl = cleanUrl($_SERVER['REQUEST_URI']);
$currentTime = time();

// Инициализация или восстановление visitor_id
if (!isset($_COOKIE['visitor_id'])) {
    $visitorId = uniqid('visitor_', true);
    setPersistentCookie('visitor_id', $visitorId);
} else {
    $visitorId = $_COOKIE['visitor_id'];
}

// Инициализация данных сессии
if (!isset($_SESSION['tracking_data'])) {
    $_SESSION['tracking_data'] = [
        'session_id' => session_id(),
        'start_time' => $currentTime,
        'last_activity' => $currentTime,
        'pages' => [],
        'saved' => false
    ];
}

$trackingData = &$_SESSION['tracking_data'];

// Проверка времени неактивности
if ($currentTime - $trackingData['last_activity'] > $sessionDuration) {
    // Сохраняем текущую сессию перед началом новой
    saveUserPath($trackingData, $visitorId);

    // Начинаем новую сессию
    session_regenerate_id(true);
    $trackingData = [
        'session_id' => session_id(),
        'start_time' => $currentTime,
        'last_activity' => $currentTime,
        'pages' => [],
        'saved' => false
    ];
}

// Обновление времени последней активности
$trackingData['last_activity'] = $currentTime;

// Получение данных о последней посещенной странице
$lastPage = end($trackingData['pages']);
$lastUrl = $lastPage['url'] ?? null;
$lastTime = $lastPage['time'] ?? 0;

// Добавление новой страницы в историю
if ($currentUrl !== $lastUrl || ($currentTime - $lastTime) >= $minPageInterval) {
    $trackingData['pages'][] = [
        'url' => $currentUrl,
        'time' => $currentTime,
        'referrer' => isset($_SERVER['HTTP_REFERER']) ? cleanUrl($_SERVER['HTTP_REFERER']) : null
    ];
    $trackingData['saved'] = false;
}

// Функция сохранения пути пользователя
function saveUserPath(&$trackingData, $visitorId) {
    global $trackingDataFile;

    if (empty($trackingData['pages']) || $trackingData['saved']) {
        return;
    }

    // Формируем полные данные для сохранения
    $pathRecord = [
        'visitor_id' => $visitorId,
        'session_id' => $trackingData['session_id'],
        'ip_address' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
        'start_time' => $trackingData['start_time'],
        'end_time' => time(),
        'duration' => time() - $trackingData['start_time'],
        'pages' => $trackingData['pages']
    ];

    // Загрузка и обновление данных
    $data = [];
    if (file_exists($trackingDataFile)) {
        $json = file_get_contents($trackingDataFile);
        $data = json_decode($json, true) ?: [];
    }

    // Удаляем возможные дубликаты по session_id
    $data = array_filter($data, function($record) use ($trackingData) {
        return $record['session_id'] !== $trackingData['session_id'];
    });

    // Добавляем новую запись и ограничиваем количество
    array_unshift($data, $pathRecord);
    $data = array_slice($data, 0, 100);

    // Сохранение в файл
    file_put_contents($trackingDataFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

    $trackingData['saved'] = true;
}

// Автоматическое сохранение при завершении скрипта
register_shutdown_function(function() use (&$trackingData, $visitorId) {
    if (!empty($trackingData) && !$trackingData['saved']) {
        saveUserPath($trackingData, $visitorId);
    }
    session_write_close();
});