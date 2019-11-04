<?php declare(strict_types=1);
/*
当 A 的子数组 A[i], A[i+1], ..., A[j] 满足下列条件时，我们称其为_湍流子数组_：

* 若 i <= k < j，当 k 为奇数时， A[k] > A[k+1]，且当 k 为偶数时，A[k] < A[k+1]；
* **或**若 i <= k < j，当 k 为偶数时，A[k] > A[k+1] ，且当 k 为奇数时， A[k] < A[k+1]。

也就是说，如果比较符号在子数组中的每个相邻元素对之间翻转，则该子数组是湍流子数组。

返回 A 的最大湍流子数组的**长度**。

**示例 1：**

    **输入：**[9,4,2,10,7,8,8,1,9]
    **输出：**5
    **解释：**(A[1] > A[2] < A[3] > A[4] < A[5])
    

**示例 2：**

    **输入：**[4,8,12,16]
    **输出：**2
    

**示例 3：**

    **输入：**[100]
    **输出：**1
    

**提示：**

1. 1 <= A.length <= 40000
1. 0 <= A[i] <= 10^9

*/
/**
 * 最长湍流子数组 (大小关系交替)
 * @param  array $arr
 * @return int     
 */
function deal(array $arr):int{
	$start_time = microtime(true);
	$start_memory = memory_get_usage(true);
	/***************代码开始*********************/
	
	$len = count($arr);
	if(1 === $len){
		return 1;
	}
	for ($i = $len; $i >0 ; $i--) {
		for ($j = 0; $j < $len - $i + 1; $j++) {
			$ret = [];
			$subarr = array_slice($arr, $j, $i);
			array_walk($subarr, function($item, $key) use(&$ret, $subarr){
				if(!isset($subarr[$key - 1])){
					return ;
				}
				if($item > $subarr[$key - 1]){
					$ret[] = 1;
				}else if($item < $subarr[$key - 1]){
					$ret[] = -1;
				}else{
					$ret[] = 0;
				}
			});
			if(in_array(0, $ret)){

			}else{
				$rlen = count($ret);
				// 校验结果
				$even = array_filter($ret, function($v, $k) {
					if($k % 2 === 0)
					    return $v;
				}, ARRAY_FILTER_USE_BOTH);
				$odd = array_filter($ret, function($v, $k) {
					if($k % 2 === 1)
					    return $v;
				}, ARRAY_FILTER_USE_BOTH);
				
				$ueven = array_unique($even);
				$uodd = array_unique($odd);

				sort($ueven);
				sort($uodd);

				if(count($ret) == 1){
					break 2;
				}
				if(count($ueven) == 2 || count($uodd) == 2 || $ueven[0] == $uodd[0]){
					continue;
				}else{
					break 2;
				}
			}
		}
	}
	if(isset($ret)){
		$res = count($ret) + 1;
	}else{
		$res = 0;
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
	
	return $res;
}

echo deal([9,4,2,10,7,8,8,1,9]);
echo "\n*************\n";
echo deal([4,8,12,16]);
echo "\n*************\n";
echo deal([100]);
