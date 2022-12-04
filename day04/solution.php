<?php
$file = file('input.txt', FILE_IGNORE_NEW_LINES);
$result = 0;
foreach ($file as $line) {
    $assigments = explode(',', $line);
    foreach ($assigments as &$assigment) {
        $assigment = range(...explode('-', $assigment));
    }

    $intersect = array_intersect($assigments[0], $assigments[1]);
    if (count($intersect) === min(count($assigments[0]), count($assigments[1]))) {
        $result++;
    }
}

echo $result;

echo PHP_EOL;