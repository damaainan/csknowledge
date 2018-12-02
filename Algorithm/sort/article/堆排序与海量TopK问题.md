## 堆排序与海量TopK问题

来源：[http://www.jianshu.com/p/c8feaee16cae](http://www.jianshu.com/p/c8feaee16cae)

时间 2018-11-30 07:15:55

 
我的博客地址：[https://rebornc.github.io/2018/11/15/%E5%A0%86%E6%8E%92%E5%BA%8F%E4%B8%8E%E6%B5%B7%E9%87%8FTopK%E9%97%AE%E9%A2%98/][3]
 
排序算法是个老生常谈的问题，笔试要考，面试也问，不过翻来覆去也就那几个花样吧。大概理解一下各个算法的原理，记下表格里的数据，然后再试试手撕代码，基本上就没问题了。

![][0]

 
从表格里可以看出，堆排序是一个时间和空间复杂度都比较优秀的算法，至于它的原理，看懂是肯定能轻易看懂的，但是我总觉得如果你不自己亲手写一遍，就很容易忘记。并且，用递归的话，代码也是很简短的，还没写过的同学，不妨自己试着敲一下吧hhh。
 
因为太久没写博客了觉得不能这么颓废下去，所以今天打算好好整理 **`堆排序`**  的相关知识点，同时讲一下面试时经常会被问到的TopK问题。
 
### 堆排序
 
#### 1. 什么是堆
 
堆（heap）是一种数据结构，也被称为优先队列（priority queue）。队列中允许的操作是先进先出（FIFO），在队尾插入元素，在队头取出元素。而堆也是一样，在堆底插入元素，在堆顶取出元素，但是堆中元素的排列不是按照到来的先后顺序，而是按照一定的优先顺序排列的。这个优先顺序可以是元素的大小或者其他规则。
 
而二叉堆是一种特殊的堆，它是完全二元树（二叉树）或者是近似完全二元树（二叉树）。二叉堆有两种：最大堆和最小堆。最大堆：父结点的键值总是大于或等于任何一个子节点的键值；最小堆：父结点的键值总是小于或等于任何一个子节点的键值。如下图。

![][1]

 
#### 2. 堆排序的原理
 
堆排序（HeapSort）是指利用堆这种数据结构所设计的一种排序算法。它的关键在于建堆和调整堆。步骤主要如下：

 
* 创建一个堆； 
* 把堆首（最大值）和堆尾互换； 
* 把堆的尺寸缩小1，并调整堆，把新的数组顶端数据调整到相应位置； 
* 重复步骤 2，直到堆的尺寸为1，此时排序结束。 
 
 
当然，光看文字肯定不能很直观地理解，我们跟着图示来学习吧。
 
现在，我们有一个待排序的数组 {2, 4, 3, 7, 5, 8}，我们通过构建最大堆的方法来排序。

![][2]

 
* 步骤说明如下：
1.将待排序的数组视作完全二叉树，按层次遍历。
2.找到二叉树的最后一个非叶子节点，也就是最后一个节点的父节点。即是 (len-1)/2 索引在的位置。如果其子节点的值大于其本身的值，则把它和较大子节点进行交换，即将数字3和8交换。如果并没有子节点大于它，则无需交换。
3.循环遍历，继续处理前一个节点，由于此时 4<7 ，因此再次交换。
4.循环遍历，继续处理前一个节点，由于此时 2<8 ，因此再次交换。 **`注意`**  ：如果某个节点和它的某个子节点交换后，该子节点又有子节点，系统还需要再次对该子节点进行判断，做相同处理。
5.遍历完成后得到一个最大堆。将每次堆排序得到的最大元素与当前规模的数组最后一个元素（假设下标为i）交换，然后再继续调整前 i - 1 的数组。遍历终止之后，得到一个自小到大的排序数组。

 `C++代码实现如下`

```cpp
void adjust(vector<int> &arr, int index, int len) {
    int left = 2 * index + 1;
    int right = 2 * index + 2;
    int max_index = index;
    if (left < len && arr[left] > arr[max_index]) max_index = left;
    if (right < len && arr[right] > arr[max_index]) max_index = right;
    if (max_index != index) {
        swap(arr[max_index], arr[index]);
        adjust(arr, max_index, len); // 继续调整子节点
    }
}
void heapSort(vector<int> &arr, int len) {
    // 将数组进行堆排序
    for (int i = len / 2 - 1; i >= 0; i--) {
        adjust(arr, i, len);
    }
    // 将每次堆排序得到的最大元素与当前规模的数组最后一个元素交换
    for (int i = len - 1; i >= 1; i--) {
        swap(arr[0], arr[i]);
        adjust(arr, 0, i);
    }
}
```
 
### 海量TopK问题
 
剑指Offer有这样一道题，求最小的K个数，题目描述：输入n个整数，找出其中最小的K个数。例如输入 4，5，1，6，2，7，3，8 这8个数字，则最小的4个数字是 1，2，3，4。
 
而在面试的时候，我们也可能遇到这样的问题：有一亿个浮点数，如何找出其中最大的10000个？
 
这类问题我们把称为 **`TopK问题`**  ：指从大量数据（源数据）中获取最大（或最小）的K个数据。
 
最容易想到的方法当然是全部排序再进行查找，然而时间复杂度怎么也要O(nlog₂n)，当n极其大时，该算法占用的内存也emmm。而我们题目所要求返回的只是前K个数据，所以没必要全部排序，做那么多无用功。我们可以先取下标 0~k-1 的局部数组，用它来维护一个大小为K的数组，然后遍历后续的数字，进行比较后决定是否替换。这时候堆排序就派上用场了。我们可以将前K个数字建立为一个最小（大）堆，如果是要取最大的K个数，则在后续遍历中，将数字与最小堆的堆顶数字进行比较，若比它大，则进行替换，然后再重新调整为最大堆。整个过程直至所有数字遍历完为止。时间复杂度为O(n*log₂K)，空间复杂度为K。
 `C++代码实现如下`

```cpp
class Solution {
public:
    void adjust(vector<int> &arr, int index, int len) {
        int left = 2 * index + 1;
        int right = 2 * index + 2;
        int max_index = index;
        if (left < len && arr[left] > arr[max_index]) max_index = left;
        if (right < len && arr[right] > arr[max_index]) max_index = right;
        if (max_index != index) {
            swap(arr[max_index], arr[index]);
            adjust(arr, max_index, len);
        }
    } 

    void heapSort(vector<int> &arr, int len) {
        for (int i = len / 2 - 1; i >= 0; i--) {
            adjust(arr, i, len);
        }
    //    for (int i = len - 1; i >= 1; i--) {
    //        swap(arr[0], arr[i]);
    //        adjust(arr, 0, i);
    //    }
    }

    vector<int> GetLeastNumbers_Solution(vector<int> input, int k) {
        if (k <= 0 || k > input.size()) {
            vector<int> nullVec;
            return nullVec;
        }
        // 因为要取最小的k个数，所以取前k个数字构建一个最大堆
        // 相反，如果是取最大的k个数，则构建一个最小堆
        vector<int> sortedArray(input.begin(), input.begin() + k);
        heapSort(sortedArray, k);
        // 将后面的数字与这个构建好的二叉堆进行比较 
        for (int i = k; i < input.size(); i++) {
            if (input[i] < sortedArray[0]) {
                sortedArray[0] = input[i];
                adjust(sortedArray, 0, k);
            }
        }
        for (int i = k - 1; i >= 1; i--) {
            swap(sortedArray[0], sortedArray[i]);
            adjust(sortedArray, 0, i);
        }
        return sortedArray;
    }
};
```
 
相似的TopK问题还有：

 
* 有10000000个记录，这些查询串的重复度比较高，如果除去重复后，不超过3000000个。一个查询串的重复度越高，说明查询它的用户越多，也就是越热门。请统计最热门的10个查询串，要求使用的内存不能超过1GB。 
* 有10个文件，每个文件1GB，每个文件的每一行存放的都是用户的query，每个文件的query都可能重复。按照query的频度排序。 
* 有一个1GB大小的文件，里面的每一行是一个词，词的大小不超过16个字节，内存限制大小是1MB。返回频数最高的100个词。 
* 提取某日访问网站次数最多的那个IP。 
* 10亿个整数找出重复次数最多的100个整数。 
* 搜索的输入信息是一个字符串，统计300万条输入信息中最热门的前10条，每次输入的一个字符串为不超过255B，内存使用只有1GB。 
* 有1000万个身份证号以及他们对应的数据，身份证号可能重复，找出出现次数最多的身份证号。 
* 等等... 
 
 
对于这类问题，比如上面第1个，可以先利用hash表将查询串存储并计数，然后再构建最小堆，将查询串的个数进行比较从而得到结果。核心思想都是一样的。
 
今天就先写到这里吧，困了睡觉去 Orz


[3]: https://rebornc.github.io/2018/11/15/%E5%A0%86%E6%8E%92%E5%BA%8F%E4%B8%8E%E6%B5%B7%E9%87%8FTopK%E9%97%AE%E9%A2%98/
[0]: ../img/NJfIVrm.png
[1]: ../img/zmEfQjN.png
[2]: ../img/jiyaErr.png