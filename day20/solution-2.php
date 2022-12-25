<?php
$file = file('input.txt', FILE_IGNORE_NEW_LINES);

$sequence = [];

foreach ($file as $index => $line) {
    $element = new stdClass();
    $element->index = $index;
    $element->value = gmp_mul($line, '811589153');
    $sequence[] = $element;
}

for ($j = 0; $j < 10; $j++) {
    for ($i = 0, $iMax = count($sequence); $i < $iMax; $i++) {
        $element = $sequence[$i];
        if (gmp_strval($element->value) === '0') {
            continue;
        }
        $value = gmp_intval(gmp_div_r($element->value, $iMax - 1));
        $newIndex = ($element->index + $value + ($iMax - 1) * 2) % ($iMax - 1);
        $oldIndex = $element->index;
        foreach ($sequence as &$item) {
            if ($item->index > $oldIndex) {
                $item->index--;
            }
            if ($item->index >= $newIndex) {
                $item->index++;
            }
        }
        unset($item);
        $element->index = $newIndex;
    }
    $zz =1;
}


$search = [];
foreach ($sequence as $item) {
    if (gmp_strval($item->value) === '0') {
        $search[] = ($item->index + 1000) % $iMax;
        $search[] = ($item->index + 2000) % $iMax;
        $search[] = ($item->index + 3000) % $iMax;
        break;
    }
}
$result = gmp_init(0);
foreach ($sequence as $item) {
    if (in_array($item->index, $search)) {
        $result = gmp_add($result, $item->value);
    }
}

echo gmp_strval($result) . PHP_EOL;