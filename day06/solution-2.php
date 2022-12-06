<?php
$line = file_get_contents('input.txt');
$buffer = [];

for ($i = 0, $c = strlen($line); $i < $c; $i++) {
    $buffer[] = $line[$i];
    if (count($buffer) > 14) {
        $buffer = array_splice($buffer, 1);
    } elseif (count($buffer) < 14) {
        continue;
    }

    if (count(array_unique($buffer)) === count($buffer)) {
        echo $i + 1;
        break;
    }
}

echo PHP_EOL;