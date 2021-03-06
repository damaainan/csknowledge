<?php
function bubbleSort(array $numbers = array())
{
    $count = count($numbers);
    if ($count <= 1) {
        return $numbers;
    }

    for ($i = 0; $i < $count - 1; $i++) {
        for ($j = 0; $j < $count - $i - 1; $j++) {
            if ($numbers[$j] > $numbers[$j + 1]) {
                $temp            = $numbers[$j];
                $numbers[$j]     = $numbers[$j + 1];
                $numbers[$j + 1] = $temp;
            }
        }
    }

    return $numbers;
}

$arr = [];
for ($i = 0; $i < 5000; $i++) {
    $arr[] = rand(1, 10000);
}

$start_time = microtime(true);

$sort = bubbleSort($arr);

$end_time = microtime(true);
$need_time = $end_time - $start_time;

print_r("排序耗时:" . $need_time . "\r\n");