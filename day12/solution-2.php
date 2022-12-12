<?php
$file = file('input.txt', FILE_IGNORE_NEW_LINES);

$heightMap = [];

$coordX = 0;
$coordY = 0;

$targetX = 0;
$targetY = 0;

$zeroPoints = [];

foreach ($file as $y => $line) {
    for ($x = 0, $c = strlen($line); $x < $c; $x++) {
        $char = $line[$x];
        if ($char === 'S') {
            $char = 'a';

        } elseif ($char === 'E') {
            $char = 'z';

            $coordY = $y;
            $coordX = $x;
        }

        $height = ord($char) - ord('a');
        $heightMap[$y][$x] = $height;
        if ($height === 0) {
            $zeroPoints[] = [$x, $y];
        }
    }
}

$path = [
    $coordY => [$coordX => 0]
];

// requires xdebug.max_nesting_level ~ 3000 if xdebug is enabled
findPath($path, $heightMap, $coordY, $coordX, $targetY, $targetX);

$result = PHP_INT_MAX;
foreach ($zeroPoints as $point) {
    [$x, $y] = $point;
    if (isset($path[$y][$x])) {
        $result = min($result, $path[$y][$x]);
    }
}

echo $result;


echo PHP_EOL;

function findPath(&$path, $heightMap, $coordX, $coordY, $targetX, $targetY) {
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
            && $heightMap[$coordX + $x][$coordY + $y] + 1 >= $heightMap[$coordX][$coordY]
            && (!isset($path[$coordX + $x][$coordY + $y]) || $path[$coordX + $x][$coordY + $y] > $path[$coordX][$coordY] + 1)
        ) {
            $path[$coordX + $x][$coordY + $y] = $path[$coordX][$coordY] + 1;
            findPath($path, $heightMap, $coordX + $x, $coordY + $y, $targetX, $targetY);
        }
    }
}