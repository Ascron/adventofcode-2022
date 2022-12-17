<?php
$commandsLine = file_get_contents('input.txt');
$commandsLenght = strlen($commandsLine);

$rocks = 0;
$rockLimit = 2022;
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

while ($rocks < $rockLimit) {

    if ($current == null) {
        $current = getItem($figures, $figuresCount, $figure++);
        $y = $high + 3;
        $x = 2;
    }

    while ($current !== null) {
        $command = getItem($commandsLine, $commandsLenght, $operation++);
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
            $high = max($high, maxY($current, $x, $y) + 1);
            $current = null;
            $rocks++;
        }
    }
}

printField($field);
echo $high;

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
    $y--;
    foreach (getPoints($current, $x, $y) as $item) {
        if (isset($field[$item[1]][$item[0]]) || $item[1] < 0) {
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