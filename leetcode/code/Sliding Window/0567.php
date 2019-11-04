<?php declare(strict_types=1);
/*
给定两个字符串 **s1** 和 **s2**，写一个函数来判断 **s2** 是否包含 **s1**的排列。

换句话说，第一个字符串的排列之一是第二个字符串的子串。

**示例1:**

    **输入:**s1 = "ab" s2 = "eidbaooo"
    **输出:**True
    **解释:** s2 包含 s1 的排列之一 ("ba").
    

**示例2:**

    **输入:**s1= "ab" s2 = "eidboaoo"
    **输出:** False
    

**注意：**

1. 输入的字符串只包含小写字母
1. 两个字符串的长度都在 [1, 10,000] 之间


*/
/**
 * 字符串的排列
 * @param  string $in 
 * @param  string $str 
 * @return boolean     
 */
function deal(string $in, string $str):string{
	$start_time = microtime(true);
	$start_memory = memory_get_usage(true);
	/***************代码开始*********************/
	$inlen = strlen($in);
	$inarr = str_split($in);
	sort($inarr);
	$len = strlen($str);

	for ($i = 0; $i < $len - $inlen; $i++) {
		$substr = substr($str, $i, $inlen);
		$subarr = str_split($substr);
		sort($subarr);
		if($inarr == $subarr){
			return 'True';
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
	
	return 'False';
}

echo deal('ab', 'eidbaooo');
echo "\n*************\n";
echo deal('ab', 'eidboaoo');
