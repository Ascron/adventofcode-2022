<?php
$file = file('test.txt', FILE_IGNORE_NEW_LINES);

$monkeys = [];
$index = 0;

$stat = [];

$lcm = 1;

foreach ($file as $line) {
    if (strpos($line, 'Monkey') === 0) {
        $index = (int) $line[7];
        $monkeys[$index] = [
            'business' => 0,
        ];
    } elseif (strpos($line, 'Starting items') === 2) {
        [,$items] = explode(': ', $line);
        $monkeys[$index]['items'] = explode(', ', $items);
    } elseif (strpos($line, 'Operation') === 2) {
        [,$operation] = explode(': ', $line);
        $operator = $operation[10];
        [,$number] = explode(' ' . $operator . ' ', $operation);

        if ($number === 'old') {
            $monkeys[$index]['operation'] = function ($item) use (&$stat) {
                return $item ** 2;
            };
        } else {
            switch ($operator) {
                case '*':
                    $monkeys[$index]['operation'] = function ($item) use ($number, &$stat) {
                        $microtime = microtime(true);
                        $result = $item * $number;
                        $stat['gmp_mul'][] = microtime(true) - $microtime;
                        return $result;
                    };
                    break;
                case '+':
                    $monkeys[$index]['operation'] = function ($item) use ($number, &$stat) {
                        $microtime = microtime(true);
                        $result = $item + $number;
                        $stat['gmp_add'][] = microtime(true) - $microtime;
                        return $result;
                    };
                    break;
            }
        }
    } elseif (strpos($line, 'Test') === 2) {
        [, $divider] = explode('divisible by ', $line);
        $lcm *= $divider;
        $monkeys[$index]['test'] = function ($item) use ($divider, &$stat) {
            return $item % $divider == 0;
        };
    } elseif (strpos($line, 'If true') === 4) {
        [, $target] = explode('to monkey ', $line);
        $monkeys[$index]['true'] = (int)$target;
    } elseif (strpos($line, 'If false') === 4) {
        [, $target] = explode('to monkey ', $line);
        $monkeys[$index]['false'] = (int)$target;
    }
}

for ($turn = 0; $turn < 10000; $turn++) {
    echo $turn . PHP_EOL;
    foreach ($monkeys as $index => $monkey) {
        while (count($monkeys[$index]['items'])) {
            $item = array_shift($monkeys[$index]['items']);
            $item = $monkey['operation']($item);
            $item %= $lcm;
            $monkeys[$index]['business']++;
            if ($monkey['test']($item)) {
                $monkeys[$monkey['true']]['items'][] = $item;
            } else {
                $monkeys[$monkey['false']]['items'][] = $item;
            }
        }
    }
}

$businesses = array_column($monkeys, 'business');
rsort($businesses);
echo $businesses[0] * $businesses[1];

echo PHP_EOL;