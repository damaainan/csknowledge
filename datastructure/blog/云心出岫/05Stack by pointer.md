## Stack by pointer

来源：[https://www.cnblogs.com/hongshijie/p/7749669.html](https://www.cnblogs.com/hongshijie/p/7749669.html)

2017-10-29 11:39




前言：因为栈的很多操作是基于表的，所以这篇文章里的例程就不再大面积地写注释了，有不理解的地方可以翻看之前的链表笔记，或者直接写在评论区。


咳咳，说到这个栈，很多人乍听之下感觉很陌生、卧槽这是什么玩意。其实生活中随处可见，在一些小餐馆，客人不多的时候，椅子都是放成一摞的，一个叠一个。有客人来了就搬下来一把——肯定是搬最上面那一把，没人会从下面搬凳子吧2333  用完之后从上面再叠放上去，这是一个例子。刷知乎或者看网页的时候需要返回，我们按一下，就跳转到上一个页面了，那这是怎么做的呢？我们用直觉考虑一下，应该是浏览器把每一次操作的结果都保存下来，要返回的时候，就把当前层移除——移除的是最新的那一层。如果有新的跳转或者其他操作，就依次叠放到之前最新的上面。类似的，主流的文本编辑器也都支持撤销操作，我们的编辑操作被记录在一个栈中，一旦出现误操作，只需要按下撤销（一般是control+z）按钮，就可以取消最近的一次操作，并回到之前的状态。


而我们在写程序的时候会涉及到不同函数之间的相互调用，被调函数（callee）执行完后，把权限返还给主调函数（caller）这也用到了“栈”这种结构。许多程序语言本身就是建立于栈结构上的，比如Postscript和Java运行环境都是基于栈结构的虚拟机。


我们再联系上一节提到的那个“free list”，可以很明显的感到一个性质：这些行为的次序，都是增加的时候从最新的那一端增加，要移除的时候，往往是把“最后移动的元素”首先给拿出去。这就叫后进先出（Last   In  First Out）。而且相对于一般的序列结构，它的数据操作范围都仅限于整个表的末端。


对栈的基本操作有Push（进栈or压栈）和Pop（出栈），前者相当于插入，后者则是删除最后的元素。

![][0]

这是一个进行若干操作后的抽象栈，一般的模型是存在某个元素位于栈顶，而这是唯一的可见元素。不过这样说可能有点不好理解，那比如说一摞椅子。

![][1]


这就可以视作一个栈，为了维持这一放置形式，对这个栈的操作只能在顶部实施：新的椅子只能叠放到最顶端；反过来只有最顶端的椅子才能被取走。因此和这个实例相比照，栈中可操作的一端被叫做栈顶，而另外一个无法操作的盲端被称为栈底。

![][2]


就像这样。


因为栈是一个表，所以任何实现表的方法都能实现栈，这次就说一下好理解的的指针实现吧，比数组貌似好理解一些。用单链表实现的话，我们要通过在表顶端插入来实现Push，通过删除顶端元素实现Pop。而Top操作仅仅是返回顶端元素的值。不过在很多时候都是把Pop和Top合二为一的。本来可以用前一节的代码段，不过为了清楚起见，还是从头开始写吧


和之前一样，先给出一些前提性声明，实现栈同样要用到表头。

```c
struct Node;
typedef struct Node *PtrToNode;
typedef PtrToNode Stack;
struct Node{
    int Element;
    PtrToNode Next;
};
```


测试空栈与测试空表的方式一样。

```c
int IsEmpty(Stack i){
    return i->Next==NULL;
}
```


创建一个栈的话也很简单，只需要建立一个头结点就好。

```c
Stack Creat(){
    Stack S;
    S=(Stack)malloc(sizeof(struct Node));
    if(S==NULL)
        printf("out of space!!!");
    else
        S->Next=NULL;
    MakeEmpty(S);
    return S;
}
```


现在是中场问答时间，我们创建一个栈之后，里面会有什么？就是仅仅申请一块内存，然后什么也不做。里面会有——


垃圾数据，对吧。这是上学期的知识，声明一个变量后，系统会随机填充一段数据，我们不知道里面是什么，但是，我们能确定一点——这东西十有八九不是我们所期望的，因此我们需要把它扔掉。这就是MakeEmpty的意义。            

```c
void MakeEmpty(Stack S){
    if(S==NULL)
       printf("Must creat a stack first");
    else
        while(!IsEmpty(S))
            Pop(S);
}
```


关于这个Pop函数是什么，emmm接着往后看吧，你看这涉及到了函数间的相互调用，就是运用了栈的特性。还有一个好玩的事实，就是——我们在写一个栈的时候已经用到了栈的环境，用栈来写栈，这就陷入递归了233  从这个角度再次理解一下递归吧，毕竟理解递归是筛选合格程序员的一道门槛。


创建之后就该讨论对栈的各项操作了，主要就三个：出栈，入栈和取栈顶元素。先说入，有入才有出嘛，Push是作为向链表前端进行插入而实现的，其中表的前端作为栈顶。所以实现起来也很顺畅

```c
void Push(int X,Stack S){
    Stack TemCell;
    TemCell=(Stack)malloc(sizeof(S));
    if(S==NULL) printf("Out of space!!!");
    else{
        TemCell->Element=X;
        TemCell->Next=S->Next;
        S->Next=TemCell;
    }
}
```


![][3]这里提一句，S是表头，里面什么都不存，而第一个有效元素是S->Next，原因是S仅作为一个地址说明，告诉我们第一个有效元素“在哪”，我们不可能指望S存数据，不然的话，谁来告诉我们这个栈的顶在哪呢？这很重要，理解这个观点是看懂下面所有函数的基础，是重中之重。


接着说取栈顶元素，Top的实施是通过考察整个表在第一个位置上的元素而完成的，也就是把Head的元素返回

```c
int Top(Stack S){
    if(!IsEmpty(S))
        return S->Next->Element;
    printf("Empty stack");
    return 0;
}
```


最后，Pop是通过删除表的前端元素而实现的。

```c
void Pop(Stack S) {
    PtrToNode FirstNode;
    if(IsEmpty(S))
        printf("Empty stack");
    else{
        FirstNode=S->Next;
        S->Next=S->Next->Next;
        free(FirstNode);
    }
}
```


到这里，已经很清楚了，所有的操作均花费常数时间，因为这些函数没有任何地方涉及到栈的size，更不用说依赖于size的循环了。但是这种实现方法的缺点在于对malloc和free的调用开销是昂贵的。避免这个缺点的方法就是用数组实现，具体的实现方法以后会说到，在后面几篇文章里会详细讨论栈的应用和数组实现。         


下面写了一个测试程序，比较简陋，你们不要嫌弃Orz

![][4]
![][5]

```c
#include <stdio.h>
#include <stdlib.h>
struct Node;
typedef struct Node *PtrToNode;
typedef PtrToNode Stack;
struct Node{
    int Element;
    PtrToNode Next;
};
//函数签名
int IsEmpty(Stack i);
void Push(int X,Stack S);
int Top(Stack S);
void Pop(Stack S);
void MakeEmpty(Stack S);
Stack Creat();
void Traverse(Stack S);

//入口
int main(){
    Stack S;
    S=Creat();
    int n;
    printf("Please input all elements to complete a stack,finished by 0\n");
    while (scanf("%d",&n)&&n)
        Push(n, S);
    Traverse(S);
    printf("Input imperative(1:top\t2:remove\t3:add),0 to quit\n");
    while (scanf("%d",&n)&&n) {
        if (n==1)
            printf("Top element:%d\n",Top(S));
        else if(n==2){
            Pop(S);
            Traverse(S);
        }
        else if(n==3){
            printf("number:");
            scanf("%d",&n);
            Push(n, S);
            Traverse(S);
        }
        else
            printf("Input again,it is invalid");
    }
}

//接口内部一览
int IsEmpty(Stack i){
    return i->Next==NULL;
}
void Push(int X,Stack S){
    Stack TemCell;
    TemCell=(Stack)malloc(sizeof(S));
    if(S==NULL) printf("Out of space!!!");
    else{
        TemCell->Element=X;
        TemCell->Next=S->Next;
        S->Next=TemCell;
    }
}

int Top(Stack S){
    if(!IsEmpty(S))
        return S->Next->Element;
    printf("Empty stack");
    return 0;
}

void Pop(Stack S) {
    PtrToNode FirstNode;
    if(IsEmpty(S))
        printf("Empty stack");
    else{
        FirstNode=S->Next;
        S->Next=S->Next->Next;
        free(FirstNode);
    }
}
void MakeEmpty(Stack S){
    if(S==NULL)
        printf("Must creat a stack first");
    else
        while(!IsEmpty(S))
            Pop(S);
}
Stack Creat(){
    Stack S;
    S=(Stack)malloc(sizeof(struct Node));
    if(S==NULL)
        printf("out of space!!!");
    else
        S->Next=NULL;
    MakeEmpty(S);
    return S;
}
void Traverse(Stack S){
    for (; S->Next; S=S->Next) {
        printf("%d->",S->Next->Element);
    }
    printf("NULL\n");
}
```


完整代码（已测试） 


然后自己调试一下吧，这会加深你对栈的理解的，祝食用愉快～


[0]: ./img/2114727506.png
[1]: ./img/1478033998.png
[2]: ./img/1100456934.png
[3]: ./img/1402381992.png
[4]: https://images.cnblogs.com/OutliningIndicators/ContractedBlock.gif
[5]: https://images.cnblogs.com/OutliningIndicators/ExpandedBlockStart.gif