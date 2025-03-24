<?php
/*
 5.	Чтение из командной строки произвольного количества слов
    (каждое слово – отдельный параметр командной строки) и определение самого длинного слова
    (или самых длинных слов, если таких окажется больше одного).
 */

// Проверяем, что переданы аргументы
if ($argc < 2){
    exit(1);
}

$words = array_slice($argv, 1);

$maxLength = 0;
foreach ($words as $word){
    $length = mb_strlen($word, 'UTF-8');
    if ($length > $maxLength){
        $maxLength = $length;
    }
}

$longestWords = [];
foreach ($words as $word){
    if(mb_strlen($word, 'UTF-8') == $maxLength){
        $longestWords[] = $word;
    }
}

echo "Самые длинные слова (" . $maxLength . " символов):\n";
foreach ($longestWords as $word){
    echo $word . "\n";
}