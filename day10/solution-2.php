<?php
$file = file('input.txt', FILE_IGNORE_NEW_LINES);

$cycle = 0;
$X = 1;

$crt = '';

foreach ($file as $line) {
    $line = explode(' ', $line);
    if (count($line) === 1) {
        $cycle++;
        if ($X - 1 <= strlen($crt) % 40 && $X + 1 >= strlen($crt) % 40) {
            $crt .= '#';
        } else {
            $crt .= '.';
        }
    } else {
        $cycle++;
        if ($X - 1 <= strlen($crt) % 40 && $X + 1 >= strlen($crt) % 40) {
            $crt .= '#';
        } else {
            $crt .= '.';
        }

        $cycle++;
        if ($X - 1 <= strlen($crt) % 40 && $X + 1 >= strlen($crt) % 40) {
            $crt .= '#';
        } else {
            $crt .= '.';
        }

        $X += $line[1];


    }
}
foreach (str_split($crt, 40) as $string) {
    echo $string . PHP_EOL;
}

echo PHP_EOL;