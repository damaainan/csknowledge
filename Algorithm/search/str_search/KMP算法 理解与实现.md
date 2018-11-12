## KMP算法 理解与实现

2018.07.14 09:48*

来源：[https://www.jianshu.com/p/d4cf13b32111](https://www.jianshu.com/p/d4cf13b32111)


KMP算法，背景不必多说，主要想写一写自己对KMP算法的一些理解和其具体实现。

关于KMP算法的原理，阮一峰老师的这篇文章足矣。

[字符串匹配的KMP算法][7]

文中对KMP算法的匹配过程以及“部分匹配表”具体代表什么，都解释的十分简洁明了，看过之后也算是对KMP算法有了一个直观的了解。

下面我想就算法的具体实现，尤其是“部分匹配表”的生成（个人认为KMP算法实现中，最不容易理解的部分），进行一些分析。
### KMP算法具体实现

KMP算法的主体是，在失去匹配时，查询最后一个匹配字符所对应的“部分匹配表“中的值，然后向前移动，移动位数为：
`移动位数 = 已匹配的字符数 - 对应的部分匹配值`比如对如下的匹配：


![][0]


在 **`D`** 处失去匹配，那么查询最后一个匹配字符 **`B`** 在部分匹配表中的值为 **`2`** 。

则向前移动6-2= **`4`** 位。（其实就相当于从搜索词的第二位开始重新进行比较）


![][1]


下面是算法主体的实现

这里的`next`数组即“部分匹配表”。注意因为搜索词最后一位对应的部分匹配值是没有意义的，所以为了编程方便，我们将”部分匹配表“整体向后移一位，并把第一位设为-1。

```java
//Java

/**
* KMP算法.

* 在目标字符串中对搜索词进行搜索。

* 
* @param t 目标字符串
* @param p 搜搜词
* @return 搜索词第一次匹配到的起始位置或-1
*/
public int KMP(String t, String p) {
    char[] target = t.toCharArray();
    char[] pattern = p.toCharArray();
    // 目标字符串下标
    int i = 0;
    // 搜索词下标
    int j = 0;
    // 整体右移一位的部分匹配表
    int[] next = getNext(pattern);

    while (i < target.length && j < patter.length) {
        // j == -1 表示从搜索词最开始进行匹配
        if (j == -1 || target[i] == pattern[j]) {
            i++;
            j++;
        // 匹配失败时，查询“部分匹配表”，得到搜索词位置j以前的最大共同前后缀长度
        // 将j移动到最大共同前后缀长度的后一位，然后再继续进行匹配
        } else {
            j = next[j];
        }
    }

    // 搜索词每一位都能匹配成功，返回匹配的的起始位置
    if (j == pattern.length)
        return i - j;
    else
        return -1;
}

```

KMP算法的搜索过程还是比较好理解的。

接下来最容易被绕进去的部分来了，求解“部分匹配表”即`next`数组。
### 部分匹配表(next数组)的生成

其实，求next数组的过程完全可以看成字符串匹配的过程，即以搜索词为主字符串，以搜索词的 **`前缀`** 为目标字符串，一旦字符串匹配成功，那么当前的next值就是匹配成功的字符串的长度。

具体来说，就是从模式字符串的第一位( **`注意，不包括第0位`** )开始对自身进行匹配运算。 在任一位置，能匹配的最长长度就是当前位置的next值，如下图所示。

(这里next数组下标从1开始表示)


![][2]


![][3]


![][4]


![][5]


![][6]


下面是算法的具体实现

```java
//Java

/**
* 生成部分匹配表.

* 生成搜索词的部分匹配表

* 
* @param p 搜搜词
* @return 部分匹配表
*/
private int[] getNext(String pattern) {
    char[] p = pattern.toCharArray();
    int[] next = new int[p.length];
  // 第一位设为-1，方便判断当前位置是否为搜索词的最开始
    next[0] = -1;
    int i = 0;
    int j = -1;

    while(i < p.length - 1) {
        if (j == -1 || p[i] == p[j]) {
            i++;
            j++;
            next[i] = j;
        } else {
            j = next[j];
        }
    }

    return next;
}

```

这里`j = next[j]`的写法十分巧妙，却有点难以理解(至少我一开始想了很久...)，其实就是一个不断的回溯过程。
`next[j]`表示`p[j]`前面的 **`最大`** 共通前缀后缀的 **`长度`** ，那么`p[next[j]]`则表示这个共通前后缀的最后一个字符。

如果`p[next[j]] == p[j]`则可以肯定`next[j+1] == next[j] + 1`。而当`p[next[j]] != p[j]`时，就应该考虑，既然`next[j]`长度的前缀后缀都不能匹配了，那么就应该缩短这个匹配的长度。直接从头开始重新匹配，那就是最朴素的暴力匹配了，效率太低。

此时对于`next[j]`这个字符串本身，也是有自己的最大共通前后缀的，那么`next[next[j]]`则代表`p[next[j]]`前面的 **`最大`** 共通前缀后缀长度。所以令`j = next[j]`然后从`p[j]`重新开始比较，如果不匹配的话再重复以上过程，最终得到结果。


[7]: http://www.ruanyifeng.com/blog/2013/05/Knuth%E2%80%93Morris%E2%80%93Pratt_algorithm.html
[0]: https://upload-images.jianshu.io/upload_images/679154-4b19618459179ecc.jpg
[1]: https://upload-images.jianshu.io/upload_images/679154-0cb36dfe404013ab.jpg
[2]: https://upload-images.jianshu.io/upload_images/679154-55e3ce1012c52c3b.jpg
[3]: https://upload-images.jianshu.io/upload_images/679154-d17ed028ce5a4099.jpg
[4]: https://upload-images.jianshu.io/upload_images/679154-b1c1d019d99536b7.jpg
[5]: https://upload-images.jianshu.io/upload_images/679154-057c0e1b20deb543.jpg
[6]: https://upload-images.jianshu.io/upload_images/679154-537d729e32a1e62f.jpg