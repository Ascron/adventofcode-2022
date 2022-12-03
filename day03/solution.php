<?php
$file = file('input.txt', FILE_IGNORE_NEW_LINES);
$result = 0;
foreach ($file as $line) {
    [$comp1, $comp2] = str_split($line, strlen($line) / 2);
    $comp1 = [];
    $comp2 = [];
    for ($i = 0, $c = strlen($line); $i < $c; $i++) {
        $priority = ord($line[$i]);
        if ($priority > 96) {
            $priority -= 96;
        } else {
            $priority -= 38;
        }
        if ($i < $c / 2) {
            $comp1[] = $priority;
        } else {
            $comp2[] = $priority;
        }
    }

    $intersect = array_intersect($comp1, $comp2);
    $result += reset($intersect);
}

echo $result;

echo PHP_EOL;