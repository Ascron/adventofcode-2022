<?php
$file = file('input.txt', FILE_IGNORE_NEW_LINES);

$index = 1;
$pairs = [];
foreach ($file as $line) {
    if ($line === '') {
        $index++;
        continue;
    }

    $pairs[$index][] = json_decode($line, true);
}

$result = 0;

foreach ($pairs as $index => $pair) {
    if (compare($pair[0], $pair[1])) {
        $result += $index;
    }
}

echo $result;

echo PHP_EOL;

function compare($one, $two) {
    if (is_array($one) && !is_array($two)) {
        $two = (array)$two;
    }

    if (is_array($two) && !is_array($one)) {
        $one = (array)$one;
    }

    if (is_array($one) && is_array($two)) {
        foreach ($one as $index => $item) {
            if (!isset($two[$index])) {
                return false;
            }

            $result = compare($item, $two[$index]);
            if ($result !== null) {
                return $result;
            }
        }

        if (count($one) > 0 && isset($two[$index + 1])) {
            return true;
        }

        if (count($one) === 0 && count($two) > 0) {
            return true;
        }
    } else {
        return ($one === $two) ? null : $one < $two;
    }

    return null;
}