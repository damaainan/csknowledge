<?php declare(strict_types=1);
/*
给你一个仅由大写英文字母组成的字符串，你可以将任意位置上的字符替换成另外的字符，总共可最多替换 _k_次。在执行上述操作后，找到包含重复字母的最长子串的长度。

**注意:**  
字符串长度 和 _k_不会超过 10000。

**示例 1:**

    **输入:**
    s = "ABAB", k = 2
    
    **输出:**
    4
    
    **解释:**
    用两个'A'替换为两个'B',反之亦然。
    

**示例 2:**

    **输入:**
    s = "AABABBA", k = 1
    
    **输出:**
    4
    
    **解释:**
    将中间的一个'A'替换为'B',字符串变为 "AABBBBA"。
    子串 "BBBB" 有最长重复字母, 答案为 4。




*/
/**
 * 替换后的最长重复字符
 * @param  string $str 
 * @param  int $num 
 * @return int     
 */
function deal(string $str, int $num):int{
	$start_time = microtime(true);
	$start_memory = memory_get_usage(true);
	/***************代码开始*********************/

	$len = strlen($str);
	$ret = '';
	for ($i = $len; $i > 0 ; $i--) {
		for ($j = 0; $j < $len - $i + 1; $j++) {
			$substr = substr($str, $j, $i);
			// 其他字符个数正好等于可替换次数
			$strarr = str_split($substr);
			$strarr = array_unique($strarr);
			foreach ($strarr as $sva) {
				if(substr_count($substr, $sva) === ($i - $num)){
					$ret = $substr;
					echo $ret;
					break 3;
				}
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
	
	return strlen($ret);
}

echo deal('ABAB', 2);
echo "\n*************\n";
echo deal('AABABBA', 1);
