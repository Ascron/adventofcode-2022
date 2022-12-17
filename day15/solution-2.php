<?php
$file = file('input.txt', FILE_IGNORE_NEW_LINES);

$points = [];

// -d memory_limit=2G
$beacons = [];
foreach ($file as $line) {
    $line = str_replace(['Sensor at x=', ' y=', ': closest beacon is at x='], ['', '', ','], $line);
    [$sensorX, $sensorY, $beaconX, $beaconY] = explode(',', $line);
    $d = distance($sensorX, $sensorY, $beaconX, $beaconY);
    $points[] = [$sensorX, $sensorY, $beaconX, $beaconY, $d];
    $beacons[] = [$beaconX, $beaconY];
}

$q = 2000000;

$pointsResult = [];
$exclude = [];

$count = count($points);

//foreach ($points as $index => $point) {
//    [$sensorX, $sensorY, $beaconX, $beaconY, $d] = $point;
//    for ($i = $index; $i < $count; $i ++) {
//        [$sensorX2, $sensorY2, $beaconX2, $beaconY2, $d2] = $points[$i];
//
//
//    }
//}

//foreach ($points as $index => $point) {
//    [$sensorX, $sensorY, $beaconX, $beaconY, $d] = $point;
//    foreach (findVertices($sensorX, $sensorY, $d) as $vertice) {
//        $result = true;
//        for ($i = 0, $c = count($points); $i < $c; $i++) {
//            if (findIsInside($vertice[0], $vertice[1], $points[$i][0], $points[$i][1], $points[$i][4])) {
//                $result = false;
//                break;
//            }
//        }
//        if ($result === true) {
//            echo $vertice[0] . ' ' . $vertice[1] . ' ' . ($vertice[0] * 4000000 + $vertice[1]) . PHP_EOL;
//        }
//    }
//}

//foreach ($beacons as $beacon) {
//    [$beaconX, $beaconY] = $beacon;
//    foreach (findAround($beaconX, $beaconY) as $point) {
//        $result = true;
//        for ($i = 0, $c = count($points); $i < $c; $i++) {
//            if (findIsInside($point[0], $point[1], $points[$i][0], $points[$i][1], $points[$i][4])) {
//                $result = false;
//                break;
//            }
//        }
//
//        if ($result) {
//            echo $point[0] . ' ' . $point[1] . ' ' . ($point[0] * 4000000 + $point[1]) . PHP_EOL;
//        }
//    }
//}
$counter = 0;
foreach ($points as $index => $point) {
    [$sensorX, $sensorY, $beaconX, $beaconY, $d] = $point;
    for ($i = 0, $c = count($points); $i < $c; $i++) {
        foreach (findFieldCross($sensorX, $sensorY, $d, $points[$i][0], $points[$i][1], $points[$i][4]) as $cross) {
            $result = true;
            for ($j = 0; $j < $c; $j++) {
                if (findIsInside($cross[0], $cross[1], $points[$j][0], $points[$j][1], $points[$j][4])) {
                    $result = false;
                    break;
                }
            }

            if ($result && $cross[0] >= 0 && $cross[1] >= 0 && $cross[0] <= 4000000 && $cross[1] <= 4000000) {
                echo $cross[0] . ' ' . $cross[1] . ' ' . ($cross[0] * 4000000 + $cross[1]) . PHP_EOL;
            }
        }
    }
}


echo PHP_EOL;

function findIsInside($x, $y, $sensorX, $sensorY, $d) {
    return distance($x, $y, $sensorX, $sensorY) <= $d;
}

function findVertices($sensorX, $sensorY, $d) {
    return [
        [$sensorX, $sensorY + $d + 1],
        [$sensorX + $d + 1, $sensorY],
        [$sensorX, $sensorY - $d - 1],
        [$sensorX - $d - 1, $sensorY],
    ];
}

function findAround($x, $y) {
    return [
        [$x + 1, $y],
        [$x - 1, $y],
        [$x, $y + 1],
        [$x, $y - 1],
        [$x + 1, $y + 1],
        [$x - 1, $y - 1],
        [$x + 1, $y - 1],
        [$x - 1, $y + 1],
    ];
}

function distance($x1, $y1, $x2, $y2) {
    return abs($x1 - $x2) + abs($y1 - $y2);
}

function findFieldCross($beaconX1, $beaconY1, $d1, $beaconX2, $beaconY2, $d2) {
    $distance = distance($beaconX1, $beaconY1, $beaconX2, $beaconY2);
    $result = [];
    if ($distance > $d1 + $d2 || $distance + $d1 <= $d2 || $distance + $d2 <= $d1) {
        return $result;
    }

    if (($beaconX1 + $beaconY1) % 2 !== ($beaconX2 + $beaconY2) % 2) {
        return $result;
    }

    $vertices1 = findVertices($beaconX1, $beaconY1, $d1);
    $vertices2 = findVertices($beaconX2, $beaconY2, $d2);

    $aArr = [
        $vertices1[0][0] + $vertices1[0][1],
        $vertices1[3][0] + $vertices1[3][1]
    ];
    $bArr = [
        $vertices2[2][1] - $vertices2[2][0],
        $vertices2[3][1] - $vertices2[3][0]
    ];
    foreach ($aArr as $a) {
        foreach ($bArr as $b) {
            $x = ($a - $b) / 2;
            if (!is_int($x)) {
                continue;
            }
            $y = $a - $x;
            if ($x == 14 && $y == 11) {
                $sdasd = 1;
            }
            if (findIsInside($x, $y, $beaconX1, $beaconY1, $d1 + 1) && findIsInside($x, $y, $beaconX2, $beaconY2, $d2 + 1)) {
                $result[] = [$x, $y];
            }
        }
    }

    $aArr = [
        $vertices2[0][0] + $vertices2[0][1],
        $vertices2[3][0] + $vertices2[3][1]
    ];
    $bArr = [
        $vertices1[2][1] - $vertices1[2][0],
        $vertices1[3][1] - $vertices1[3][0]
    ];

    foreach ($aArr as $a) {
        foreach ($bArr as $b) {
            $x = ($a - $b) / 2;
            if (!is_int($x)) {
                continue;
            }
            $y = $a - $x;
            if ($x == 14 && $y == 11) {
                $sdasd = 1;
            }
            if (findIsInside($x, $y, $beaconX1, $beaconY1, $d1 + 1) && findIsInside($x, $y, $beaconX2, $beaconY2, $d2 + 1)) {
                $result[] = [$x, $y];
            }
        }
    }

    return $result;
}

