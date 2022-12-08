<?php
$file = file('input.txt', FILE_IGNORE_NEW_LINES);

$map = [];
foreach ($file as $index => $line) {
    $map[$index] = array_map('intval', str_split($line));
}

$visibleMap = [];

for ($y = 0, $maxY = count($map); $y < $maxY; $y++) {
    $maxH = 0;
    for ($x = 0, $maxX = count($map[$y]); $x < $maxX; $x++) {
        if ($map[$y][$x] > $maxH) {
            $visibleMap[$y][$x] = 1;
        } elseif ($map[$y][$x] === 0 && $maxH === 0 && $x === 0) {
            $visibleMap[$y][$x] = 1;
        }
        $maxH = max($maxH, $map[$y][$x]);
    }

    $maxH = 0;
    for ($x = count($map[$y]) - 1; $x >= 0; $x--) {
        if ($map[$y][$x] > $maxH) {
            $visibleMap[$y][$x] = 1;
        } elseif ($map[$y][$x] === 0 && $maxH === 0 && $x === count($map[$y]) - 1) {
            $visibleMap[$y][$x] = 1;
        }
        $maxH = max($maxH, $map[$y][$x]);
    }
}

for ($x = 0, $maxX = count($map[0]); $x < $maxX; $x++) {
    $maxH = 0;
    for ($y = 0, $maxY = count($map); $y < $maxY; $y++) {
        if ($map[$y][$x] > $maxH) {
            $visibleMap[$y][$x] = 1;
        } elseif ($map[$y][$x] === 0 && $maxH === 0 && $y === 0) {
            $visibleMap[$y][$x] = 1;
        }
        $maxH = max($maxH, $map[$y][$x]);
    }

    $maxH = 0;
    for ($y = count($map) - 1; $y >= 0; $y--) {
        if ($map[$y][$x] > $maxH) {
            $visibleMap[$y][$x] = 1;
        } elseif ($map[$y][$x] === 0 && $maxH === 0 && $y === count($map) - 1) {
            $visibleMap[$y][$x] = 1;
        }
        $maxH = max($maxH, $map[$y][$x]);
    }
}
$visible = 0;
foreach ($visibleMap as $row) {
    $visible += count($row);
}
echo $visible;

echo PHP_EOL;