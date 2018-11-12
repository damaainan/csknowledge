## Javascript中的尾递归及其优化

时间：2018年11月01日

来源：<https://juejin.im/post/5bdab8546fb9a0222205d991>

在平时的代码里，递归是很常见的，然而它可能会带来的调用栈溢出问题有时也令人头疼：
[][5]
我们知道， js 引擎（包括大部分语言）对于函数调用栈的大小是有限制的，如下图（虽然都是很老的浏览器，但还是有参考价值）：
[][6]
为了解决递归时调用栈溢出的问题，除了把递归函数改为迭代的形式外，改为`尾递归`的形式也可以解决（虽然目前很多浏览器没有对尾递归（尾调用）做优化，依然会导致栈溢出，但了解尾递归的优化方式还是有价值的。而且我们可以通过一个统一的工具函数把尾递归转化为不会溢出的形式，这些下文会一一展开）。
在讨论`尾递归`之前，我们先了解一下`尾调用`，以及 js 引擎如何对其进行优化。
## 尾调用

当函数`a`的最后一个动作是调用函数`b`时，那么对函数`b`的调用形式就是`尾调用`。比如下面的代码里对`fn1`的调用就是尾调用：

```js
const fn1 = (a) => {
  let b = a + 1;
  return b;
}

const fn2 = (x) => {
  let y = x + 1;
  return fn1(y);        // line A
}

const result = fn2(1);  // line B
```

我们知道，在代码执行时，会产生一个调用栈，调用某个函数时会将其压入栈，当它 return 后就会出栈，下图是对于这段代码简易示例的调用栈（没有对`尾调用`做优化）：
[][7]
首先`fn2`被压入栈，`x`、`y`依次被创建并赋值，栈内也会记录相应的信息，同时也记录了该函数被调用的地方，这样在函数 return 后就能知道结果应该返回到哪里。然后`fn1`入栈，当它运行结束后就可以出栈，之后`fn2`也得到了想要的结果，返回结果后也出栈，此段代码运行结束。
仔细看一下以上过程，你有没有觉得第二第三步中`fn2`的存在有些多余？它内部的一切计算都已经完成了，此时它在栈内的唯一作用就是记录最后结果应该返回到哪一行。因而可以有如下的优化：
[][8]
在第二步调用`fn1`时，`fn2`即可出栈，并把`line B`信息给`fn1`，然后将`fn1`入栈，最后把`fn1`的结果返回到`line B`即可，这样就减小了调用栈的大小。
## 辨别是否是尾调用

```js
const a = () => {
  b();
}
```


这里`b`的调用不是尾调用，因为函数`a`在调用`b`后还隐式地执行了一段`return undefined`，如下面这段代码：

```js
const a = () => {
  b();
  return undefined;
}
```

如果我们把它当做`尾调用`并按照上面的方法优化的话，就得不到函数`a`正确的返回结果了。

```js
const a = () => b() || c();
const a1 = () => b() && c();
```


这里`a`和`a1`中的`b`都不是`尾调用`，因为在它调用之后还有判断的动作以及可能的对于`c`的调用，而`c`都是`尾调用`。

```js
const a = () => {
  let result = b();
  return result;
}
```


对于这段代码，有文章指出`b`并不是`尾调用`，即便它与`const a = () => b()`是等价的，而后者显然是尾调用。这就涉及到定义的问题了，我觉得不必过于纠结，`尾调用`的真正目的是为了进行优化，防止栈溢出，我测试了下支持`尾调用`的 safari 浏览器，在严格模式下用类似的代码执行一段递归函数，结果是不会导致栈溢出，所以 safari 对这种形式的代码做了优化。
## 尾递归

现在就轮到本篇文章的主角——`尾递归`了，它其实只是`尾调用`的一种特殊情况，即每次递归调用都是`尾调用`，看一下下面这段简单的递归代码：

```js
const sum = (n) => {
  if (n <= 1) return n;
  return n + sum(n-1)
}
```

就是计算从1到n的整数的和，显然这段代码并不是`尾递归`，因为`sum(n-1)`调用后还需要一步计算的过程，所以当n较大时就会导致栈溢出。我们可以把这段代码改为`尾递归`的形式：

```js
const sum = (n, prevSum = 0) => {
  if (n <= 1) return n + prevSum;
  return sum(n-1, n + prevSum)
}
```

这样就是`尾递归`了，这段代码在 safari 里以严格模式运行时，不会出现栈溢出错误，因为它对`尾调用`做了优化。那有多少浏览器会做优化呢？其实在[ es6 的规范里][9]，就已经定义了对`尾调用`的优化，不过目前浏览器对其支持情况很不好:
[][10]
具体见[这里][11]
即便将来大部分浏览器都支持`尾调用`优化了，按照 es6 的规范，也只会在严格模式下触发，这明显会很不方便。但我们可以通过一个统一的方法对`尾递归`函数进行处理，让其不再导致栈溢出。

## Trampoline

[Trampoline][12]是对`尾递归`函数进行处理的一种技巧。我们需要先把上面的`sum`函数改造一下，再由`trampoline`函数处理即可：

```js
const sum0 = (n, prevSum = 0) => {
  if (n <= 1) return n + prevSum;
  return () => sum0(n-1, n + prevSum)
}
const trampoline = f => (...args) => {
  let result = f(...args);
  while (typeof result === 'function') {
    result = result();
  }
  return result;
}
const sum = trampoline(sum0);

console.log(sum(1000000)); // 不会栈溢出
```

可以看到，这里实际上就是把原本的递归改为了迭代，这样就不会有栈溢出的问题啦。

当然，如果一个方法可以写成`尾递归`的形式，那它肯定也能被写成迭代的形式（其实理论上所有递归都能被写成迭代的形式，不过有些用迭代实现起来会很复杂），但有些场景下使用递归可能会更加直观，如果它能被转为`尾递归`，你就可以直接用`trampoline`函数进行处理，或者把它改写成迭代的方法（或是在特殊场景下，在支持`尾调用`优化的浏览器里以严格模式运行）

### 参考：

[blog.logrocket.com/using-tramp…][13]  
[2ality.com/2015/06/tai…][14]  
[www.zhihu.com/question/30…][15]  

## ---------更新---------

咦，不是应该结束了吗，怎么还有内容！

以下内容只是奇技淫巧，不一定能运用到实践中，仅供娱乐或开拓思维（下面不是本文的正经内容，所以画风可能不一样，只是随意写写~）
## 奇技淫巧

这篇文章发到知乎后，评论区有人说用settimeout也可以，我想了下，哎？好像是可以，我们把递归调用放到settimeout中异步执行，每次递归执行结束后再把下一次递归放到settimeout里。这样函数执行一次后就直接返回了，它会退出调用栈，下一次递归调用函数会被settimeout推入回调队列里，在js的回调队列里永远最多都只有一个函数待执行，函数调用栈里当然也永远最多只有一个函数~（如果不考虑其它函数）

还是以前面的sum函数举例，显然我们不能同步地得到最终结果，可以通过一个回调函数去获取最终的值。于是我欢快地写起了下面的代码：

```js
sum2 = (num, callback, sum = 0) => {
  if (num < 1) {
    callback(sum);
    return;
  }

  setTimeout(() => sum2(num-1, callback, sum + num), 0);
}

sum2(1000, v => console.log(v));
```

运行！

怎么这么慢？

因为settimeout有延时啊，最小4ms，所以每一次递归都被settimeout延迟了一小会，性能大打折扣！虽然只是奇技淫巧，但这么差的性能还是让人不爽，必须优化！(* ￣︿￣)

重新想一下，每次settimeout都可以理解为把当前调用栈清空，然后再执行settimeout中的函数。那么我们不就可以把同步递归调用与settimeout结合！每递归个5000层，settimeout一次！（5000只是个比较保险的数字，可以针对不同浏览器的上限做不同处理）

```js
sum3 = (num, callback, sum = 0, batchLeft = 5000) => {
  if (num < 1) {
    callback(sum);
    return;
  }
  batchLeft--;
  if (batchLeft > 0) 
  sum3(num-1, callback, sum + num, batchLeft)
  else setTimeout(() => sum3(num-1, callback, sum + num, 5000), 0);}

sum3(30000, v => console.log(v));
```

（如果真的要实际使用的话，最好对这个函数封装一下，不要把sum和batchLeft这两个变量暴露出来）

这样我们就用js实现了永不会导致栈溢出的递归函数！不需要trampoline！不需要改迭代！这是真·递归！（即便是settimeout中的调用也是递归，只不过延后执行了）。只不过写法很啰嗦，还把原本可以同步执行的函数改成了麻烦的异步。


其实我们再回头想一下，这个settimeout调用形式的本身就是一种尾递归，我们是用settimeout把递归函数延迟到最后执行了，而且都延迟到上一个函数执行结束且出栈了，可以理解为我们利用了js异步本身的特性，使js引擎做了一次非常规的“尾调用优化”。是不是挺有意思 σ`∀´)σ


[5]: https://link.juejin.im?target=https%3A%2F%2Fygyooo.github.io%2F2018%2F10%2F17%2FJavascript%25E7%259A%2584%25E5%25B0%25BE%25E9%2580%2592%25E5%25BD%2592%25E5%258F%258A%25E5%2585%25B6%25E4%25BC%2598%25E5%258C%2596%2Foverflow.png
[6]: https://link.juejin.im?target=https%3A%2F%2Fygyooo.github.io%2F2018%2F10%2F17%2FJavascript%25E7%259A%2584%25E5%25B0%25BE%25E9%2580%2592%25E5%25BD%2592%25E5%258F%258A%25E5%2585%25B6%25E4%25BC%2598%25E5%258C%2596%2Fbrowsers-overflow.png
[7]: https://link.juejin.im?target=https%3A%2F%2Fygyooo.github.io%2F2018%2F10%2F17%2FJavascript%25E7%259A%2584%25E5%25B0%25BE%25E9%2580%2592%25E5%25BD%2592%25E5%258F%258A%25E5%2585%25B6%25E4%25BC%2598%25E5%258C%2596%2FcallWithoutOpt.png
[8]: https://link.juejin.im?target=https%3A%2F%2Fygyooo.github.io%2F2018%2F10%2F17%2FJavascript%25E7%259A%2584%25E5%25B0%25BE%25E9%2580%2592%25E5%25BD%2592%25E5%258F%258A%25E5%2585%25B6%25E4%25BC%2598%25E5%258C%2596%2FcallWithOpt.png
[9]: https://link.juejin.im?target=http%3A%2F%2Fwww.ecma-international.org%2Fecma-262%2F6.0%2F%23sec-tail-position-calls
[10]: https://link.juejin.im?target=https%3A%2F%2Fygyooo.github.io%2F2018%2F10%2F17%2FJavascript%25E7%259A%2584%25E5%25B0%25BE%25E9%2580%2592%25E5%25BD%2592%25E5%258F%258A%25E5%2585%25B6%25E4%25BC%2598%25E5%258C%2596%2FbrowserSupport.png
[11]: https://link.juejin.im?target=http%3A%2F%2Fkangax.github.io%2Fcompat-table%2Fes6%2F%23test-proper_tail_calls_%2528tail_call_optimisation%2529
[12]: https://link.juejin.im?target=https%3A%2F%2Fen.wikipedia.org%2Fwiki%2FTail_call%23Through_trampolining
[13]: https://link.juejin.im?target=https%3A%2F%2Fblog.logrocket.com%2Fusing-trampolines-to-manage-large-recursive-loops-in-javascript-d8c9db095ae3
[14]: https://link.juejin.im?target=http%3A%2F%2F2ality.com%2F2015%2F06%2Ftail-call-optimization.html
[15]: https://link.juejin.im?target=https%3A%2F%2Fwww.zhihu.com%2Fquestion%2F30078697%2Fanswer%2F146047599