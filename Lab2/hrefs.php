<?php

/*
    На экран вывести ссылки меню с названиями (например  "О компании", "Услуги", "Прайс",
    Контакты").  При клике по ссылке меняется и остается измененным цвет фона вокруг активной
    ссылки. Весь код на одной странице. Не использовать javascript. Использовать GET-запросы
 */

// Определяем текущую активную страницу из GET-параметра
$active_page = isset($_GET['page']) ? $_GET['page'] : '';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Меню с активной ссылкой</title>
    <style>
        .menu {
            display: flex;
            gap: 20px;
            padding: 20px;
            background: #f5f5f5;
        }
        .menu a {
            text-decoration: none;
            color: #333;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .menu a.active {
            background: #4CAF50;
            color: white;
        }
        .menu a:hover {
            background: #ddd;
        }
    </style>
</head>
<body>
    <nav class="menu">
        <a href="?page=about" class="<?= $active_page === 'about' ? 'active' : '' ?>">О компании</a>
        <a href="?page=services" class="<?= $active_page === 'services' ? 'active' : '' ?>">Услуги</a>
        <a href="?page=prices" class="<?= $active_page === 'prices' ? 'active' : '' ?>">Прайс</a>
        <a href="?page=contacts" class="<?= $active_page === 'contacts' ? 'active' : '' ?>">Контакты</a>
    </nav>

    <div class="content">
        <?php
        // Выводим содержимое в зависимости от активной страницы
        switch($active_page) {
            case 'about':
                echo '<h1>О компании</h1><p>Информация о нашей компании...</p>';
                break;
            case 'services':
                echo '<h1>Услуги</h1><p>Наши услуги...</p>';
                break;
            case 'prices':
                echo '<h1>Прайс-лист</h1><p>Наши цены...</p>';
                break;
            case 'contacts':
                echo '<h1>Контакты</h1><p>Как с нами связаться...</p>';
                break;
            default:
                echo '<h1>Главная</h1><p>Добро пожаловать...</p>';
        }
        ?>
    </div>
</body>
</html>