<?php
$file = file('input.txt', FILE_IGNORE_NEW_LINES);

$map = [];
foreach ($file as $index => $line) {
    $map[$index] = array_map('intval', str_split($line));
}

$visibleMap = [];

$score = 0;
for ($y = 0, $maxY = count($map); $y < $maxY; $y++) {
    for ($x = 0, $maxX = count($map[$y]); $x < $maxX; $x++) {
        $score = max($score, getScore($map, $x, $y));
    }
}
echo $score;

echo PHP_EOL;

function getScore($map, $x, $y) {
    $h = $map[$y][$x];
    $score1 = 0;
    for ($viewX = $x; $viewX >= 0; $viewX--) {
        if ($viewX === $x) {
            continue;
        }

        if ($map[$y][$viewX] <= $h) {
            $score1 += 1;
        }

        if ($map[$y][$viewX] >= $h) {
            break;
        }
    }
    $score2 = 0;
    for ($viewX = $x, $c = count($map[$y]); $viewX < $c; $viewX++) {
        if ($viewX === $x) {
            continue;
        }

        $score2 += 1;

        if ($map[$y][$viewX] >= $h) {
            break;
        }
    }
    $score3 = 0;
    for ($viewY = $y, $c = count($map); $viewY < $c; $viewY++) {
        if ($viewY === $y) {
            continue;
        }

        if ($map[$viewY][$x] <= $h) {
            $score3 += 1;
        }

        if ($map[$viewY][$x] >= $h) {
            break;
        }
    }
    $score4 = 0;
    for ($viewY = $y; $viewY >= 0; $viewY--) {
        if ($viewY === $y) {
            continue;
        }

        if ($map[$viewY][$x] <= $h) {
            $score4 += 1;
        }

        if ($map[$viewY][$x] >= $h) {
            break;
        }
    }

    return $score1 * $score2 * $score3 * $score4;
}