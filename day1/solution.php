<?php
$file = file('input.txt');
$sum = [];
$index = 0;
foreach ($file as $key => $row) {
    $row = trim($row);
    if ($row === '') {
        $index++;
    } else {
        $sum[$index] = ($sum[$index] ?? 0) + $row;
    }
}

echo max($sum);
echo PHP_EOL;