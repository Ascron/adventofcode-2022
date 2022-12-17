<?php
$file = file('input.txt', FILE_IGNORE_NEW_LINES);

$points = [];

// -d memory_limit=2G

foreach ($file as $line) {
    $line = str_replace(['Sensor at x=', ' y=', ': closest beacon is at x='], ['', '', ','], $line);
    $points[] = explode(',', $line);
}

$q = 2000000;

$pointsResult = [];
$exclude = [];

$count = count($points);

foreach ($points as $point) {
    echo $count-- . PHP_EOL;
    [$sensorX, $sensorY, $beaconX, $beaconY] = $point;
    $d = abs($sensorX - $beaconX) + abs($sensorY - $beaconY);
    $abs = abs($sensorY - $q);
    if ($abs > $d) {
        continue;
    }

    $pointsResult = array_merge($pointsResult, range($sensorX - $d + $abs, $sensorX + $d - $abs));
    $pointsResult = array_unique($pointsResult);

    if ($sensorY == $q) {
        $exclude[] = $sensorX;
    }

    if ($beaconY == $q) {
        $exclude[] = $beaconX;
    }
}

$exclude = array_unique($exclude);
$result = count($pointsResult);
foreach ($exclude as $point) {
    if (in_array($point, $pointsResult)) {
        $result--;
    }
}

echo $result;

echo PHP_EOL;