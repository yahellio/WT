<?php
session_start();

$uploadDir = 'uploads/';

if (isset($_GET['file'])) {
    $fileName = basename($_GET['file']);
    $filePath = $uploadDir . $fileName;

    if (file_exists($filePath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    } else {
        $_SESSION['message'] = "Файл не найден.";
        header("Location: index.php");
        exit();
    }
} else {
    $_SESSION['message'] = "Не указан файл для скачивания.";
    header("Location: index.php");
    exit();
}
?>