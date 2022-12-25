<?php
$file = file('input.txt', FILE_IGNORE_NEW_LINES);

$values = [];

foreach ($file as $line) {
    [$name, $operation] = explode(': ', $line);
    if (is_numeric($operation)) {
        $values[$name] = (int)$operation;
    } else {
        $values[$name] = explode(' ', $operation);
    }
}
while (true) {
    $complete = true;
    foreach ($values as $name => $value) {
        if (is_array($value)) {
            [$operand1, $operator, $operand2] = $value;
            if (!is_numeric($operand1) && isset($values[$operand1]) && is_numeric($values[$operand1])) {
                $operand1 = $values[$operand1];
            }

            if (!is_numeric($operand2) && isset($values[$operand2]) && is_numeric($values[$operand2])) {
                $operand2 = $values[$operand2];
            }

            if (is_numeric($operand1) && is_numeric($operand2)) {
                switch ($operator) {
                    case '+':
                        $values[$name] = $operand1 + $operand2;
                        break;
                    case '*':
                        $values[$name] = $operand1 * $operand2;
                        break;
                    case '-':
                        $values[$name] = $operand1 - $operand2;
                        break;
                    case '/':
                        $values[$name] = $operand1 / $operand2;
                        break;
                }

                $complete = false;
            }
        }
    }

    if ($complete) {
        break;
    }
}

echo $values['root'];

echo PHP_EOL;