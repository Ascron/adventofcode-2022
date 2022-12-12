<?php
$file = file('input.txt', FILE_IGNORE_NEW_LINES);

//Monkey 0:
//  Starting items: 72, 97
//  Operation: new = old * 13
//  Test: divisible by 19
//    If true: throw to monkey 5
//    If false: throw to monkey 6

$monkeys = [];
$index = 0;

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
        switch ($operator) {
            case '*':
                $monkeys[$index]['operation'] = function ($item) use ($number) {
                    return $item * ($number === 'old' ? $item : $number);
                };
                break;
            case '+':
                $monkeys[$index]['operation'] = function ($item) use ($number) {
                    return $item + ($number === 'old' ? $item : $number);
                };
                break;
        }
    } elseif (strpos($line, 'Test') === 2) {
        [, $divider] = explode('divisible by ', $line);
        $monkeys[$index]['test'] = function ($item) use ($divider) {
            return $item % $divider === 0;
        };
    } elseif (strpos($line, 'If true') === 4) {
        [, $target] = explode('to monkey ', $line);
        $monkeys[$index]['true'] = (int)$target;
    } elseif (strpos($line, 'If false') === 4) {
        [, $target] = explode('to monkey ', $line);
        $monkeys[$index]['false'] = (int)$target;
    }
}

for ($turn = 0; $turn < 20; $turn++) {
    foreach ($monkeys as $index => $monkey) {
        while (count($monkeys[$index]['items'])) {
            $item = array_shift($monkeys[$index]['items']);
            $item = $monkey['operation']($item);
            $item = floor($item / 3);
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