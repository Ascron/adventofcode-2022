<?php
$commandsLine = file_get_contents('input.txt');
$commandsLenght = strlen($commandsLine);

$rocks = 0;
$rockLimit = 6000;
$operation = 0;
$figure = 0;

$figures = [
    [[0, 0], [1, 0], [2, 0], [3, 0]],
    [[1, 0], [0, 1], [1, 1], [2, 1], [1, 2]],
    [[0, 0], [1, 0], [2, 0], [2, 1], [2, 2]],
    [[0, 0], [0, 1], [0, 2], [0, 3]],
    [[0, 0], [0, 1], [1, 0], [1, 1]]
];
$figuresCount = count($figures);

$field = [];
$high = 0;
$current = null;
$x = 0;
$y = 0;

$situations = [];

$REPEAT_DATA = [];
$repeats = 0;
while ($rocks < $rockLimit) {
    if ($current == null) {
        if ($high > 250) { // skip false similarity
            $situation = $figure . '_' . $operation . '_' . fieldHash($field, $high);

            if (isset($situations[$situation])) {
                $REPEAT_DATA = [
                    'firstRock' => $situations[$situation]['rock'],
                    'firstHigh' => $situations[$situation]['high'],
                    'repeatRock' => $rocks - $situations[$situation]['rock'],
                    'repeatHigh' => $high - $situations[$situation]['high'],
                    'figure' => $figure,
                    'operation' => $operation,
                ];
//                printField($field);
                echo 'REPEAT: ' . ' ROCK # ' . $rocks . '/' . $situations[$situation]['rock'] . ' ' . $situation . ' on high=' . $situations[$situation]['high'] . ', now high=' . $high . PHP_EOL;
                break;
            }

            $situations[$situation] = ['rock' => $rocks, 'high' => $high];
        }

        $current = getItem($figures, $figuresCount, $figure++);
        $figure = $figure % $figuresCount;
        $y = $high + 3;;
        $x = 2;
    }

    while ($current !== null) {
        $command = getItem($commandsLine, $commandsLenght, $operation++);
        $operation = $operation % $commandsLenght;
        switch ($command) {
            case '>':
                if (possibleRight($current, $x, $y, $field)) {
                    $x++;
                }
                break;
            case '<':
                if (possibleLeft($current, $x, $y, $field)) {
                    $x--;
                }
                break;
        }

        if (possibleDown($current, $x, $y, $field)) {
            $y--;
        } else {
            foreach (getPoints($current, $x, $y) as $point) {
                $field[$point[1]][$point[0]] = 1;
            }

            $maxY = maxY($current, $x, $y) + 1;
            if ($maxY > $high) {
                $high = $maxY;
            }
            $current = null;
            $rocks = $rocks + 1;
        }
    }
}

$gmpHigh = gmp_init($REPEAT_DATA['firstHigh']);
$goal = gmp_sub('1000000000000', $REPEAT_DATA['firstRock']);
$gmpHigh = gmp_add($gmpHigh, gmp_mul(gmp_div_q($goal, $REPEAT_DATA['repeatRock']), $REPEAT_DATA['repeatHigh']));
$goal = gmp_intval(gmp_mod($goal, $REPEAT_DATA['repeatRock']));

$field = [];
$high = 0;
$current = null;
$x = 0;
$y = 0;
$rockLimit = $goal;
$figure = $REPEAT_DATA['figure'];
$operation = $REPEAT_DATA['operation'];
$rocks = 0;

while ($rocks < $rockLimit) {
    if ($current == null) {

        $current = getItem($figures, $figuresCount, $figure++);
        $figure = $figure % $figuresCount;
        $y = $high + 3;;
        $x = 2;
    }

    while ($current !== null) {
        $command = getItem($commandsLine, $commandsLenght, $operation++);
        $operation = $operation % $commandsLenght;
        switch ($command) {
            case '>':
                if (possibleRight($current, $x, $y, $field)) {
                    $x++;
                }
                break;
            case '<':
                if (possibleLeft($current, $x, $y, $field)) {
                    $x--;
                }
                break;
        }

        if (possibleDown($current, $x, $y, $field)) {
            $y--;
        } else {
            foreach (getPoints($current, $x, $y) as $point) {
                $field[$point[1]][$point[0]] = 1;
            }

            $maxY = maxY($current, $x, $y) + 1;
            if ($maxY > $high) {
                $high = $maxY;
            }
            $current = null;
            $rocks = $rocks + 1;
        }
    }
}

echo gmp_strval(gmp_add($gmpHigh, $high)) . PHP_EOL;

echo PHP_EOL;

function getItem($items, $count, $index) {
    return $items[$index % $count];
}

function possibleRight($current, $x, $y, $field) {
    foreach (getPoints($current, $x + 1, $y) as $item) {
        if ($item[0] > 6 || isset($field[$item[1]][$item[0]])) {
            return false;
        }
    }
    return true;
}

function possibleLeft($current, $x, $y, $field) {
    foreach (getPoints($current, $x - 1, $y) as $item) {
        if ($item[0] < 0 || isset($field[$item[1]][$item[0]])) {
            return false;
        }
    }
    return true;
}

function getPoints($current, $x, $y) {
    foreach ($current as $item) {
        yield [$item[0] + $x, $item[1] + $y];
    }
}

function possibleDown($current, $x, $y, $field) {
    foreach (getPoints($current, $x, $y - 1) as $item) {
        if (isset($field[$item[1]][$item[0]])) {
            return false;
        }

        if ($item[1] < 0) {
            return false;
        }
    }
    return true;
}

function maxY($current, $x, $y) {
    $max = 0;
    foreach ($current as $item) {
        $max = max($max, $item[1] + $y);
    }
    return $max;
}

function printField($field) {
    krsort($field);
    foreach ($field as $y => $row) {
        echo '|';
        for ($x = 0; $x < 7; $x++) {
            echo isset($row[$x]) ? '#' : '.';
        }
        echo '|' . PHP_EOL;
    }

    echo '+-------+' . PHP_EOL;
}

function fieldHash($field, $high) {
    $hash = '';
    for ($y = 0; $y < 5; $y++) {
        $row = $high - $y - 1;
        for ($x = 0; $x < 10; $x++) {
            $hash .= isset($field[$row][$x]) ? '#' : '.';
        }
    }

    return $hash;
}