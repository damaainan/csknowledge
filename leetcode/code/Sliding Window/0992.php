<?php declare(strict_types=1);
/*
给定一个正整数数组 A，如果 A 的某个子数组中不同整数的个数恰好为 K，则称 A 的这个连续、不一定独立的子数组为_好子数组_。

（例如，[1,2,3,1,2] 中有 3 个不同的整数：1，2，以及 3。）

返回 A 中_好子数组_的数目。

**示例 1：**

    **输出：**A = [1,2,1,2,3], K = 2
    **输入：**7
    **解释：**恰好由 2 个不同整数组成的子数组：[1,2], [2,1], [1,2], [2,3], [1,2,1], [2,1,2], [1,2,1,2].
    

**示例 2：**

    **输入：**A = [1,2,1,3,4], K = 3
    **输出：**3
    **解释：**恰好由 3 个不同整数组成的子数组：[1,2,1,3], [2,1,3], [1,3,4].
    

**提示：**

1. 1 <= A.length <= 20000
1. 1 <= A[i] <= A.length
1. 1 <= K <= A.length


*/
/**
 * K 个不同整数的子数组 (子数组实际只包含 k 个不同的值)
 * @param  array $arr
 * @param  int $k
 * @return int     
 */
function deal(array $arr, int $k):int{
	$start_time = microtime(true);
	$start_memory = memory_get_usage(true);
	/***************代码开始*********************/
	
	$len = count($arr);
	$ret = 0;
	for ($i = $len; $i > 0 ; $i--) {
		for ($j = 0; $j < $len - $i + 1; $j++) {
			$subarr = array_slice($arr, $j, $i);
			$uarr = array_unique($subarr);
			if(count($uarr) == $k){
				$ret++;
			}
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

echo deal([1,2,1,2,3], 2);
echo "\n*************\n";
echo deal([1,2,1,3,4], 3);
echo "\n*************\n";
