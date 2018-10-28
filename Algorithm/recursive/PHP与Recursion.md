### PHP与Recursion  
发表于2012-06-25  
[较好排版链接](http://www.nowamagic.net/librarys/veda/detail/2334)  
[本文地址](http://huoding.com/2012/06/25/158)  
在程序设计中，递归（Recursion）是一个很常见的概念，合理使用递归，可以提升代码的可读性，但同时也可能会带来一些问题。


下面以阶乘（Factorial）为例来说明一下递归的用法，实现语言是PHP：

```php
<?php
function factorial($n) {
    if ($n == 0) {
        return 1;
    }
    return factorial($n - 1) * $n;
}
var_dump(factorial(100));
?>
```
如果安装了XDebug的话，可能会遇到如下错误：

Fatal error: Maximum function nesting level of ‘100’ reached, aborting!

注：这是XDebug的一个保护机制，可以通过max_nesting_level选项来设置。

即便代码能正常运行，只要我们不断增大参数，程序迟早会报错：

Fatal error:  Allowed memory size of … bytes exhausted

为什么呢？简单点说就是递归造成了栈溢出。有几个方法可以用来规避这个问题，比如说利用尾调用（Tail Call）来消除递归对栈的影响。

下面以Lua作为描述语言来说明尾调用的含义，代码如下：

    function factorial(n)
        if (n == 0) then
            return 1
        end
    
        return factorial(n - 1) * n
    end

print(factorial(100))
这段代码同样会遇到栈溢出的问题。怎样利用尾调用来搞定呢？让我们先来看看尾调用的定义：如果一个函数在执行了一次函数调用后，不再做别的事就称为尾调用。形象点说就是直接返回一个函数调用。尾调用不会返回原来的函数，所以不需要额外的栈保留调用函数的数据。上面代码改成尾调用后类似下面代码的样子：

    function factorial(n, accumulator)
        accumulator = accumulator or 1
    
        if (n == 0) then
            return accumulator
        end
    
        return factorial(n - 1, accumulator * n)
    end
    
    print(factorial(100))
注：关于Lua中尾调用的介绍可以参考：Proper Tail Recursion。

照猫画虎，我们用PHP来实现一个**`尾调用`**版本的阶乘：

```php
<?php

function factorial($n, $accumulator = 1) {
    if ($n == 0) {
        return $accumulator;
    }

    return factorial($n - 1, $accumulator * $n);
}

var_dump(factorial(100));

?>
```

可惜测试后才发现`PHP根本不支持尾调用`！好在天无绝人之路，仔细阅读维基百科中关于尾调用的介绍，你会发现里面提到了`Trampoline`的概念。简单点说就是`利用高阶函数消除递归`，依照这样的理论基础，我们可以把上面的尾调用代码改写成如下方式：

```php
<?php

function factorial($n, $accumulator = 1) {
    if ($n == 0) {
        return $accumulator;
    }
    return function() use($n, $accumulator) {
        return factorial($n - 1, $accumulator * $n);
    };
}

function trampoline($callback, $params) {
    $result = call_user_func_array($callback, $params);
    while (is_callable($result)) {
        $result = $result();
    }
    return $result;
}
var_dump(trampoline('factorial', array(100)));

?>
```
看上去不错，不过我不得不向大家道个歉，本文用递归实现阶乘其实是个玩笑，实际上只要用一个循环就行了，《代码大全》里专门提到了这一点：

```php
<?php

function factorial($n) {
    $result = 1;

    for ($i = 1; $i <= $n; $i++) {
        $result *= $i;
    }

    return $result;
}

var_dump(factorial(100));

?>
```
还有很多别的方法可以用来规避递归引起的栈溢出问题，比如说Python中可以通过装饰器和异常来消灭尾调用，让人有一种别有洞天的感觉：

Tail Call Optimization Decorator (Python recipe)
另外，Python之父关于为何不在Python中支持尾调用的博文也很有看头：

Tail Recursion Elimination
Final Words on Tail Calls
好了，就写到这吧。除非能提升代码可读性，否则没有必要使用递归；迫不得已之时，最好考虑使用Tail Call或Trampoline等技术来规避潜在的栈溢出问题。

此条目由老王发表在Technical分类目录，并贴了PHP、Recursion标签。将固定链接加入收藏夹。