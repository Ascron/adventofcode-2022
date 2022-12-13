<?php
$file = file('input.txt', FILE_IGNORE_NEW_LINES);

$pairs = [];
foreach ($file as $line) {
    if ($line === '') {
        continue;
    }

    $pairs[] = json_decode($line, true);
}

$pairs[] = [[2]];
$pairs[] = [[6]];

$result = 0;

usort($pairs, function ($a, $b) {
    return compare($a, $b) ? -1 : 1;
});

$pairs = array_map(function ($item) {
    return json_encode($item);
}, $pairs);

echo (array_search('[[2]]', $pairs) + 1) * (array_search('[[6]]', $pairs) + 1);

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