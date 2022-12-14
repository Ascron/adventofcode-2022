<?php
$file = file('input.txt', FILE_IGNORE_NEW_LINES);

$map = [];
// 503,4 -> 502,4 -> 502,9 -> 494,9
foreach ($file as $line) {
    $coords = explode(' -> ', $line);
    $previousX = null;
    $previousY = null;
    foreach ($coords as $coord) {
        $coord = explode(',', $coord);
        if ($previousX === null) {
            [$previousX, $previousY] = $coord;
            $map[$previousY][$previousX] = 1;
        } else {
            [$x, $y] = $coord;

            if ($x != $previousX) {
                while ($x != $previousX) {
                    $map[$y][$x] = 1;
                    $x = $x > $previousX ? $x - 1 : $x + 1;
                }
            } elseif ($y != $previousY) {
                while ($y != $previousY) {
                    $map[$y][$x] = 1;
                    $y = $y > $previousY ? $y - 1 : $y + 1;
                }
            }

            [$previousX, $previousY] = $coord;
        }
    }
}

$abyssY = max(array_keys($map));

$flowX = 500;
$flowY = 0;

$sand = 0;
while (spawnSand($flowX, $flowY, $abyssY, $map)) {
    $sand++;
}

ksort($map);

echo $sand;

echo PHP_EOL;

function spawnSand($x, $y, $abyssY, &$map) {
    return dropSand($x, $y, $abyssY, $map);
}

function dropSand($x, $y, $abyssY, &$map) {
    $newPoint = null;
    if (!isset($map[$y + 1][$x])) {
        $newPoint = [$x, $y + 1];
    } elseif (!isset($map[$y + 1][$x - 1])) {
        $newPoint = [$x - 1, $y + 1];
    } elseif (!isset($map[$y + 1][$x + 1])) {
        $newPoint = [$x + 1, $y + 1];
    }

    if ($newPoint) {
        [$x, $y] = $newPoint;
        if ($y >= $abyssY) {
            return false;
        }
        return dropSand($x, $y, $abyssY, $map);
    }

    $map[$y][$x] = 2;
    return true;
}