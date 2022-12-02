<?php
$file = file('input.txt');
$score = 0;
// 1 for Rock, 2 for Paper, and 3 for Scissors
$turnScore = [
    'X' => 1,
    'Y' => 2,
    'Z' => 3
];

// 0 if you lost, 3 if the round was a draw, and 6 if you won
$matchResult = [
    'A' => [ // rock
        'X' => 3,
        'Y' => 6,
        'Z' => 0
    ],
    'B' => [ // paper
        'X' => 0,
        'Y' => 3,
        'Z' => 6
    ],
    'C' => [ // sciss
        'X' => 6,
        'Y' => 0,
        'Z' => 3
    ],
];

foreach ($file as $key => $row) {
    [$enemyTurn, $playerTurn] = explode(' ', trim($row));
    $score += $turnScore[$playerTurn] + $matchResult[$enemyTurn][$playerTurn];
}

echo $score;

echo PHP_EOL;