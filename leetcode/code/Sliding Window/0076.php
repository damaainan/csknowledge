<?php declare(strict_types=1);
/*
给你一个字符串 S、一个字符串 T，请在字符串 S 里面找出：包含 T 所有字母的最小子串。

**示例：**

    **输入: S** = "ADOBECODEBANC", **T** = "ABC"
    **输出:** "BANC"

**说明：**

* 如果 S 中不存这样的子串，则返回空字符串 ""。
* 如果 S 中存在这样的子串，我们保证它是唯一的答案。


*/
/**
 * 寻找最小子串
 * @param  string $str 
 * @param  string $in  目标字符串
 * @return string     
 */
function deal(string $str, string $in):string{
	$start_time = microtime(true);
	$start_memory = memory_get_usage(true);
	
	$len = strlen($str);
	if($len === 0){
		return '';
	}
	$harr = str_split($str);
	$inarr = str_split($in);
	$temp = [];
	$ret = [];

	for ($i = 1; $i < $len - 1; $i++) {
		for ($j = 0; $j < $len - 1; $j++) {
			$flag = 0;
			$tstr = substr($str, $j, $i);
			foreach ($inarr as $inva) {
				if(strpos($tstr, $inva) === false){
					$flag = 1;
					continue;
				}
			}
					
			if($flag){
				// 不合要求
			}else{
				$ret[] = $tstr;
				$flag = 0;
			}
		}
		if(count($ret) > 0){
			break;
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
	if(empty($ret)){
		$ret[] = "";
	}
	return implode('*', $ret);
}

echo deal("ADOBECODEBANC", 'ABC');
echo "\n*************\n";
echo deal("ADOBECODEBANC", 'QP');