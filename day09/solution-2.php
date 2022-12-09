<?php
$file = file('input.txt', FILE_IGNORE_NEW_LINES);

/**
 *              ____
 *             / . .\
 *             \  ---<
 *              \  /
 *    __________/ /
 * -=:___________/
 */
$snek = [ // not a mistake https://knowyourmeme.com/photos/1200748-snek
    0 => [0, 0],
    1 => [0, 0],
    2 => [0, 0],
    3 => [0, 0],
    4 => [0, 0],
    5 => [0, 0],
    6 => [0, 0],
    7 => [0, 0],
    8 => [0, 0],
    9 => [0, 0],
];

$track = [];
$track[$snek[9][0]][$snek[9][1]] = 1;
$lineIndex = 0;
foreach ($file as $line) {
    [$command, $length] = explode(' ', $line);

    for ($i = 0; $i < $length; $i++) {
        switch ($command) {
            case 'U':
                $snek[0][0]++;
                break;
            case 'D':
                $snek[0][0]--;
                break;
            case 'R':
                $snek[0][1]++;
                break;
            case 'L':
                $snek[0][1]--;
                break;
        }

        for ($part = 0; $part <= 8; $part++) {
            if (
                abs($snek[$part][1] - $snek[$part + 1][1]) >= 2
                && abs($snek[$part][0] - $snek[$part + 1][0]) >= 2
            ) {
                $snek[$part + 1][0] = ($snek[$part][0] - $snek[$part + 1][0]) / 2 + $snek[$part + 1][0];
                $snek[$part + 1][1] = ($snek[$part][1] - $snek[$part + 1][1]) / 2 + $snek[$part + 1][1];
            }

            if (abs($snek[$part][1] - $snek[$part + 1][1]) >= 2) {
                $snek[$part + 1][0] = $snek[$part][0];
                $snek[$part + 1][1] = ($snek[$part][1] - $snek[$part + 1][1]) / 2 + $snek[$part + 1][1];
            }

            if (abs($snek[$part][0] - $snek[$part + 1][0]) >= 2) {
                $snek[$part + 1][1] = $snek[$part][1];
                $snek[$part + 1][0] = ($snek[$part][0] - $snek[$part + 1][0]) / 2 + $snek[$part + 1][0];
            }
        }


        $track[$snek[9][0]][$snek[9][1]] = 1;
    }

    $lineIndex++;
}

echo array_sum(array_map('count', $track));



echo PHP_EOL;