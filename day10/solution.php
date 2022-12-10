<?php
$file = file('input.txt', FILE_IGNORE_NEW_LINES);

$cycle = 0;
$X = 1;

$majorPoints = [20, 60, 100, 140, 180, 220];
$result = 0;

foreach ($file as $line) {
    $line = explode(' ', $line);
    if (count($line) === 1) {
        $cycle++;
        if (in_array($cycle, $majorPoints)) {
            $result += $X * $cycle;
        }
    } else {
        $cycle++;
        if (in_array($cycle, $majorPoints)) {
            $result += $X * $cycle;
        }

        $cycle++;
        if (in_array($cycle, $majorPoints)) {
            $result += $X * $cycle;
        }

        $X += $line[1];
    }
}

echo $result;

echo PHP_EOL;