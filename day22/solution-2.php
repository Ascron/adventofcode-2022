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
$squareSize = (int)(count($map) / 4);
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
                            [$newFacing, $newX, $newY] = findNext($x, $y, $facing, $squareSize);
                            if ($map[$newY][$newX] === 0) {
                                $place = [$newX, $newY];
                                $facing = $newFacing;
                            }
                        }
                        break;
                    case '<':
                        if (isset($map[$y][$x - 1]) && $map[$y][$x - 1] === 0) {
                            $place = [$x - 1, $y];
                        } elseif (!isset($map[$y][$x - 1])) {
                            [$newFacing, $newX, $newY] = findNext($x, $y, $facing, $squareSize);
                            if ($map[$newY][$newX] === 0) {
                                $place = [$newX, $newY];
                                $facing = $newFacing;
                            }
                        }
                        break;
                    case '^':
                        if (isset($map[$y - 1][$x]) && $map[$y - 1][$x] === 0) {
                            $place = [$x, $y - 1];
                        } elseif (!isset($map[$y - 1][$x])) {
                            [$newFacing, $newX, $newY] = findNext($x, $y, $facing, $squareSize);
                            if ($map[$newY][$newX] === 0) {
                                $place = [$newX, $newY];
                                $facing = $newFacing;
                            }
                        }
                        break;
                    case 'v':
                        if (isset($map[$y + 1][$x]) && $map[$y + 1][$x] === 0) {
                            $place = [$x, $y + 1];
                        } elseif (!isset($map[$y + 1][$x])) {
                            [$newFacing, $newX, $newY] = findNext($x, $y, $facing, $squareSize);
                            if ($map[$newY][$newX] === 0) {
                                $place = [$newX, $newY];
                                $facing = $newFacing;
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

function findNext($x, $y, $facing, $size) {
    switch ($facing) {
        case '>':
            if ($y >= 0 && $y < $size) {
                return ['<', $size * 2 - 1, $size * 3 - 1 - $y];
            }
            if ($y >= $size && $y < $size * 2) {
                return ['^', $y + $size, $size - 1];
            }
            if ($y >= $size * 2 && $y < $size * 3) {
                return ['<', $size * 3 - 1, $size * 3 - 1 - $y];
            }
            if ($y >= $size * 3 && $y < $size * 4) {
                return ['^', $y - $size * 2, $size * 3 - 1];
            }
            die('error');
            break;
        case '<':
            if ($y >= 0 && $y < $size) {
                return ['>', 0, $size * 3 - $y - 1];
            }
            if ($y >= $size && $y < $size * 2) {
                return ['v', $y - $size, $size * 2];
            }
            if ($y >= $size * 2 && $y < $size * 3) {
                return ['>', $size, $size * 3 - $y - 1];
            }
            if ($y >= $size * 3 && $y < $size * 4) {
                return ['v', $y - $size * 2, 0];
            }
            die('error');
            break;
        case '^':
            if ($x >= 0 && $x < $size) {
                return ['>', $size, $size + $x];
            }
            if ($x >= $size && $x < $size * 2) {
                return ['>', 0, $x + $size * 2];
            }
            if ($x >= $size * 2 && $x < $size * 3) {
                return ['^', $x - $size * 2, $size * 4 - 1];
            }
            die('error');
            break;
        case 'v':
            if ($x >= 0 && $x < $size) {
                return ['v', $size * 2 + $x, 0];
            }
            if ($x >= $size && $x < $size * 2) {
                return ['<', $size - 1, $x + $size * 2];
            }
            if ($x >= $size * 2 && $x < $size * 3) {
                return ['<', $size * 2 - 1, $x - $size];
            }
            die('error');
            break;
    }
}