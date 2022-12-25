<?php
$file = file('input.txt', FILE_IGNORE_NEW_LINES);

$values = [];

foreach ($file as $line) {
    [$name, $operation] = explode(': ', $line);
    if ($name === 'humn') {
        continue;
    }
    if (is_numeric($operation)) {
        $values[$name] = (int)$operation;
    } else {
        if ($name === 'root') {
            [$operand1, $operator, $operand2] = explode(' ', $operation);
            $operator = '=';
            $values[$name] = [$operand1, $operator, $operand2];
        } else {
            $values[$name] = explode(' ', $operation);
        }
    }
}



class Human {
    public array $stack = [];

    public function addOperation($operation, $operand)
    {
        $this->stack[] = [$operation, $operand];
        return $this;
    }

    public function pop()
    {
        return array_pop($this->stack);
    }
}

$values['humn'] = new Human();
$counter = 0;
while (true) {
    $complete = false;
    echo $counter++ . PHP_EOL;
    foreach ($values as $name => $value) {
        if ($name === 'humn') {
            continue;
        }
        if (is_array($value)) {
            [$operand1, $operator, $operand2] = $value;
            if (is_string($operand1) && isset($values[$operand1]) && is_numeric($values[$operand1])) {
                $operand1 = $values[$operand1];
            }

            if (is_string($operand2) && isset($values[$operand2]) && is_numeric($values[$operand2])) {
                $operand2 = $values[$operand2];
            }

            if ($operator === '=' && ($operand1 instanceof Human || $operand2 instanceof Human)) {
                $complete = true;
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
            }

            if (
                (
                    (is_string($operand1) && $values[$operand1] instanceof Human)
                    || (is_string($operand2) && $values[$operand2] instanceof Human)
                )
                && (is_numeric($operand2) || is_numeric($operand1))) {
                $base = (is_string($operand1) && $values[$operand1] instanceof Human) ? $values[$operand1] : $values[$operand2];
                $operand = is_numeric($operand1) ? $operand1 : $operand2;
                switch ($operator) {
                    case '+':
                        $values[$name] = $base->addOperation('+', $operand);
                        break;
                    case '*':
                        $values[$name] = $base->addOperation('*', $operand);
                        break;
                    case '-':
                        if ($operand == $operand1) {
                            $values[$name] = $base->addOperation('-a', $operand);
                        } else {
                            $values[$name] = $base->addOperation('a-', $operand);
                        }

                        break;
                    case '/':
                        if ($operand == $operand1) {
                            $values[$name] = $base->addOperation('/a', $operand);
                        } else {
                            $values[$name] = $base->addOperation('a/', $operand);
                        }
                        break;
                    case '=':
                        if ($operand == $operand1) {
                            $values[$name] = [$operand, '=', $base];
                        } else {
                            $values[$name] = [$base, '=', $operand];
                        }
                        break;
                }
            }

        }
    }

    if ($complete) {
        break;
    }
}

[$operand1, , $operand2] = $values['root'];

$human = $operand1 instanceof Human ? $operand1 : $operand2;
$goal = is_numeric($operand1) ? $operand1 : $operand2;

while ([$operator, $operand] = $human->pop()) {
    switch ($operator) {
        case '+':
            $goal -= $operand;
            break;
        case '*':
            $goal = (int)($goal / $operand);
            break;
        case '-a':
            $goal = $operand - $goal;
            break;
        case 'a-':
            $goal = $goal + $operand;
            break;
        case '/a':
            $goal = (int)($operand / $goal);
            break;
        case 'a/':
            $goal = $goal * $operand;
            break;
    }
}

echo $goal;

echo PHP_EOL;