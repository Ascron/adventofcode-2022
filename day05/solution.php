<?php
$file = file('input.txt', FILE_IGNORE_NEW_LINES);

$mode = 0;
$stacks = [];
foreach ($file as $line) {
    if ($line === '') {
        $mode++;
        ksort($stacks);
        continue;
    }

    if ($mode === 0) {
        for ($i = 0, $c = strlen($line); $i < $c; $i++) {
            $pos = strpos($line, '[', $i);
            if ($pos !== false) {
                $i = $pos + 1;
                $stack = $pos / 4 + 1;
                if (!isset($stacks[$stack])) {
                    $stacks[$stack] = [];
                }
                array_unshift($stacks[$stack], substr($line, $pos + 1, 1));
            }
        }
    } else {
        // move 3 from 9 to 7
        [, $amount, , $from, , $to] = explode(' ', $line);
        $slice = array_splice($stacks[$from], -(int)$amount, (int)$amount);
        array_push($stacks[$to], ...array_reverse($slice));
    }
}

$result = '';
foreach ($stacks as $stack) {
    $key = end($stack);
    $result .= $key;
}

echo $result;

echo PHP_EOL;