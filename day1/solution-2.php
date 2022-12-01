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

rsort($sum);
echo $sum[0] + $sum[1] + $sum[2];
echo PHP_EOL;