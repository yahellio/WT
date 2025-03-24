<?php

/*
    Создайте 2 массива с целыми числами через 2 поля формы, объедините два массива
    в один (не используя специальные функции PHP типа array_merge(arr1,arr2)!),
    Выведите все чётные числа из получившегося массива
*/

$numbers1 = [];
$numbers2 = [];
$merged = [];
$evens = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Преобразуем введённые строки в массивы чисел
    $numbers1 = array_map('intval', explode(',', $_POST['array1']));
    $numbers2 = array_map('intval', explode(',', $_POST['array2']));

    // Объединяем массивы
    $merged = merge_arr($numbers1, $numbers2);

    // Фильтруем чётные числа
    $evens = array_filter($merged, function($n) {
        return $n % 2 === 0;
    });
}

function merge_arr(array $arr1, array $arr2){
    foreach ($arr2 as $value) {
        $arr1[] = $value;
    }
    return $arr1;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Объединение массивов</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .result { margin-top: 20px; padding: 10px; background: #f0f0f0; }
        input[type="text"] { width: 300px; padding: 5px; }
    </style>
</head>
<body>
<h2>Введите два массива чисел через запятую:</h2>
<form method="post">
    <p>
        Массив 1: <input type="text" name="array1" value="<?= isset($_POST['array1']) ? htmlspecialchars($_POST['array1']) : '' ?>">
    </p>
    <p>
        Массив 2: <input type="text" name="array2" value="<?= isset($_POST['array2']) ? htmlspecialchars($_POST['array2']) : '' ?>">
    </p>
    <input type="submit" value="Объединить и найти чётные">
</form>

<?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <div class="result">
        <h3>Результаты:</h3>
        <p><strong>Объединённый массив:</strong> [<?= implode(', ', $merged) ?>]</p>
        <p><strong>Чётные числа:</strong> [<?= implode(', ', $evens) ?>]</p>
    </div>
<?php endif; ?>
</body>
</html>