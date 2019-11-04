<?php declare(strict_types=1);
/*
给定一个数组 _nums_，有一个大小为 _k_的滑动窗口从数组的最左侧移动到数组的最右侧。你只可以看到在滑动窗口内的 _k_ 个数字。滑动窗口每次只向右移动一位。

返回滑动窗口中的最大值。

**示例:**

    **输入:** _nums_ = [1,3,-1,-3,5,3,6,7], 和 _k_ = 3
    **输出:**[3,3,5,5,6,7] 
    **解释:**
      滑动窗口的位置                最大值
    ---------------               -----
    [1  3  -1] -3  5  3  6  7       3
     1 [3  -1  -3] 5  3  6  7       3
     1  3 [-1  -3  5] 3  6  7       5
     1  3  -1 [-3  5  3] 6  7       5
     1  3  -1  -3 [5  3  6] 7       6
     1  3  -1  -3  5 [3  6  7]      7

**提示：**

你可以假设 _k_总是有效的，在输入数组不为空的情况下，1 ≤ k ≤ 输入数组的大小。

**进阶：**

你能在线性时间复杂度内解决此题吗？


*/
/**
 * 滑动窗口最大值  子数组最大值
 * @param  array $arr 
 * @param  int $len 
 * @return string     
 */
function deal(array $arr, int $len):string{
	$start_time = microtime(true);
	$start_memory = memory_get_usage(true);
	
	if(1 === $len){
		return $arr;
	}
	$alen = count($arr);
	$ret = [];
	for ($i = 0; $i < $alen - $len + 1; $i++) {
		$sub_arr = array_slice($arr, $i, $len);
		$ret[] = max($sub_arr);
	}

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
