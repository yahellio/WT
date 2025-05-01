<?php
session_start();
require 'db.php';
global $pdo;

if (!isset($_SESSION['user'])) {

    if (isset($_COOKIE['auth_token'])) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE auth_token = ?");
        $stmt->execute([$_COOKIE['auth_token']]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['user'] = $user['username'];
        } else {
            // Токен в куке не валиден
            setcookie('auth_token', '', time() - 3600, "/");
            header("Location: login.php");
            exit();
        }
    } else {

        header("Location: login.php");
        exit();
    }
}
$uploadDir = 'uploads/';

// Создаем директорию, если ее нет
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Обработка загрузки файла
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $fileName = basename($_FILES['file']['name']);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
        $_SESSION['message'] = "Файл $fileName успешно загружен.";
    } else {
        $_SESSION['message'] = "Ошибка при загрузке файла.";
    }
    header("Location: index.php");
    exit();
}

// Обработка удаления файла
if (isset($_GET['delete'])) {
    $fileToDelete = $uploadDir . basename($_GET['delete']);
    if (file_exists($fileToDelete)) {
        if (unlink($fileToDelete)) {
            $_SESSION['message'] = "Файл " . basename($_GET['delete']) . " успешно удален.";
        } else {
            $_SESSION['message'] = "Ошибка при удалении файла.";
        }
    } else {
        $_SESSION['message'] = "Файл не найден.";
    }
    header("Location: index.php");
    exit();
}

// Получаем список файлов
$files = [];
if (file_exists($uploadDir)) {
    $files = array_diff(scandir($uploadDir), array('..', '.'));
}
?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Файловый менеджер</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .message { padding: 10px; margin: 10px 0; border-radius: 5px; }
            .success { background-color: #d4edda; color: #155724; }
            .error { background-color: #f8d7da; color: #721c24; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
            th { background-color: #f2f2f2; }
            tr:hover { background-color: #f5f5f5; }
            .action-link { margin-right: 10px; }
        </style>
    </head>
    <body>
    <h1>Файловый менеджер</h1>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="message <?php echo strpos($_SESSION['message'], 'Ошибка') !== false ? 'error' : 'success'; ?>">
            <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <h2>Загрузить файл</h2>
    <form action="index.php" method="post" enctype="multipart/form-data">
        <input type="file" name="file" required>
        <button type="submit">Загрузить</button>
    </form>

    <h2>Список файлов</h2>
    <?php if (empty($files)): ?>
        <p>Нет загруженных файлов.</p>
    <?php else: ?>
        <table>
            <thead>
            <tr>
                <th>Имя файла</th>
                <th>Размер</th>
                <th>Дата изменения</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($files as $file): ?>
                <?php $filePath = $uploadDir . $file; ?>
                <tr>
                    <td><?php echo $file; ?></td>
                    <td><?php echo formatSizeUnits(filesize($filePath)); ?></td>
                    <td><?php echo date("d.m.Y H:i:s", filemtime($filePath)); ?></td>
                    <td>
                        <a href="download.php?file=<?php echo urlencode($file); ?>" class="action-link">Скачать</a>
                        <a href="index.php?delete=<?php echo urlencode($file); ?>" class="action-link" onclick="return confirm('Вы уверены, что хотите удалить этот файл?')">Удалить</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <p>Вы вошли как <strong><?php echo htmlspecialchars($_SESSION['user']); ?></strong> | <a href="logout.php">Выйти</a></p>
    </body>
    </html>

<?php
function formatSizeUnits($bytes) {
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }
    return $bytes;
}
?>