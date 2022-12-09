<?php
$file = file('input.txt', FILE_IGNORE_NEW_LINES);

$headX = 0;
$headY = 0;
$tailX = 0;
$tailY = 0;

$track = [];
$track[$tailY][$tailX] = 1;

foreach ($file as $line) {
    [$command, $length] = explode(' ', $line);

    for ($i = 0; $i < $length; $i++) {
        switch ($command) {
            case 'U':
                $headY++;
                break;
            case 'D':
                $headY--;
                break;
            case 'R':
                $headX++;
                break;
            case 'L':
                $headX--;
                break;
        }
        if (abs($headX - $tailX) >= 2) {
            $tailY = $headY;
            $tailX = ($headX - $tailX) / 2 + $tailX;
        }

        if (abs($headY - $tailY) >= 2) {
            $tailX = $headX;
            $tailY = ($headY - $tailY) / 2 + $tailY;
        }

        $track[$tailY][$tailX] = 1;
    }
}

echo array_sum(array_map('array_sum', $track));

echo PHP_EOL;