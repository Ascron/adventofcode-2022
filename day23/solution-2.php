<?php
$file = file('input.txt', FILE_IGNORE_NEW_LINES);
$map = [];
$elves = [];
foreach ($file as $y => $line) {
    foreach (str_split($line) as $x => $char) {
        if ($char === '#') {
            $map[$y][$x] = 1;
            $elves[] = [
                'x' => $x,
                'y' => $y,
                'decision' => null,
            ];
        }
    }
}
$counter = 0;
$index = 0;
while (true) {
    $nobodyMoved = true;
    $decisions = [];
    foreach ($elves as $elfIndex => &$elf) {
        $elf['decision'] = null;
        if (nobodyAround($map, $elf['x'], $elf['y'])) {
            continue;
        }
        for ($i = $index; $i < $index+4; $i++) {
            $newPlace = possibleDecision($i % 4, $map, $elf['x'], $elf['y']);
            if ($newPlace) {
                $elf['decision'] = $newPlace;
                if (isset($decisions[$newPlace[1]][$newPlace[0]])) {
                    $decisions[$newPlace[1]][$newPlace[0]]++;
                } else {
                    $decisions[$newPlace[1]][$newPlace[0]] = 1;
                }
                break;
            }
        }
    }
    unset($elf);

    foreach ($elves as $elfIndex => &$elf) {
        if ($elf['decision'] && $decisions[$elf['decision'][1]][$elf['decision'][0]] === 1) {
            unset($map[$elf['y']][$elf['x']]);
            $elf['x'] = $elf['decision'][0];
            $elf['y'] = $elf['decision'][1];
            $map[$elf['y']][$elf['x']] = 1;
            $nobodyMoved = false;
            $elf['decision'] = null;
        }
    }
    unset($elf);

    if ($nobodyMoved) {
        break;
    }

//    if (++$counter >= 10) {
//        break;
//    }

    $index++;
}

$xs = array_column($elves, 'x');
$ys = array_column($elves, 'y');
//echo (max($xs) - min($xs) + 1) * (max($ys) - min($ys) + 1) - count($elves);

echo $index + 1;

echo PHP_EOL;
for ($y = min($ys); $y <= max($ys); $y++) {
    for ($x = min($xs); $x <= max($xs); $x++) {
        if (isset($map[$y][$x])) {
            echo '#';
        } else {
            echo '.';
        }
    }
    echo PHP_EOL;
}


echo PHP_EOL;

function possibleDecision($index, $map, $x, $y) {
    $directions = [
        // north
        [
            [
                [-1, -1],
                [0, -1],
                [1, -1],
            ],
            [0, -1]
        ],
        // south
        [
            [
                [-1, 1],
                [0, 1],
                [1, 1],
            ],
            [0, 1]
        ],
        // west
        [
            [
                [-1, -1],
                [-1, 0],
                [-1, 1],
            ],
            [-1, 0]
        ],
        // east
        [
            [
                [1, -1],
                [1, 0],
                [1, 1],
            ],
            [1, 0]
        ],
    ];

    $direction = $directions[$index];
    foreach ($direction[0] as $options) {
        $options[0] += $x;
        $options[1] += $y;
        if (isset($map[$options[1]][$options[0]])) {
            return false;
        }
    }

    return [$x + $direction[1][0], $y + $direction[1][1]];
}

function nobodyAround($map, $x, $y) {
    $around = [
        [-1, -1],
        [0, -1],
        [1, -1],
        [-1, 0],
        [1, 0],
        [-1, 1],
        [0, 1],
        [1, 1],
    ];

    foreach ($around as $options) {
        $options[0] += $x;
        $options[1] += $y;
        if (isset($map[$options[1]][$options[0]])) {
            return false;
        }
    }

    return true;
}