<?php

require_once 'Logger.php';

// 1. Логирование на экран
$screenLogger = new Logger(Logger::OUTPUT_SCREEN);
$screenLogger->log("Это сообщение появится на экране");

// Альтернативный вариант создания
$screenLogger = Logger::toScreen();
$screenLogger->log("Еще одно сообщение на экране");

// 2. Логирование в файл
$fileLogger = new Logger(Logger::OUTPUT_FILE, 'application.log');
$fileLogger->log("Это сообщение будет записано в файл");

// Альтернативный вариант создания
$fileLogger = Logger::toFile('application.log');
$fileLogger->log("Еще одно сообщение в файл");