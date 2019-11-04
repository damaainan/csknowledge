<?php declare(strict_types=1);
/*
中位数是有序序列最中间的那个数。如果序列的大小是偶数，则没有最中间的数；此时中位数是最中间的两个数的平均数。

例如：

[2,3,4]，中位数是 3[2,3]，中位数是 (2 + 3) / 2 = 2.5给出一个数组 nums，有一个大小为 _k_ 的窗口从最左端滑动到最右端。窗口中有 k 个数，每次窗口移动 1 位。你的任务是找出每次窗口移动后得到的新窗口中元素的中位数，并输出由它们组成的数组。

例如：

给出 _nums_ = [1,3,-1,-3,5,3,6,7]，以及 _k_ = 3。

    窗口位置                      中位数
    ---------------               -----
    [1  3  -1] -3  5  3  6  7       1
     1 [3  -1  -3] 5  3  6  7       -1
     1  3 [-1  -3  5] 3  6  7       -1
     1  3  -1 [-3  5  3] 6  7       3
     1  3  -1  -3 [5  3  6] 7       5
     1  3  -1  -3  5 [3  6  7]      6
    

因此，返回该滑动窗口的中位数数组 [1,-1,-1,3,5,6]。

**提示：**  
假设k是合法的，即：k 始终小于输入的非空数组的元素个数.




*/
/**
 * 滑动窗口中位数
 * @param  array $arr 
 * @param  int $num 
 * @return string     
 */
function deal(array $arr, int $num):string{
	$start_time = microtime(true);
	$start_memory = memory_get_usage(true);
	/***************代码开始*********************/

	$len = count($arr);
	$ret = [];
	for ($i = 0; $i < $len - $num + 1; $i++) {
		$temp = [];
		$subarr = array_slice($arr, $i, $num);
		sort($subarr);
		$temp = array_slice($subarr, (int)floor($num /2), (int)(2 - ($num % 2)));
		$ret = array_merge($ret, $temp);
	}
	
	/***************代码结束*******************/
	/***********性能监控***********************/ 
	$end_time = microtime(true);
	$need_time = $end_time - $start_time;
	$end_memory = memory_get_usage(true);
	$memory_peak = memory_get_peak_usage(true);
	print_r("耗时:" . $need_time . "\r\n");
	print_r("开始内存:" . $start_memory . "\r\n");
	print_r("结束内存:" . $end_memory . "\r\n");
	print_r("峰值内存:" . $memory_peak . "\r\n");
	/*******************************************/
	
	return '[' . implode(',', $ret) . ']';
}

echo deal([1,3,-1,-3,5,3,6,7], 3);
echo "\n*************\n";
