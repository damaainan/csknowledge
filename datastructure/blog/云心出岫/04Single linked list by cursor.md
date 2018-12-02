## Single linked list by cursor

来源：[https://www.cnblogs.com/hongshijie/p/7748098.html](https://www.cnblogs.com/hongshijie/p/7748098.html)

2017-10-28 21:56




有了指针实现看似已经足够了，那为什么还要有另外的实现方式呢？原因是诸如BASIC和FORTRAN等许多语言都不支持指针，如果需要链表而又不能使用指针，那么就必须使用另外的实现方法。还有一个原因，是在ACM-ICPC，OI等竞赛中，比赛时间有限，用指针写起来太费事，而且数量不多的情况下，用数组实现的脸变运行速度会更快。还有一些人觉得用指针写起来不优雅。嗯，不管怎么说，多掌握一种写法还是有必要的，说不定面试就会被问到2333


下面我会先把游标实现的细节阐述清楚，然后给出一个例题，来辅助理解。


其实游标在操作起来和普通链表并无太大不同，实际上两者的实现代码（特别是链表中函数的实现）差别不大，游标实现的链表效率会高一些，因为他是通过数组存储数据的，所以读写速度都是O(1)的，非常快。但是它并不能像普通链表一样实现动态增长缩减，一旦定义了数组大小，则能存储的数据的个数便不可更改了，所以更适合事先知道最大数据个数的案例


根据之前的知识，在链表的指针实现种有两个重要的特点



* 数据存储在一组结构体中。每一个结构体包含有数据以及指向下一个结构体的指针。
* 一个新的结构体可以通过调用malloc而从系统全局内存(global memory)得到，并可以通过调用free而被释放。



那游标法就必须能够模拟这两条特性，因为这是指针的基础性质。满足条件1的逻辑方法是要有一个全局的结构体数组，这个数组用来干嘛的？应该很容易想到——一方面存数据，这是单元内容。另一方面，那么下标呢？对于这个数组的任何单元，它的下标用来代表一个地址。


先给出一些声明

```c
typedef int PtrToNode;       //因为现在不需要把数据和指针绑定，所以不再是结构体,而是数组下标

typedef PtrToNode List;

typedef PtrToNode Position;

#define SpaceSize 100

struct Node{

    int Element;

    Position Next;

};

 

struct Node CursorSpace[SpaceSize];
```


这里的声明和之前的指针实现保持结构上的一致，这样就会形成一种对称的美感～


现在我们必须模拟条件2，让CursorSpace数组中的单元代行malloc和free的职能。为此，我们将保留一个数组（也就是free list），用slot命名，还挺形象2333，这个表由不在任何表中的单元构成。而且用0号单元作为表头，下面给出它的初始配置

 
| Slot | Element  | Next |
| - | - | - |
| 0 |   | 1 |
| 1 |   | 2 |
| 2 |   | 3 |
| 3 |   | 4 |
| 4 |   | 5 |
| 5 |   | 6 |
| 6 |   | 7 |
| 7 |   | 8 |
| 8 |   | 9 |
| 9 |   | 10 |
| 10 |   | 0 |



 这是一个初始化的CursorSpace，对于Next，0值等价于一个NULL指针。上面的状态用链表形式表示为：

CursorSpace[0]—>CursorSpace[1]—>CursorSpace[2]—>CursorSpace[3]—>CursorSpace[4]—>CursorSpace[5]—>CursorSpace[6]—>CursorSpace[7]—>CursorSpace[8]—>CursorSpace[9]—>CursorSpace[10]—>NULL.


![][0]而这个Slot的值，其实就是CursorSpace这个结构体数组的下标！！理解这点，下面的分配和返还函数的细节就容易理解了。


我们做什么操作都离不开第一步——初始化，这很简单，一个循环就够了。

![][0]与此同时，为了执行malloc的功能，需要把表头后面的第一个元素从freelist中删除，为什么要这样做——因为这个slot数组模拟的是系统内存，你申请一块，他就少一块。为了执行 free的功能，我们把要删除的单元放在freelist的前面，下面给出内存分配和返还的游标实现。如果没有可用空间，我们就让P=0，它表明没有空间可用，并且也可以使分配函数的第二行称为空操作。


先说初始化一个游标空间 

```c
void Initial(){

    int i;

    for (i=0; i<SpaceSize-1; i++)   //遍历每一个单元

        CursorSpace[i].Next=i+1;    //依次对next升序编号

    CursorSpace[0].Element=0;       //初始元素置空

    CursorSpace[SpaceSize-1].Next=0;//把最后一个单元的next设为0，就类似指针链表的尾指针是NULL

}
```


![][0]下面这两个是重中之重，各位要看仔细了，这两个基础操作理解透彻了，后面的都是小菜一碟。

```c
static Position CursorAlloc(){

    Position P;

    P=CursorSpace[0].Next;     //先从next的第0个单元获取一个数，这个数是第P个单元的地址

    CursorSpace[0].Next=CursorSpace[P].Next; //cursor 0后面本来接的是cursor P，但现在第P个单元被申请走了，所以顺接到P后面的位置。

    return P;

}
```


这里的CursorSpace[0]仅代表一般意义上的“第一个元素”，未必是真正的下标0.这几句代码不太好理解，我一开始学的时候费了不少劲去弄懂，后来总结出一个状态转换的示意图，能很清晰地解释这个函数的运行过程：

![][3]

 因为malloc的时候要将第一个元素（表头之后的第一个）从freelist中删除。


 释放内存：

```c
void CursorFree(Position P){
    CursorSpace[P].Next=CursorSpace[0].Next;//cursor P后面接上原本是cursor 0所指的下一个
    CursorSpace[0].Next=P; //cursor 0后面接上被删除的P，相当于返还给操作系统。
}
```


这两句代码的顺序不能反过来，不然的话，cursor 0里面存的Next值就会改变，顺序就乱了。不过——我们思考这个free过程的时候最好从下往上看，因为要返还P这个单元，所以从逻辑上，表头的下一位记录P，然后P记录“原本是表头的下一位”那个单元的序号——也就是下标。记住！是从逻辑上，不是从代码细节上。实际写的时候要考虑边边角角，调整Next值的顺序一定要小心，就像用指针删除链表时的顺序问题（回想一下）。  

 这个的运行过程如下：

![][4]


因为free后要把该单元放在freelist的前端，放回去。

有没有发现，这两个函数的操作是完全对称的！多么和谐的美感啊，无论顺序和具体的步骤，他们都是对称的，所以这个细节也会有利于我们去理解内存的分配和返还机制。这或许对我们理解后续课程有帮助。


有了这些，链表的游标实现就简单了。为了前后一致我们将用一个头节点实现我们的链表。为了方便从整体架构上理解游标链表，给出一个例子：
| Slot | Element | Next |
| - | - | - |
| 0 | - | 6 |
| 1 | B | 9 |
| 2 | F | 0 |
| 3 | Header | 7 |
| 4 | -  | 0 |
| 5 | Header | 10 |
| 6 | - | 4 |
| 7 | C | 8 |
| 8 | D | 2 |
| 9 | E | 0 |
| 10 | A | 1 |



假设L=5，M=3，那么L表示链表a->b->e->NULL，M表示链表c->d->f->NULL。为了写出用游标实现链表的这些函数，必须传递和返回与指针实现时相同的参数。


这节因为有了之前的基础，注释就不写那么冗长了。


判断是否为空表，也就是一个元素都没有的表。

```c
 int Isempty(List L) {

      return CursorSpace[L].Next==0;

}
```


判断是否为末尾

```c
int IsLast(Position P) {

    return CursorSpace[P].Next==0;

}
```


虽然细节和判空相同，但是用作接口由于实际功能有细微差别，还是要区分开写的。


查找是这样的

```c
Position Find(int X,List L) {

    Position P;

    P=CursorSpace[L].Next;

    while(P && CursorSpace[L].Element!=X)   //当后续的表还存在，并且还未找到给定的X时

        P=CursorSpace[P].Next;     //向后迭代，并逐个比对元素

    return P;       //返回X在L中的位置，当没有找到时，返回0

}
```


我们再说删除：

和之前一样，删除要先找到前一个元素

```c
Position FindPrevious(int X,List L){

    Position P;

    P=L;

    while (P && CursorSpace[CursorSpace[P].Next].Element!=X) { //P没有走到末尾，同时还没找到给定的X时

      P=CursorSpace[P].Next; //P向后走

 }//走到这一步时，说明要么没找到，P=NULL（结尾处），要么找到了，P=前驱的位置

    return P;

}
```


接下来就要删除了，有了前面的基础，就容易理解了。

```c
void Delete(int X , List L){

    Position P,TempCell;

    P=FindPrevious(X, L);

    if (!IsLast(P)) {

        TempCell=CursorSpace[P].Next;

        CursorSpace[P].Next=CursorSpace[TempCell].Next;//相当于P->Next=P->Next->Next

        CursorFree(TempCell);

    }

}
```


再说插入,顺次向后添加一个单元比较自然，就先说向后插入的实现：

```c
Position Insert(int X,Position P){//P是插入前的末尾节点
    
    Position TempCell;
    
    TempCell=CursorAlloc(); //申请一块新内存
    
    if(!TempCell)
        printf("Out of space!");
    
    CursorSpace[TempCell].value=X;
    
   CursorSpace[TempCell].Next=0;
    
    CursorSpace[P].Next=TempCell;
    
    return TempCell;
}
```


哦对了，应该有不少人对之前的“freelist”感到疑惑吧hhhhh   它从字面上看表示了一种有趣的数据结构，从freelist删除的单元是刚刚由free放在那里的单元。因此，最后被放在freelist的单元是最先被拿走的单元。有一种数据结构也具有这种性质，叫做栈（stack），它是下一节要讨论的内容。


下面给出一个有趣的题目，emmmm有兴趣的or有能力的可以继续往下看——没人希望自己很弱吧，所以都接着往下看吧哈哈哈


破损的键盘（又名：悲剧文本），Uva OJ  11988

        你有一个破损的键盘，键盘上的所有键都可以正常工作，但有时Home或者End键会自动按下。你并不知道键盘存在这一问题，于是专心地打稿子，甚至连显示器都没打开。当你打开显示器后，展现在你面前的是一段悲剧的文本。你的任务是打开显示器之前，计算出这段悲剧文本。

       输入包含多组数据，每组数据占一行，包含不超过100000个字母、下划线、字符“[”或者“]”。其中字符“[”表示Home键，“]”表示End键。输入结束标志为文件结束符（EOF）输入文件不超过5MB，对于每组数据，输出一行，即屏幕上的悲剧文本。

Sample:


Input

This_is_a_[Beiju]_text [[]][]Happy_Birthday_to_Tsinghua_University


Output

BeijuThis_is_a__text Happy_Birthday_to_Tsinghua_University


最简单的想法是用一个数组保存这段文本，然后用一个变量pos保存光标的位置。这样的话，输入一个字符相当于在数组中插入一个字符……那这就很尴尬了，每插入一个字符，需要把当前位置的所有元素向右移动，还要考虑是否存在溢出的问题。很不方便而且时间开销巨大，这样的代码妥妥TLE。


解决方案是用链表，每输入一个字符就把它存起来。假设输入的字符串是s[1~n],则可以用next[i]表示在当前显示器中s[i]右边的字符编号——也就是对应的下标。方便起见，假设字符串s的最前面有一个虚拟的s[0]，则next[0]就表示显示器中最左边的第一个有效字符。再用一个变量cur表示光标位置：当前光标位于s[cur]的右边。cur=0说明光标在虚拟字符的右边，也就是显示器的最左边、刚开始要输入的那个位置。


为了移动光标，还需要用一个变量last表示显示器的最后一个字符是s[last]。现在思路大概理顺了，实现如下：

```c
#include <stdio.h>

#include <string.h>

const int maxn = 100000+5;

int last,cur,next[maxn],i;

char s[maxn];

int main(){

    while (scanf("%s",s+1)==1) {       //每次输入一个字符，存储地址向后偏移一位

        int n=strlen(s+1);          //n为当前字符串长度

        last=cur=0;

        next[0]=0;

        for (i=0; i<n+1; i++) {     //遍历每一个字符

            char ch=s[i];

            if(ch=='[') cur=0;      //遇到Home键就把光标移到最左边

            else if(ch==']') cur=last;//遇到End键就把光标移到最后的位置

            else{               //如果是文本

                next[i]=next[cur];

                next[cur]=i;

                if(cur==last)last=i;    //更新最后一个字符的编号

                cur=i;          //移动光标

            }

        }

        for (i=next[0];i!=0 ;i=next[i])   //对于建立好的链表，通过next数组遍历整个处理后的字符串

            printf("%c",s[i]);

        printf("\n");

    }

    return 0;

}
```


有哪里感到疑惑的就直接写在评论里吧，我会积极参与讨论的2333


下一篇写栈。


[0]: ./img/1035672895.png
[1]: ./img/1035672895.png
[2]: ./img/1035672895.png
[3]: ./img/331460619.png
[4]: ./img/922750934.png