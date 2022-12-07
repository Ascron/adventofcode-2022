<?php
$file = file('input.txt', FILE_IGNORE_NEW_LINES);

function addByPwd(&$structure, $pwd, $data) {
    $path = &$structure;
    if (count($pwd)) {
        foreach ($pwd as $folder) {
            $newPath = &$path[$folder];
            unset($path);
            $path = &$newPath;
            unset($newPath);
        }
    }

    foreach ($data as $key => $value) {
        $path[$key] = $value;
    }
}

function folderSize ($path, $structure, &$result) {
    $size = 0;
    foreach ($structure as $key => $value) {
        if (is_int($value)) {
            $size += $value;
        } else {
            $size += folderSize($path . $key . '/', $structure[$key], $result);
        }
    }

    $result[$path] = $size;
    return $size;
}

$structure = [];
$pwd = [];
$lastCommand = '';

foreach ($file as $line) {
    $line = explode(' ', $line);
    if ($line[0] === '$') {
        $lastCommand = $line[1];
        switch ($line[1]) {
            case 'cd':
                if ($line[2][0] === '/') {
                    $line[2] = trim($line[2], '/');
                    if ($line[2]) {
                        $pwd = explode('/', $line[2]);
                    } else {
                        $pwd = [];
                    }
                } elseif ($line[2] === '..') {
                    array_pop($pwd);
                } else {
                    $pwd[] = $line[2];
                }
                break;
            case 'ls':
                break;
        }
    } else {
        if ($line[0] === 'dir') {
            addByPwd($structure, $pwd, [$line[1] => []]);
        } else {
            addByPwd($structure, $pwd, [$line[1] => (int)$line[0]]);
        }
    }
}


$result = [];
folderSize('/', $structure, $result);
asort($result);
$sum = 0;

$needToDelete =  $result['/'] - 70000000 + 30000000;
foreach ($result as $key => $value) {
    if ($value > $needToDelete) {
        echo $value;
        break;
    }
}


echo PHP_EOL;