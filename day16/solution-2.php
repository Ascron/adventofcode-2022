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

$time = 26;
$path = ['AA'];
$map = [];
$open = [];
$result = [];

preventEruption(
    0,
    [
        ['place' => 'AA', 'moving' => false, 'opening' => false],
        ['place' => 'AA', 'moving' => false, 'opening' => false]
    ],
    $time,
    $valves,
    $open,
    [],
    [],
    'AA.',
    $workingValves,
    $result
);

echo max($result);

echo PHP_EOL;
$counter = 0;

function preventEruption($power, $characters, $time, &$valves, $open, $opening, $moving, $path, &$workingValves, &$result) {
    global $counter;
    $counter++;
    if ($counter > 100000) {
        return; // magic
    }
//    echo $counter . PHP_EOL;
    if ($time === 0) {
        $result[] = $power;
        return;
    }

    if (
        ($characters[0]['opening'] !== false || $characters[0]['moving'] !== false)
        && ($characters[1]['opening'] !== false || $characters[1]['moving'] !== false)
    ) {
        if ($characters[0]['opening'] !== false || $characters[1]['opening'] !== false) {
            $passTime = 1;
        } else {
            $passTime = min($characters[0]['moving']['time'], $characters[1]['moving']['time']);
        }

        $time -= $passTime;
        $power += $passTime * calcPower($open, $valves);
        foreach ($characters as &$character) {
            if ($character['opening']) {
                $open[] = $character['opening'];
                unset($opening[array_search($character['opening'], $opening)]);
                $character['opening'] = false;
            } else {
                if ($character['moving']['time'] == $passTime) {
                    $character['place'] = $character['moving']['target'];
                    $character['moving'] = false;
                    unset($moving[array_search($character['place'], $moving)]);
                } else {
                    $character['moving']['time'] -= $passTime;
                }
            }
        }
        unset($character);
    }

    if (count($open) == count($workingValves)) {
        $result[] = $power + $time * calcPower($open, $valves);
        return;
    }

    $paths = [];
    foreach ($characters as $index => &$character) {
        if ($character['opening'] !== false || $character['moving'] !== false) {
            continue;
        }
        $place = $character['place'];
        if ($valves[$place]['flow'] > 0 && !in_array($place, $open)) {
            $opening[] = $place;
            $character['opening'] = $place;
            $path .= ".open({$index})";
        } else {
            $maxDistance = max($valves[$place]['speed']);
            $paths[$index] = [];
            foreach ($valves[$place]['speed'] as $valve => $speed) {
                if (in_array($valve, $open) || in_array($valve, $opening) || in_array($valve, $moving)) {
                    continue;
                }
                if ($time <= ($speed + 1)) {
                    continue;
                }
                $paths[$index][$valve] = ($maxDistance - $speed) * $valves[$valve]['flow'];
            }
            arsort($paths[$index]);
        }
    }
    unset($character);

    if (count($paths) === 2) {
        $paths1 = array_keys($paths[0]);
        $paths2 = array_keys($paths[1]);
        $chars = $characters;
        foreach ($paths1 as $index1 => $valve1) {
            foreach ($paths2 as $index2 => $valve2) {
                if ($chars[0]['place'] == $chars[1]['place'] && $index2 <= $index1) {
                    continue;
                }
                if ($valve2 == $valve1) {
                    continue;
                }

                $chars[0]['moving'] = ['target' => $valve1, 'time' => $valves[$chars[0]['place']]['speed'][$valve1]];
                $chars[1]['moving'] = ['target' => $valve2, 'time' => $valves[$chars[1]['place']]['speed'][$valve2]];
                preventEruption($power, $chars, $time, $valves, $open, $opening, [$valve1, $valve2], $path . "{$valve1}(0).{$valve2}(1)", $workingValves, $result);
            }
        }
    } elseif (count($paths)) {
        foreach ($paths as $index => $charPaths) {
            foreach ($charPaths as $target => $value) {
                $chars = $characters;
                $mov = $moving;
                $mov[] = $target;
                $chars[$index]['moving'] = ['target' => $target, 'time' => $valves[$chars[$index]['place']]['speed'][$target]];
                preventEruption($power, $chars, $time, $valves, $open, $opening, $mov, $path . "{$target}({$index})", $workingValves, $result);
            }
        }
    } else {
        preventEruption($power, $characters, $time, $valves, $open, $opening, $moving, $path, $workingValves, $result);
    }

    $result[] = $power + $time * calcPower($open, $valves);

    return;
}

function calcPower($open, &$valves) {
    $power = 0;
    foreach ($open as $valve) {
        $power += $valves[$valve]['flow'];
    }
    return $power;
}