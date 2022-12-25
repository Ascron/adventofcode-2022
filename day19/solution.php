<?php
$file = file('input.txt', FILE_IGNORE_NEW_LINES);

$schemes = [];

// Blueprint 1: Each ore robot costs 4 ore. Each clay robot costs 2 ore. Each obsidian robot costs 3 ore and 14 clay. Each geode robot costs 2 ore and 7 obsidian.

foreach ($file as $line) {
    $line = str_replace(
        [
            'Blueprint ',
            ': Each ore robot costs ',
            ' ore. Each clay robot costs ',
            ' ore. Each obsidian robot costs ',
            ' ore and ',
            ' clay. Each geode robot costs ',
            ' ore and ',
            ' obsidian.'
        ],
        ['', ',', ',', ',', ',', ',', ',', ''],
        $line
    );

    $scheme = explode(',', $line);
    $schemes[$scheme[0]] = [
        [(int)$scheme[1], 0, 0],
        [(int)$scheme[2], 0, 0],
        [(int)$scheme[3], (int)$scheme[4], 0],
        [(int)$scheme[5], 0, (int)$scheme[6]],
    ];
}

$result = 0;

foreach ($schemes as $id => $scheme) {
    $counter = 0;
    [$x, $path] = getSchemeQuality($scheme);
    $result += $x * $id;
    print_r($path[0]);
    echo PHP_EOL . $path[1] . PHP_EOL;
}

// test 33
echo $result;

echo PHP_EOL;

function getSchemeQuality(&$scheme) {
    $minerals = [0, 0, 0, 0];
    $robots = [1, 0, 0, 0];

    $priorities = [0, 0, 0, 20];
    foreach ($scheme as $price) {
        $priorities[0] += $price[0];
        $priorities[1] += $price[1];
        $priorities[2] += $price[2];
    }

    arsort($priorities);
//    $priorities = array_keys($priorities);


    $time = 24;
    return makeTurn($scheme, $priorities, $minerals, $robots, $time, []);
}

function makeTurn(&$scheme, $priorities, $minerals, $robots, $time, $path) {
    global $counter;
    $counter++;
    if ($counter % 100000 == 0) {
        echo $counter . PHP_EOL;
    }
//    if ($counter > 100000) {
//        return 0;
//    }
    $result = []; // 1425488

    if ($time > 0) {
        $limit = 4;
        foreach ($priorities as $i => $value) {
//            if ($i == 0 && $robots[$i] > 2) {
//                continue;
//            }
            $wait = enoughMinerals($scheme[$i], $minerals, $robots);
            if ($wait !== false && $wait <= 24 && $time > $wait) {
                $newPath = $path;
                $newPath[$time - $wait] = [$i, $minerals[3] + $robots[3] * $wait, $robots[2], $counter];
                [$max, $value] = makeTurn($scheme, $priorities, addMinerals($robots, removeMinerals($scheme[$i], $minerals), $wait), addRobot($i, $robots), $time - $wait, $newPath);
                $result[$max] = $value;
                $limit--;
            }

            if ($limit == 0) {
                break;
            }
        }
    }

    if ($time > 0) {
        [$max, $value] = makeTurn($scheme, $priorities, addMinerals($robots, $minerals, $time), $robots, 0, $path);
        $result[$max] = $value;
    } else {
        $result[$minerals[3]] = [$path, $minerals[3]];
    }

    $max = max(array_keys($result));
    return [$max, $result[$max]];
}

function enoughMinerals($price, $minerals, $robots) {
    $turns = [];
    foreach ($price as $index => $value) {
        if ($price[$index] === 0) {
            continue;
        }
        if ($robots[$index] > 0) {
            $turns[$index] = max((int)ceil(($price[$index] - $minerals[$index]) / $robots[$index]), 0);
        } else {
            return false;
        }
    }

    return max($turns) + 1;
}

function addMinerals($robots, $minerals, $turns = 1) {
    $minerals[0] += $robots[0] * $turns;
    $minerals[1] += $robots[1] * $turns;
    $minerals[2] += $robots[2] * $turns;
    $minerals[3] += $robots[3] * $turns;
    return $minerals;
}

function removeMinerals($robotScheme, $minerals) {
    $minerals[0] -= $robotScheme[0];
    $minerals[1] -= $robotScheme[1];
    $minerals[2] -= $robotScheme[2];
    return $minerals;
}

function addRobot($key, $robots) {
    $robots[$key]++;
    return $robots;
}
