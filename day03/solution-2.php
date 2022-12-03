<?php
$file = file('input.txt', FILE_IGNORE_NEW_LINES);
$comps = [];
$result = 0;
foreach ($file as $line) {
    $comps[] = $line;
    if (count($comps) === 3) {
        $converted = [];
        foreach ($comps as $index => $comp) {
            for ($i = 0, $c = strlen($comp); $i < $c; $i++) {
                $priority = ord($comp[$i]);
                if ($priority > 96) {
                    $priority -= 96;
                } else {
                    $priority -= 38;
                }
                $converted[$index][] = $priority;
            }
        }
        $intersect = array_intersect($converted[0], $converted[1], $converted[2]);
        $result += reset($intersect);
        $comps = [];
    }
}

echo $result;

echo PHP_EOL;