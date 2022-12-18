<?php
$file = file('input.txt', FILE_IGNORE_NEW_LINES);

$map = [];
$points = [];
$surfaces = [];
$minX = $minY = $minZ = INF;
$maxX = $maxY = $maxZ = -INF;

foreach ($file as $line) {
    [$x, $y, $z] = explode(',', $line);
    $map[$z][$y][$x] = 1;
    $points[] = [$x, $y, $z];
    $minX = min($minX, $x);
    $minY = min($minY, $y);
    $minZ = min($minZ, $z);
    $maxX = max($maxX, $x);
    $maxY = max($maxY, $y);
    $maxZ = max($maxZ, $z);
}

$minX--;
$minY--;
$minZ--;
$maxX++;
$maxY++;
$maxZ++;

$exteriorAir = [];

spawnExteriorAir([0, 0, 0], $minX, $minY, $minZ, $maxX, $maxY, $maxZ, $map, $exteriorAir);
$result = 0;
foreach ($points as $point) {
    [$x, $y, $z] = $point;
    foreach (getNeighbours($x, $y, $z) as $neighbour) {
        $key = getKey($neighbour[0], $neighbour[1], $neighbour[2]);
        if (isset($exteriorAir[$key])) {
            $result++;
        }
    }
}

// test 58
// near 1700
echo $result;

echo PHP_EOL;

function spawnExteriorAir($point, $minX, $minY, $minZ, $maxX, $maxY, $maxZ, $map, &$exteriorAir) {
    [$x, $y, $z] = $point;
    foreach (getNeighbours($x, $y, $z) as $neighbour) {
        [$nx, $ny, $nz] = $neighbour;
        $key = getKey($nx, $ny, $nz);
        if (isset($map[$nz][$ny][$nx])) {
            continue;
        }
        if (isset($exteriorAir[$key])) {
            continue;
        }
        if ($nz < $minX || $ny < $minY || $nx < $minZ || $nz > $maxZ || $ny > $maxY || $nx > $maxX) {
            continue;
        }
        $exteriorAir[$key] = 1;
        spawnExteriorAir($neighbour, $minX, $minY, $minZ, $maxX, $maxY, $maxZ, $map, $exteriorAir);
    }
};

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

function getKey($x, $y, $z) {
    return $x . '_' . $y . '_' . $z;
}