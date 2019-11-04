<?php declare(strict_types=1);
/*
给定一个字符串，请你找出其中不含有重复字符的 **最长子串**的长度。

**示例 1:**

    **输入:**"abcabcbb"
    **输出:**3 
    **解释:** 因为无重复字符的最长子串是 "abc"，所以其长度为 3。
    

**示例 2:**

    **输入:**"bbbbb"
    **输出:**1
    **解释:**因为无重复字符的最长子串是 "b"，所以其长度为 1。
    

**示例 3:**

    **输入:**"pwwkew"
    **输出:**3
    **解释:**因为无重复字符的最长子串是 "wke"，所以其长度为 3。
         请注意，你的答案必须是 **子串**的长度，"pwke" 是一个_子序列，_不是子串。



*/
function deal(string $str):int{
	$start_time = microtime(true);
	$start_memory = memory_get_usage(true);

	$len = strlen($str);
	if($len <= 1){
		return $len;
	}
	$arr = str_split($str);
	$ret = [];
	$temp = [];

	foreach ($arr as $sva) {
		if(!in_array($sva, $temp)){
			$temp[] = $sva;
		}else{
			if(count($temp) > count($ret)){
				$ret = $temp;
			}
		}
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

	return count($ret);
}

echo deal("abcabcbb");
echo "\n";
echo deal("bbbbb");
echo "\n";
echo deal("pwwkew");