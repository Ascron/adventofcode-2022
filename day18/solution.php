<?php
$file = file('input.txt', FILE_IGNORE_NEW_LINES);

$map = [];
$points = [];

foreach ($file as $line) {
    [$x, $y, $z] = explode(',', $line);
    $map[$z][$y][$x] = 1;
    $points[] = [$x, $y, $z];
}

$result = 0;

foreach ($points as $point) {
    [$x, $y, $z] = $point;
    $result += 6;
    foreach (getNeighbours($x, $y, $z) as $neighbour) {
        if (isset($map[$neighbour[2]][$neighbour[1]][$neighbour[0]])) {
            $result--;
        }
    }
}

// test 64
echo $result;

echo PHP_EOL;

function getNeighbours($x, $y, $z) {
    foreach ([-1, 1] as $dx) {
        yield [$x + $dx, $y, $z];
    }
    foreach ([-1, 1] as $dy) {
        yield [$x, $y + $dy, $z];
    }
    foreach ([-1, 1] as $dz) {
        yield [$x, $y, $z + $dz];
    }
}