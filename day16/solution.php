<?php
$file = file('input.txt', FILE_IGNORE_NEW_LINES);

/*
 * Valve AA has flow rate=0; tunnels lead to valves DD, II, BB
Valve BB has flow rate=13; tunnels lead to valves CC, AA
Valve CC has flow rate=2; tunnels lead to valves DD, BB
Valve DD has flow rate=20; tunnels lead to valves CC, AA, EE
Valve EE has flow rate=3; tunnels lead to valves FF, DD
Valve FF has flow rate=0; tunnels lead to valves EE, GG
Valve GG has flow rate=0; tunnels lead to valves FF, HH
Valve HH has flow rate=22; tunnel leads to valve GG
Valve II has flow rate=0; tunnels lead to valves AA, JJ
Valve JJ has flow rate=21; tunnel leads to valve II
 */

$valves = [];
$workingValves = [];

foreach ($file as $line) {
    $line = str_replace(
        ['Valve ', ' has flow rate=', '; tunnels lead', '; tunnel leads', ' to valves ', ' to valve ', ' '],
        ['', ',', ',', ',', '', '', ''],
        $line
    );
    $line = explode(',', $line);
    [$valve, $flowRate] = $line;
    $tunnels = array_slice($line, 2);
    $valves[$valve] = [
        'flow' => (int)$flowRate,
        'tunnels' => $tunnels,
        'speed' => [],
    ];

    if ($flowRate > 0) {
        $workingValves[] = $valve;
    }
}

while (true) {
    $reset = true;
    foreach ($valves as $valve => &$data) {

        foreach ($workingValves as $workingValve) {
            if (in_array($workingValve, $data['tunnels']) && !isset($data['speed'][$workingValve])) {
                $data['speed'][$workingValve] = 1;
            }
        }

        foreach ($data['tunnels'] as $tunnel) {
            foreach ($valves[$tunnel]['speed'] as $testValve => $speed) {
                if ($testValve === $valve) {
                    continue;
                }
                if (!isset($data['speed'][$testValve]) || $data['speed'][$testValve] > $speed + 1) {
                    $data['speed'][$testValve] = $speed + 1;
                }
            }
        }

        if ($data['flow'] > 0) {
            $full = count($data['speed']) === count($workingValves) - 1;
        } else {
            $full = count($data['speed']) === count($workingValves);
        }
        $reset = $reset && $full;
    }

    if ($reset) {
        break;
    }
}
unset($data);

$time = 30;
$path = ['AA'];
$map = [];
$open = [];

echo preventEruption(0, 'AA', $time, $valves, $open, 'AA', $workingValves);

echo PHP_EOL;
$counter = 0;


function preventEruption($power, $place, $time, &$valves, $open, $path, &$workingValves): int {
    global $counter;
    $counter++;
    if ($time === 0) {
        return $power;
    }

    if ($valves[$place]['flow'] > 0 && !in_array($place, $open)) {
        $power += calcPower($open, $valves);
        $time--;
        $open[] = $place;
        $path .= '.open';
    }

    if (count($open) === count($workingValves)) {
        return $power + $time * calcPower($open, $valves);
    }


    $maxDistance = max($valves[$place]['speed']);
    $paths = [];
    foreach ($valves[$place]['speed'] as $valve => $speed) {
        if (in_array($valve, $open)) {
            continue;
        }
        if ($time <= $speed) {
            continue;
        }
        $paths[$valve] = ($maxDistance - $speed) * $valves[$valve]['flow'];
    }

    arsort($paths);

    $result = [];
    foreach ($paths as $valve => $value) {
        $result[] = preventEruption($power + $valves[$place]['speed'][$valve] * calcPower($open, $valves), $valve, $time - $valves[$place]['speed'][$valve], $valves, $open, $path . '.' . $valve, $workingValves);
    }
    $result[] = $power + $time * calcPower($open, $valves);
    return max($result);
}

function calcPower($open, &$valves) {
    $power = 0;
    foreach ($open as $valve) {
        $power += $valves[$valve]['flow'];
    }
    return $power;
}

function findClosedValves($open, $workingValves) {
    $closed = [];
    foreach ($workingValves as $valve) {
        if (!in_array($valve, $open)) {
            $closed[] = $valve;
        }
    }
    return $closed;
}

function findPath($from, $to) {

}