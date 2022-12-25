<?php
$file = file('input.txt', FILE_IGNORE_NEW_LINES);

$sequence = [];

foreach ($file as $index => $line) {
    $element = new stdClass();
    $element->index = $index;
    $element->value = (int)$line;
    $sequence[] = $element;
}

for ($i = 0, $iMax = count($sequence); $i < $iMax; $i++) {
    $element = $sequence[$i];
    if ($element->value === 0) {
        continue;
    }
    $newIndex = ($element->index + $element->value + ($iMax - 1) * 2) % ($iMax - 1);
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

$search = [];
foreach ($sequence as $item) {
    if ($item->value === 0) {
        $search[] = ($item->index + 1000) % $iMax;
        $search[] = ($item->index + 2000) % $iMax;
        $search[] = ($item->index + 3000) % $iMax;
        break;
    }
}
$result = 0;
foreach ($sequence as $item) {
    if (in_array($item->index, $search)) {
        $result += $item->value;
    }
}

echo $result . PHP_EOL;