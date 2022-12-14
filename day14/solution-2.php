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

$floorY = max(array_keys($map)) + 2;

$flowX = 500;
$flowY = 0;

$sand = 0;
while (spawnSand($flowX, $flowY, $floorY, $map)) {
    $sand++;
}

ksort($map);

echo $sand + 1;

echo PHP_EOL;

function spawnSand($x, $y, $floorY, &$map) {
    return dropSand($x, $y, $floorY, $map);
}

function dropSand($x, $y, $floorY, &$map) {
    $newPoint = null;
    if (!isset($map[$y + 1][$x]) && $y + 1 < $floorY) {
        $newPoint = [$x, $y + 1];
    } elseif (!isset($map[$y + 1][$x - 1]) && $y + 1 < $floorY) {
        $newPoint = [$x - 1, $y + 1];
    } elseif (!isset($map[$y + 1][$x + 1]) && $y + 1 < $floorY) {
        $newPoint = [$x + 1, $y + 1];
    }

    if ($newPoint) {
        [$x, $y] = $newPoint;
        return dropSand($x, $y, $floorY, $map);
    }

    $map[$y][$x] = 2;
    if ($x == 500 && $y == 0) {
        return false;
    }
    return true;
}