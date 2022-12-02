<?php
// X means you need to lose, Y means you need to end the round in a draw, and Z means you need to win. Good luck!"

$file = file('input.txt');
$score = 0;
// 1 for Rock, 2 for Paper, and 3 for Scissors
$playsScore = [
    'A' => 1,
    'B' => 2,
    'C' => 3
];

$matchResult = [
    'X' => 0,
    'Y' => 3,
    'Z' => 6
];

$matchingTurn = [
    'A' => [ // rock
        'X' => 'C',
        'Y' => 'A',
        'Z' => 'B'
    ],
    'B' => [ // paper
        'X' => 'A',
        'Y' => 'B',
        'Z' => 'C'
    ],
    'C' => [ // sciss
        'X' => 'B',
        'Y' => 'C',
        'Z' => 'A'
    ],
];

foreach ($file as $key => $row) {
    [$enemyTurn, $playerTurn] = explode(' ', trim($row));
    $score += $matchResult[$playerTurn] + $playsScore[$matchingTurn[$enemyTurn][$playerTurn]];
}

echo $score;

echo PHP_EOL;