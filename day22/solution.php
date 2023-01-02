<?php
$file = file('input.txt', FILE_IGNORE_NEW_LINES);
$mode = 'map';
$place = null;
$facing = '>';
foreach ($file as $y => $line) {
    if ($line === '') {
        $mode = 'path';
        continue;
    }

    if ($mode === 'map') {
        foreach (str_split($line) as $x => $char) {
            if ($char !== ' ') {
                $map[$y][$x] = $char === '#' ? 1 : 0;
                if ($place === null) {
                    $place = [$x, $y];
                }
            }
        }
    } else {
        $path = str_split($line);
    }
}

$path[] = ['R'];
$path[] = ['L'];
$digit = '';
foreach ($path as $command) {
    if (is_numeric($command)) {
        $digit .= $command;
    } else {
        if ($digit !== '') {
            $digit = (int)$digit;
            for ($i = 0; $i < $digit; $i++) {
                [$x, $y] = $place;
                switch ($facing) {
                    case '>':
                        if (isset($map[$y][$x + 1]) && $map[$y][$x + 1] === 0) {
                            $place = [$x + 1, $y];
                        } elseif (!isset($map[$y][$x + 1])) {
                            $newX = findFirstX($y, $map);
                            if ($map[$y][$newX] === 0) {
                                $place = [$newX, $y];
                            }
                        }
                        break;
                    case '<':
                        if (isset($map[$y][$x - 1]) && $map[$y][$x - 1] === 0) {
                            $place = [$x - 1, $y];
                        } elseif (!isset($map[$y][$x - 1])) {
                            $newX = findLastX($y, $map);
                            if ($map[$y][$newX] === 0) {
                                $place = [$newX, $y];
                            }
                        }
                        break;
                    case '^':
                        if (isset($map[$y - 1][$x]) && $map[$y - 1][$x] === 0) {
                            $place = [$x, $y - 1];
                        } elseif (!isset($map[$y - 1][$x])) {
                            $newY = findLastY($x, $map);
                            if ($map[$newY][$x] === 0) {
                                $place = [$x, $newY];
                            }
                        }
                        break;
                    case 'v':
                        if (isset($map[$y + 1][$x]) && $map[$y + 1][$x] === 0) {
                            $place = [$x, $y + 1];
                        } elseif (!isset($map[$y + 1][$x])) {
                            $newY = findFirstY($x, $map);
                            if ($map[$newY][$x] === 0) {
                                $place = [$x, $newY];
                            }
                        }
                        break;
                }
            }
            $digit = '';
        }

        switch ($command) {
            case 'R':
                switch ($facing) {
                    case '>':
                        $facing = 'v';
                        break;
                    case '<':
                        $facing = '^';
                        break;
                    case '^':
                        $facing = '>';
                        break;
                    case 'v':
                        $facing = '<';
                        break;
                }
                break;
            case 'L':
                switch ($facing) {
                    case '>':
                        $facing = '^';
                        break;
                    case '<':
                        $facing = 'v';
                        break;
                    case '^':
                        $facing = '<';
                        break;
                    case 'v':
                        $facing = '>';
                        break;
                }
                break;
        }
    }
}

// Facing is 0 for right (>), 1 for down (v), 2 for left (<), and 3 for up (^)
$facingScore = ['>' => 0, 'v' => 1, '<' => 2, '^' => 3];

[$x, $y] = $place;

echo ($y + 1) * 1000 + 4 * ($x + 1) + $facingScore[$facing];

echo PHP_EOL;

function findFirstX($y, $map)
{
    return min(array_keys($map[$y]));
}

function findLastX($y, $map)
{
    return max(array_keys($map[$y]));
}

function findFirstY($x, $map)
{
    foreach ($map as $y => $line) {
        if (isset($line[$x])) {
            return $y;
        }
    }
}

function findLastY($x, $map)
{
    $y = null;
    $keys = array_keys($map);
    rsort($keys);
    foreach ($keys as $y) {
        if (isset($map[$y][$x])) {
            return $y;
        }
    }
}