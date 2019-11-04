<?php declare(strict_types=1);
/*
在仅包含 0 和 1 的数组 A 中，一次 _K 位翻转_包括选择一个长度为 K 的（连续）子数组，同时将子数组中的每个 0 更改为 1，而每个 1 更改为 0。

返回所需的 K 位翻转的次数，以便数组没有值为 0 的元素。如果不可能，返回 -1。

**示例 1：**

    **输入：**A = [0,1,0], K = 1
    **输出：**2
    **解释：**先翻转 A[0]，然后翻转 A[2]。
    

**示例 2：**

    **输入：**A = [1,1,0], K = 2
    **输出：**-1
    **解释：**无论我们怎样翻转大小为 2 的子数组，我们都不能使数组变为 [1,1,1]。
    

**示例 3：**

    **输入：**A = [0,0,0,1,0,1,1,0], K = 3
    **输出：**3
    **解释：**
    翻转 A[0],A[1],A[2]: A变成 [1,1,1,1,0,1,1,0]
    翻转 A[4],A[5],A[6]: A变成 [1,1,1,1,1,0,0,0]
    翻转 A[5],A[6],A[7]: A变成 [1,1,1,1,1,1,1,1]
    

**提示：**

1. 1 <= A.length <= 30000
1. 1 <= K <= A.length


*/
/**
 * K 连续位的最小翻转次数 (连续翻转)
 * @param  array $arr
 * @param  int $k
 * @return int     
 */
function deal(array $arr, int $k):int{
	$start_time = microtime(true);
	$start_memory = memory_get_usage(true);
	/***************代码开始*********************/
	
	$len = count($arr);
	$ret = -1;

	//未完
	for ($j = 0; $j < $len - $k + 1; $j++) {
		$subarr = array_slice($arr, $j, $k);
		if(array_unshift($subarr) == 1){
			continue;
		}
		if(!in_array(0, $subarr)){
			continue;
		}
		
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
	
	return $ret;
}

echo deal([0,1,0], 1);
echo "\n*************\n";
echo deal([1,1,0], 2);
echo "\n*************\n";
echo deal([0,0,0,1,0,1,1,0], 3);
