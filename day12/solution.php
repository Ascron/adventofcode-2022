<?php
$file = file('input.txt', FILE_IGNORE_NEW_LINES);

$heightMap = [];

$coordX = 0;
$coordY = 0;

$targetX = 0;
$targetY = 0;

foreach ($file as $y => $line) {
    for ($x = 0, $c = strlen($line); $x < $c; $x++) {
        $char = $line[$x];
        if ($char === 'S') {
            $char = 'a';

            $coordY = $y;
            $coordX = $x;
        } elseif ($char === 'E') {
            $char = 'z';

            $targetY = $y;
            $targetX = $x;
        }

        $heightMap[$y][$x] = ord($char) - ord('a');
    }
}

$path = [
    $coordY => [$coordX => 0]
];

// requires xdebug.max_nesting_level ~ 2000 if xdebug is enabled
findPath($path, $heightMap, $coordY, $coordX);

echo $path[$targetY][$targetX];

echo PHP_EOL;

function findPath(&$path, $heightMap, $coordX, $coordY) {
    $directions = [
        [0, 1],
        [0, -1],
        [1, 0],
        [-1, 0],
    ];

    foreach ($directions as $direction) {
        [$x, $y] = $direction;
        if (
            isset($heightMap[$coordX + $x][$coordY + $y])
            && $heightMap[$coordX + $x][$coordY + $y] <= $heightMap[$coordX][$coordY] + 1
            && (!isset($path[$coordX + $x][$coordY + $y]) || $path[$coordX + $x][$coordY + $y] > $path[$coordX][$coordY] + 1)
        ) {
            $path[$coordX + $x][$coordY + $y] = $path[$coordX][$coordY] + 1;
            findPath($path, $heightMap, $coordX + $x, $coordY + $y);
        }
    }
}