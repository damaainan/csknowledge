## 来一场轰轰烈烈的HTTP协议扫盲革命

来源：[https://segmentfault.com/a/1190000012789390](https://segmentfault.com/a/1190000012789390)


## 前言

最近一段时间在空闲之余拜读了一下《图解HTTP协议》，收货颇丰。以前不懂的地方在读完这本书之后，豁然开朗。于是花了一些时间总结一下，其中我也查阅了一些其他资料来补充进去，希望这篇文章可以给大家带来帮助。如果各位觉得我写的还不错的话，还望大家多多收藏点在支持哦！

## 网络基础

## TCP/IP分层

TCP/IP体系有人把它分为四层也有人把它分为五层，不同书有着不同的分法。五层和四层的区别就在于五层的数据链路层和物理层对应着四层的网络接口层。二者都对，不必要纠结，了解一下即可。如果按照协议划分的话，物理层是没有必要单独划分出来的，毕竟物理层是没有协议的。本篇文章采用的是四层结构。

| 名称 | 作用 |
| - | - |
| 应用层 | HTTP、FTP、DNS等协议就位于该层上，用于向用户提供应用服务. |
| 传输层 | 该层最主要的协议就是TCP和UDP协议，为应用层实体提供端到端的通信功能。 |
| 网络层 | 该层的核心就是IP协议，规定了通过什么样子的路径到达对方计算机，并把数据包传送给对方。ARP、RARP协议也位于该层。 |
| 网络接口层 | 负责接收IP数据包并通过网络发送，或者从网络上接收物理帧，抽出IP数据包，交给网络层。该层常见的协议就是Ethernet 802.3、Token Ring 802.5。 |

## TCP/IP通信传输流

![][0]

利用TCP/IP协议族进行通信时，会通过分层顺序与对方进行通信。发送端从应用层往下走，接收端则从下往应用层向上走。发送端在层与层之间传输数据时，每经过一层时就会打上一个该层对应的首部信息。反之，接收端在层与层传输数据时，每经过一层时会把对应的首部信息剔除。

## TCP协议的三次握手

TCP协议位于传输层，其作用就是提高可靠的字节流服务。为了可以准确无误地将数据送达到目标处，TCP协议采用了三次握手策略来保证其可靠性。其整体流程如下：

![][1]

发送端首先会发送一个带SYN标志的数据包给对方。接收端收到后，回传一个带有SYN/ACK标志的数据包以示传达确认信息。最后，发送端再回传一个带ACK标志的数据包，代表“握手”结束。对于TCP协议的三次握手，相信有不少小伙伴们有着这样一个疑问。为什么一定要三次握手才可以保证其可靠性，一次两次不行吗？针对这个问题，我们举个简单的例子来说明一下。  
 **`场景描述：`** 有一对好基友在高中毕业之后就失去了联系，十年过去了，基友甲通过某些方式弄到了基友乙的联系方式，准备打个电话给基友乙，叙叙旧，重燃当年捡肥皂的基情。  
 **`场景分析：`** 场景中基友甲就是客户端，而基友乙就是服务端。现在这么一个问题，怎样保证基友甲和基友乙可以顺利地重拾当年的肥皂情？从基友甲的角度来说，我得确定我电话对面的是基友乙。从基友乙的角度来说，我得确定我电话对面的是基友甲。只有双方都能确定电话那一头就是对方，那么这个肥皂情才能重拾。接下来我们以这个维度去分析这三次握手。  
 **`第一次握手：`** 基友甲拨通电话，说了一句“喂，请问是基友乙吗？”（这就好比于客户端发送一个带SYN标志的数据包给服务端）。这句话发出，基友甲还不能确定对方就是基友乙，同样基友乙也不能确定对方是基友甲。   
 **`第二次握手：`** 基友乙接起电话，说了一句“我是基友乙，请问你是哪位？”（这就好比于服务端回传一个带有SYN/ACK标志的数据包，确认信息）。这句话一发出，基友甲就可以确定了对方是基友乙，但此时基友乙还不能确定对方是基友甲。   
 **`第三次握手：`** 基友甲确定了电话另一头是基友乙，非常兴奋，回了一句“你不认识我了吗？我是基友甲啊”（这就好比于发送端再回传一个带ACK标志的数据包）。这一句话一发出，那么基友乙便可根据这句话确认了电话那一头就是基友甲。此时，甲乙双方均可互相确认是对方。满足可以重拾肥皂情的条件，可以开始重拾基友情。

通过我们举例这个肥皂情来对三次握手的分析，我们发现只有三次握手才能保证通信的可靠性，两次是不可以的保证的。希望我这个举例可以加深大家对三次握手的理解。

## TCP的四次分手

上面我们讲了TCP的建立连接，连接之后必然就要遇到断开连接的问题。TCP是需要四次分手才能断开连接的。估计会有不少人不理解这个问题，一次客户端发送断开连接请求，还有一次服务端收到后回传一个确认信息。两次就可以断了，为什么还要四次？说到这里，我们要弄清楚一件事，TCP协议是采用的是全双工模式，什么叫全双工？全双工简单来说就是可以同时进行双向的数据传输。因此，客户端不仅仅是发送端，也可以是接收端。而服务端不仅仅是接收端，也可以是发送端。所以，我们把TCP建立的连接拆成两个单向连接。如下：
 **`1. 客户端是发送端，服务端是接收端。`** 
 **`2. 服务端是发送端，客户端是接收端。`** 

每个连接断开，我们需要一个发送端发送断开请求，接收端确认断开请求，两次分手。两个连接便是两次，总共就是四次分手。整个流程如下：

![][2]
 **`第一次分手：`** 客户端发送FIN报文段给服务端，用来断开客户端到服务端的连接。此时，客户端处于FIN_WAIT_1状态，即没有数据要发送了，在等待服务端的断开连接的确认。  
 **`第二次分手：`** 服务端接受到来自客户端的FIN报文段之后，回传了一个ACK报文段，同意了客户端的断开连接的请求。此时服务端进入CLOSE-WAIT状态，等待关闭客户端关闭连接。客户端在接收到这个来自服务端回传的ACK报文段时，客户端关闭向服务端发送数据的连接，客户端此时进行FIN_WAIT_2状态。  
 **`第三次分手：`** 断开客户端到服务端的连接之后，服务端发送一个FIN报文段给客户端，请求断开服务端到客户端的连接。此时服务端进入LAST_ACK状态，等待客户端的确认。   
 **`第四次分手：`** 客户端接收到来自服务端的FIN报文之后，回传一个ACK报文段，同意连接关闭请求。此时，客户端进入TIME_WAIT状态。服务端接收到来自客户端的ACK报文段之后，便关闭服务端向客户端发送数据的连接，服务端就进入了CLOSED状态。而客户端此时还在等待状态，那怎么让客户端也进入CLOSED状态呢？很简单，客户端在等待两个MSL时间之后，它会自动进入CLOSED状态。

## URI和URL的区别

一说到URL和URI，那真的就是老虎老鼠傻傻分不清楚，应该有不少小伙伴们会弄混它们。URL叫做统一资源定位符，而URI叫做统一资源标识符。虽然说这两者名字很相似，但是区分他们确实很简单。URL和URI我们可以类比成邮政编码和收件地址。URL的范围是要大于URI的。我们以淘宝的例子来说，[https://www.taobao.com/这个域...][17]，而每个商品的地址就是一个URI。

很多AJAX工具中，地址参数名称用的是url，例如jquery中的$.ajax方法中用的就是url，但是我们得弄清楚HTTP请求的地址是URI，而不是URL。## 简单的HTTP协议

## HTTP请求方法

| 名称 | 描述 | 最低支持协议版本 |
| - | - | - |
| GET | 请求服务器上的某一资源。 | 1.0 |
| POST | 向指定资源提交数据进行处理请求,数据包含在请求体中。 | 1.0 |
| HEAD | 用于确认URI的有效性及资源更新的日期时间，不返回报文主体，只返回报文文首部。 | 1.0 |
| PUT | 向用来传输文件，将文件内容放进报文主体中，保存到URI指定位置上。 | 1.1 |
| DELETE | 与PUT相反，请求URI删除指定资源。 | 1.1 |
| OPTIONS | 查询针对请求URI指定的资源支持的方法。 | 1.1 |
| TRACE | 用于追踪路径。发送请求时，首部字段Max-Forwards会指定一个数值，每经过一个服务器之后，该数值减1。当该数值为0时，停止传输，最后接收到的服务器响应。 | 1.1 |
| CONNECT | 用于在与代理服务器通信时建立隧道，实现用隧道协议进行TCP通信。 | 1.1 |

## HTTP协议1.1

### 持久连接

在1.0版本中，每进行一次HTTP通信就要断开一次TCP连接。TCP连接的新建成本很高，频繁地打开关闭会极大地增加开销，影响性能。于是在1.1版本中引入持久连接的方法。其特点就是任意一端没有明确提出断开连接，则保持TCP连接的状态。

### 管道机制

在1.1版本之前，在一个TCP连接中，客户端发送一个请求之后，必须等待服务器响应之后才能发送下一个请求，不能同时并行发送多个请求。1.1版本的管道机制解决这个问题，客户端不必等待服务端响应之后再发送请求了，可以并行发送多个请求。

### 增加HOST字段

1.0版本中，认为每台物理服务器对应唯一的IP地址。所以，在1.0版本中是没有主机名这个概念的。但随着Web技术的发展，一台物理服务器可以存在多个虚拟主机，他们共享同一个IP地址。为了解决这个问题，HOST字段应运而生。

### 分块传输编码

在1.1版本中，在一个TCP连接中存在多个响应。如何区分数据包对应哪个响应就成了问题。在1.1版本中就出现 **`Content-Length`** 这个字段，标记本次响应的数据长度。

例如： **`Content-Length: 2333`**      

 告诉客户端本次回应的数据长度为2333个字节,后面的字节就不属于这次响应在使用 **`Content-Length`** 的前提条件就是得知道整个数据长度才行。因此，产生出一个数据块的时候是不能立即传输给客户端，得等所有数据产生完毕才能发送。这中间的等待时间势必会影响性能。为解决这个弊端，1.1版本中提出了“分块传输编码”的解决方案，在响应头会有一个Transfer-Encoding这个字段，告诉客户端这次响应是由数量未定的数据块组成，每一个非空数据块都有一个16进制的数值来标记其长度。最后以长度为0的数据块来表示这次响应的数据发送完毕。

### 加入100状态码

随着Web应用的复杂度不断增加，往往服务端会加入权限控制。如果客户端发送一个HTTP请求，请求体重带着大量数据过来，结果服务端因为其没有权限给它打回了。那么这就造成无谓的开销。100状态码引入之后，客户端事先发送一个带部分请求体的HTTP请求，如果服务端的响应码为100，客户端会带上剩余的请求体再次发送HTTP请求。反之，则取消后续的带有剩下的请求体的HTTP请求。

## HTTP协议2.0

### 二进制分帧

在HTTP协议2.0中，应用层和传输层之间会多一个二进制分帧层。在二进制分帧层上，HTTP 2.0 会将所有传输的信息分割为更小的帧,并对它们采用二进制格式的编码 。之前HTTP1.x版本中的HTTP报文首部信息会被封装到Headers帧，而我们的HTTP报文主体则封装到Data帧里面。原先我们是以HTTP报文为单位传输的，现在HTTP报文被拆成了多个帧的形式，并且这些帧可以乱序发送，我们只需根据每个帧首部的流标识符就可以重新完成组装。这样极大提升了HTTP的性能。

![][3]

### 多路复用

多路复用允许同时通过单一的 TCP 连接 连接发起多重的请求-响应消息。在 HTTP/1.1 协议中 客户端在同一时间，针对同一域名下的请求有一定数量限制。超过限制数目的请求会被阻塞。针对这一个情况，2.0中采用了多路复用的机制，通过单一的 HTTP/2 连接可以发起多重的请求-响应报文，这样就不用依赖多个TCP连接了。

![][4]

### 首部压缩

每次HTTP请求都会有一个请求首部，这个首部放到一些重要信息，比如Cookie、User Agent之类的字段，这些字段每次请求都是一样的，但还必须要带上。这就造成了一些不必要的浪费。2.0中就优化这一点，引入首部压缩机制，客户端和服务端会维护同样一张首部信息表，每次请求只要发送索引号就可以了，不必带上请求首部上冗余的key-value，极大地减少了不必要的浪费。

### 服务端推送

在2.0之前的版本中，服务端是属于被动一方，只有客户端发送请求，服务端才能发送资源。2.0协议中，服务端可以主动地向客户端发送资源。例如：客户端请求一个html，里面所需要的js和css完全不需要客户端解析完html之后再去请求这些内容那么麻烦，服务端可以在客户端请求html的时候一起回传过来。

## 使用Cookie管理状态

HTTP是一种无状态协议，就是它不会对之前发生过的请求和响应的状态进行管理。也就是说，无法根据之前的状态进行本次的请求处理。例如，我们访问一个需要登录验证的网页，我们登录完成之后没有对其登录状态进行管理的话，那么每次请求新页面都需要重新登陆一下。这样体验就很差。为了解决这一尴尬，于是引入Cookie这个技术。
 **`在没有Cookie信息状态下：`** 客户端发送一个验证请求给服务端，服务端验证通过之后，将验证信息添加在Cookie中，并将其返回给客户端。客户端拿到这个Cookie就将其存在本地。

![][5]
 **`第二次以后（存有Cookie信息状态）的请求`** ：客户端再次请求时会添加之前存在本地的Cookie发送给服务器，服务器根据Cookie带过来的验证信息，对其进行验证，验证通过直接返回数据，反之跳转到登录页。

![][6]

既然Cookie是存在浏览器端的，所以js是可以访问Cookie的，我们本地也可以用Cookie去做一些存储的操作，下面附上js中对Cookie的操作代码。

```js
//写入Cookie
function setCookie(cname, cvalue, exdays) {  
    var d = new Date();  
    d.setTime(d.getTime() + (exdays*24*60*60*1000));  
    var expires = "expires="+d.toUTCString();  
    document.cookie = cname + "=" + cvalue + "; " + expires;  
}  
//读取Cookie
function getCookie(cname) {  
    var name = cname + "=";  
    var ca = document.cookie.split(';');  
    for(var i=0; i<ca.length; i++) {  
        var c = ca[i];  
        while (c.charAt(0)==' ') c = c.substring(1);  
        if (c.indexOf(name) != -1) return c.substring(name.length, c.length);  
    }  
    return "";  
}
//清除cookie    
function clearCookie(name) {    
    setCookie(name, "", -1);    
}     

```

## HTTP报文内的HTTP信息

## HTTP报文结构

用于HTTP协议交互的信息被称为HTTP报文。请求端（客户端）的HTTP报文叫做请求报文，响应端（服务器端）的叫做响应报文。HTTP报文本身是由多行（用CR+LF作换行符）数据构成的字符串文本。

HTTP报文大致可分为请求行/响应行、报文首部和报文主体两块。两者由最初出现的空行（CR+LF）来划分。关于报文首部是我们重点需要掌握的，文章的后面我们会有较大的篇幅去阐述，这边就带过。

![][7]

请求报文的请求行主要三个部分组成：请求方法、URI地址和HTTP协议版本号。

例如：

```
POST https://segmentfault.com/api/article/draft/save HTTP/1.1

```

![][8]

响应报文的响应行主要三个部分组成：HTTP协议版本号、HTTP状态码和状态描述。

例如：

```
HTTP/1.1 200 OK

```

## 编码提升传输速率

HTTP在传输数据时可以按照数据原貌直接传输，但也可以在传输过程中通过编码提升传输速率。通过在传输时编码，能有效地处理大量的访问请求。但是，编码的操作需要计算机来完成，因此会消耗更多的CPU等资源。

### 压缩传输的内容编码

向待发送邮件内增加附件时，为了使邮件容量变小，我们会先用ZIP压缩文件之后再添加附件发送。HTTP协议中有一种被称为内容编码的功能也能进行类似的操作。

内容编码指明应用在实体内容上的编码格式，并保持实体信息原样压缩。内容编码后的实体由客户端接收并负责解码。

![][9]

常用的内容编码格式：

* **`gzip`** 
* **`compress`** 
* **`deflate`** 
* **`identity`** 

### 分割发送的分块传输编码

在本文介绍http协议1.1版本的时候，我们就讲过这些。大家可以再次结合下面这张图来理解整个流程。

![][10]

## 发送多种数据的多部分对象集合

我们发送邮件时，可以往邮件附件里添加图片、视频等数据。这个功能得益于MIME机制，它允许邮件处理文本、图片、视频等不同类型的数据。MIME采用就是多部分对象集合的方法来容纳多份不同类型的数据。

我们在HTTP协议框架下上传图片或文本文件等数据时，也采用了这个多部分对象集合的方法。
 **`多部分对象集合包含的对象如下：`** 

* **`multipart/form-data`** web表单上传文件时使用

* **`multipart/byteranges`** 

状态码206响应报文包含了多个范围的内容时使用。

例如：

```
content-type:multipart/form-data; boundary=WebKitFormBoundary5V53Jp7BUFBGzu9B

// 注：boundary指定划分多部分对象集合的起止符

```

```
--WebKitFormBoundary5V53Jp7BUFBGzu9B
Content-Disposition: form-data; name="image"; filename="表二：价值观评分表.numbers"
Content-Type: application/x-iwork-keynote-sffnumbers

--WebKitFormBoundary5V53Jp7BUFBGzu9B--

//开始的标记：--WebKitFormBoundary5V53Jp7BUFBGzu9B 
//结束的标记：--WebKitFormBoundary5V53Jp7BUFBGzu9B-- 

```

## 获取部分内容的范围请求

在很早之前互联网技术还不是很发达的时候，比如我们下载一个稍微大一点的游戏得时候，如果中途遇到什么情况中断了下载，就必须重新从零开始下载，那种痛在现在高速网络的时代是无法体会到，当然我也是无法体会到的，因为我还很年轻，哈哈哈！为了解决这种痛，范围请求技术应运而生，中断的下载便可得以恢复。

执行范围请求时，会用到首部字段Range来指定资源的byte范围。

```
1.Range:bytes=5001-10000          5001~10000字节
2.Range:bytes=5001-               从5001字节之后全部的内容
3.Range:bytes=-3000,5000-7000     从0~3000字节和5000~7000字节的多重范围

```

针对这个范围请求，我们上面所说到响应会返回状态码206。其中针对多重范围的范围请求时，响应会在用上首部字段 **`Content-Type:multipart/byteranges`** 。

## 内容协商返回最合适的内容

在全球化的浪潮下，催生了一大批国际公司，像国际知名的视频网站YOUTUBE，每天都有来自全球各地的网民登录这个网站。这样面临了一个问题，不同的国家他们的语言是不一样的，YOUTUBE不可能对所有人都千篇一律，都用英文，那这样就不符合这种国际知名大公司的形象。所以YOUTUBE也不可能这么做，但是怎么保证不同国家的网民可以对应不同语言的网站呢？为解决这个问题，内容协商机制就应运而生。如果我们使用的浏览器设置的语言是简体中文，那么我们访问YOUTUBE的时候，则给我们显示的网页便是简体中文的，以此类推。

内容协商机制是指客户端和服务器端就响应资源进行交涉，然后提供客户端最合适的资源。请求报文某些首部字段作为判断的基准,如下：

* **`Accept`** 
* **`Accept-Charset`** 
* **`Accept-Encoding`** 
* **`Accept-Language`** 
* **`Content-Language`** 

内容协商技术有以下三种类型：

* **`服务器驱动协商`**  （由服务器端进行内容协商的方式）
* **`客户端驱动协商`**    （由客户端进行内容协商的方式）
* **`透明协商`**    （由服务器端和客户端各自进行内容协商的方式）

## 返回结果的HTTP状态码

状态码的职责是当客户端向服务器端发送请求时，描述返回的请求结果。借助状态码，用户可以知道服务器端是正常处理了请求，还是出现了错误。这里我们列举常见的HTTP状态码，其他感兴趣的可以自己去查阅。这里我们附上完整的HTTP状态码的地址：[https://developer.mozilla.org...][18]

## 1XX

### 100 Continue （继续）

客户端应当继续发送请求。这个临时响应是用来通知客户端它的部分请求已经被服务器接收，且仍未被拒绝。客户端应当继续发送请求的剩余部分，或者如果请求已经完成，忽略这个响应。服务器必须在请求完成后向客户端发送一个最终响应。

### 101 Switching Protocols （切换协议）

服务器已经理解了客户端的请求，并将通过Upgrade 消息头通知客户端采用不同的协议来完成这个请求。在发送完这个响应最后的空行后，服务器将会切换到在Upgrade 消息头中定义的那些协议。

## 2XX

### 200 OK (成功)

表示从客户端发来的请求在服务器端被正常处理了

### 204 No Content （无内容）

服务器成功处理了请求，但在返回的响应报文不含任何实体内容。比如：页面上有一个a标签，它的href属性设置的是http-204.html，点击a标签，正常情况下会跳转到http-204.html。但是，如果http-204.html的响应码是204，则页面不跳转，停留在当前页。

### 206 Partial Content（部分内容）

该状态码在前面范围请求的时候提到过，它表示客户端进行了范围请求，而服务器成功执行了这部分的GET请求。响应报文中包含由Content-Range指定范围的实体内容。

## 3XX

### 301 Moved Permanently （永久重定向）

该状态码表示请求的资源已被分配了新的URI，以后应使用资源现在所指的URI。响应报文的首部字段会用location字段来标记新的URI。

### 302 Found （临时重定向）

该状态码表示本次请求的资源被临时分配了新的URI。由于这样的重定向是临时的，客户端应当继续向原有地址发送以后的请求。同样，在响应报文的首部字段location中也会标记本次被分配到的新的URI。

### 303 See Other （查看其它位置）

该状态码表示对应当前请求的响应可以在另一个URI上被找到，而且客户端应当采用GET的方式访问那个资源。这里需要注意到的一点就是，303明确说明需要用GET方法获取。跟上面一样，在响应报文的首部字段location中也会标记本次被分配到的新的URI。

### 304 Not Modified （未修改）

该状态码表示所请求的资源未修改，服务器返回此状态码时，不会返回任何资源。客户端通常会缓存访问过的资源，通过提供一个头信息指出客户端希望只返回在指定日期之后修改的资源。

### 307 Temporary Redirect （临时重定向）

跟302 Found的含义是一样的。为什么会有307的出现？愿意在于当303标准虽然禁止POST变成GET,但是实际情况却经常发生，而307会严格遵守标准，不会把POST变成GET。在POST重定向中会很有用。

## 4XX

### 400 Bad Request （错误请求）

该状态码表示由于包含语法错误，当前请求无法被服务器理解，需重新修改再次发送请求。在开发过程中，大家把400归纳到前端的问题，然后后端不理不睬的。这样划分是不对的，这里语法错误大多数情况下并不是前端写的有问题，而是前端某些字段类型与后端没有协同好，比如后端接受的是Long类型或者Date，而前端传的String。又或者前端用json传给后端，后端没有做解析json的操作。这些问题很多情况下是前后端没有协同好造成的，而不是单纯是前端的问题。

### 401 Unauthorized （未授权）

该状态码表示当前请求需要用户验证。该响应必须包含一个适用于被请求资源的WWW-Authenticate信息头用以询问用户信息。

### 403 Forbidden （已禁止）

该状态码表示对请求资源的访问被服务器拒绝了。这种在爬虫过程中最常见，对方发现你正在爬他们网站的数据，然后对你的IP进行限制，就会造成403.

### 404 Not Found（未找到）

该状态码表示服务器上没有找到请求的资源。这个大家经常会遇到，比如说手抖啦，打错字母或者你访问的网站被封啦之类的。

### 405 Method Not Allowed（方法禁用）

该状态码表示请求行中指定的请求方法不能被用于请求相应的资源。该响应报文必须返回一个Allow头信息用以表示出当前资源能够接受的请求方法的列表。

## 5XX

### 500 Internal Server Error（服务器内部错误）

该状态码表示服务器在执行请求时发生了错误，可以是代码存在bug或者某些临时性的故障。

### 503 Service Unavailable（服务不可用）

该状态码表示服务器暂时处于超负载或正在进行停机维护，现在无法处理请求。

## HTTP首部

前面在提到HTTP报文的结构时，我们说过HTTP报文首部是非常重要的一块，将单独拎出来大篇幅地区讲它。这里我们将重点讲这个问题。

首先我们要确定报文首部构成，报文首部可以请求首部（请求头）和响应首部（响应头）。其中，请求首部可拆分为请求首部字段、通用首部字段、实体首部字段、其他拓展首部字段，而响应首部可拆分成响应首部字段、通用首部字段、实体首部字段、其他拓展首部字段。由上我们可以得出：

 **`报文首部=请求首部字段/响应首部字段+通用首部字段+实体首部字段+其他拓展首部字段`** 下面我们将从请求首部字段、响应首部字段、通用首部字段、实体首部字段这四个部分入手，由于字段过多，我们只挑选其中比较常见的首部字段进行阐述。完整版的HTTP首部可以自行查看，附上地址：[https://developer.mozilla.org...][19]

## 请求首部字段

### Accept

该字段用于指定客户端接受哪些类型的信息。

```
Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8

```

上一个例子中的 **`q=`** 来额外表示权重值，用（ **`;`** ）进行分割。q的取值范围为0~1（可精确到小数点后3位），且1为最大值。不指定权重q值时，默认权重为q=1.0。另外，可以用（*）作为通配符，指定任意类型信息。

### Accept-Charset

该字段用于用于指定客户端接受的字符集。

```
Accept-Charset:iso-8859-15,unicode-1-1;q=0.8

```

这里的q跟Accept的q的效果一致，不再赘述。通配符（*）也同样一致。

### Accept-Encoding

用于指定客户端可接受的内容编码。

```
Accept-encoding:gzip, deflate;q=0.9, br

```

这里同样可以用q来表示其优先级,与Accept相同。另外，通配符（*）同样一致。

### Accept-Language

用于指定客户端可接受的自然语言集（指中文或英文等）。

```
Accept-Language:zh-CN,zh;q=0.9,en;q=0.8

```

权重值q跟Accept一致，通配符（*）也一致。

### Authorization

主要用于证明客户端有权查看某个资源。通常，想要通过服务器认证的用户代理会在接收到返回的401状态码响应后，会首部字段Authorization加入请求中。

```
Authorization: Basic YWxhZGRpbjpvcGVuc2VzYW1l

```

### Expect

用于指定期望条件，并告知服务器只有在满足此期望条件的情况下才能妥善地处理请求。HTTP/1.1只规范了一个期望条件，即：

```
Expect: 100-continue

```

服务器会返回状态码100或者状态码417，返回100表示请求头中的期望条件可以得到满足，而417则表示不能满足请求头的期望条件。

### From

用于告知服务器发送请求的用户代理的实际用户的电子邮件地址。比如说：如果你在运行一个机器人代理程序（比如爬虫），那么 Form 首部应该随请求一起发送，这样的话，在服务器遇到问题的时候，例如机器人代理发送了过量的、不希望收到的或者不合法的请求，站点管理员可以联系到你。

```
From: webmaster@example.org

```

### Host

指明了服务器的域名（用于区分虚拟主机），以及（可选的）服务器监听的TCP端口号。端口号是可选的，如果未指定端口，则会自动调用被请求服务器的默认端口号。

```
Host: developer.cdn.mozilla.net

```

### If-Match

形如If-xxx这样样式的请求首部字段，都是条件请求。服务器接收到附带条件的请求后，只有判断指定条件为真时，才会执行请求。

If-Match，它会告知服务器匹配资源所用的ETag值，服务器会比对If-Match的字段值和资源的ETag值，仅当两者一致时，才会执行请求。ETag 之间的比较使用的是强比较算法，即只有在每一个字节都相同的情况下，才可以认为两个文件是相同的。在 ETag 前面添加    W/ 前缀表示可以采用相对宽松的算法。

If-None-Match,它与If-Match功能正好相反。
 **`If-Match`** 

```
If-Match: "bfc13a64729c4290ef5b2c2730249c88ca92d82d"
If-Match: W/"67ab43", "54ed21", "7892dd"
If-Match: *

```
 **`If-None-Match`** 

```
If-None-Match: "bfc13a64729c4290ef5b2c2730249c88ca92d82d"
If-None-Match: W/"67ab43", "54ed21", "7892dd"
If-None-Match: *

```

### If-Modified-Since/If-Unmodified-Since

If-Modified-Since,服务器只在所请求的资源在 If-Modified-Since 给定的日期时间之后对内容进行过修改的情况下才会将资源返回，状态码为200。如果未经修改，那么返回一个不带有消息主体的  304  响应，而在 Last-Modified 首部中会带有上次修改时间。

If-Unmodified-Since,它与If-Modified-Since作用相反
 **`If-Modified-Since`** 

```
If-Modified-Since: Wed, 21 Oct 2015 07:28:00 GMT

```
 **`If-Unmodified-Since`** 

```
If-Unmodified-Since: Wed, 21 Oct 2015 07:28:00 GMT

```

### If-Range

该字段与Range字段配合使用，If-Range字段值中的条件得到满足时，Range 头字段才会起作用，同时服务器回复状态码206（部分内容）以及返回Range字段指定的内容。反之没有得到满足，服务器则将会返回状态码200，并返回完整的请求资源。

```
If-Range: Wed, 21 Oct 2015 07:28:00 GMT

```

### Max-Forwards

前面在讲到TRACE方法时，我们讲到了Max-Forwards字段。使用HTTP协议通信时，请求可能会经过多个代理服务器。途中，如果代理服务器由于某些原因导致请求转发失败，中间我们是不知道是哪台代理服务器失败了。而设置Max-Forwards字段之后，每经过一台代理服务器，Max-Forwards便会减1，到0之后便不会进行转发，而是直接返回响应。这样我们就可以轻松排查出是哪台代理服务器出问题了。

### Proxy-Authorization

这个字段的作用跟Authorization是一样的，他们之间的区别在于Proxy-Authorization用于客户端与代理服务器之间的认证，而Authorization是用于客户端和服务器之间的认证

```
Proxy-Authorization: Basic YWxhZGRpbjpvcGVuc2VzYW1l

```

### Range

该字段用于只获取部分资源的范围请求，Range字段指定了其范围。成功处理范围请求，则返回状态码 206 （部分内容）。若指定范围超出边界，则返回状态码416 Range Not Satisfiable （范围无法满足）。范围合法但没有成功处理，则返回状态码200，并响应全部资源。

```
Range: bytes=200-1000, 2000-6576, 19000-

```

### Referer

当前请求页面的来源页面的地址，即表示当前页面是通过此来源页面里的链接进入的。

```
Referer: https://developer.mozilla.org/en-US/docs/Web/JavaScript

```

### TE

该字段用于告知服务器客户端能够处理响应的传输编码方式及相对优先级。这里不要跟Accept-Encoding弄混， TE是用于传输编码，而Accept-Encoding用于内容编码。

```
TE: trailers, deflate;q=0.5

```

### User-Agent

发起请求的浏览器或者用户代理软件的应用类型、操作系统、软件开发商以及版本号等。

```
User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36
Name

```

## 响应首部字段

### Accept-Ranges

用于告知客户端服务器是否能处理范围请求，若能，则定义范围请求的单位。取值有两种 **`none`** 和 **`bytes`** 。

```
Accept-Ranges: bytes   //范围请求单位为bytes
Accept-Ranges: none    //不支持范围请求

```

### Age

当缓存服务器用自己的缓存的资源去响应请求时，用该头部标识该资源在缓存服务器缓存的时长，单位为秒。

```
Age:600

```

### ETag

服务器分配给每份资源的唯一标识符。当资源更新时，ETag也会相应更新。ETag分为强ETag和弱ETag。

```
Etag:"33a64df551425fcc55e4d42a148795d9f25f89d4"  //无论实体发生多么细微的变化都会改变其值。

ETag: W/"0815"  //只有资源发生了根本改变，产生差异时才改变ETag值。字段值最开始处附加W/

```

### Location

指定需要将页面重新定向至的地址，一般在响应码为3xx的响应中才会有意义。

```
Location:http//www.haimaiche.com/index.html

```

### Retry-After

告知客户端应该在多久之后再次发送请求。主要配合503 Service Unavailable 或者3XX Redirect响应一起使用。取值可以是具体的日期时间也可以是创建响应后的秒数。

```
Retry-After: Wed, 21 Oct 2015 07:28:00 GMT
Retry-After: 120

```

### Server

包含服务器所用到的软件相关信息。

```
Server: Apache/2.4.1 (Unix)

```

### Vary

用于控制缓存。从代理服务器接收到服务器返回包含Vary指定项的响应之后，若再要进行缓存，仅对请求中含有相同Vary指定首部字段的请求返回缓存。即使对相同资源发起请求，但由于Vary指定的首部字段不相同，也必须要从源服务器重新获取资源。

```
Vary:Accept-Language

```
 **`注解：`** 

当代理服务器接收到带有Vary首部字段指定获取资源的请求时，如果使用的Accept-Language字段的值相同时，则直接从缓存返回响应。反之，则需要先从源服务器端获取资源后才能作为响应返回。

### WWW-Authenticate

定义了使用何种验证方式去获取对资源的连接。

```
WWW-Authenticate: Basic realm="Access to the staging site"

```

## 通用首部字段

### Cache-Control

指定指令，用于控制缓存行为。指令可以多选，中间用”,“分割。

```
Cache-Control: no-cache, no-store, must-revalidate

```
 **`缓存请求指令`**

| 指令 | 参数 | 说明 |
| - | - | - |
| no-cache | 无 | 强制向源服务器再次验证 |
| no-store | 无 | 不缓存请求或响应的任何内容 |
| no-transform | 无 | 代理不可更改媒体类型 |
| only-if-cached | 无 | 从缓存获取资源 |
| max-age=<seconds> | 必需 | 响应的最大Age值 |
| max-stale[=<seconds>] | 可省略 | 接受已过期的响应 |
| min-fresh=<seconds> | 必需 | 期望在指定时间内的响应仍有效 |

 **`缓存响应指令`**

| 指令 | 参数 | 说明 |
| - | - | - |
| no-cache | 无 | 缓存前必须先确认其有效性 |
| no-store | 无 | 不缓存请求或响应的任何内容 |
| no-transform | 无 | 代理不可更改媒体类型 |
| public | 无 | 可向任意方提供响应的缓存 |
| private | 无 | 仅向特定用户返回响应 |
| must-revalidate | 无 | 可缓存但必须再向源服务器进行确认 |
| proxy-revalidate | 无 | 要求中间缓存服务器对缓存的响应有效性再进行确认 |
| max-age=<seconds> | 必需 | 响应的最大Age值 |
| s-maxage=<seconds> | 必需 | 公共缓存服务器响应的最大Age值 |

### Connection

决定当前的事务完成后，是否会关闭网络连接。Http协议1.1之后默认都是keep-alive（持久连接），1.0则是close（非持久连接）。

```
Connection: keep-alive
Connection: close

```

### Date

标明HTTP报文的创建日期和时间。

```
Date: Wed, 21 Oct 2015 07:28:00 GMT

```

### Pragma

用来向后兼容只支持 HTTP/1.0 协议的缓存服务器

```
Pragma: no-cache   //唯一形式

```

### Trailer

允许发送方在分块发送的消息后面添加额外的元信息，常用于分块传输编码中。

```
HTTP/1.1 200 OK 
Content-Type: text/plain
... 
Transfer-Encoding: chunked
Trailer: Expires
...(报文主体)...
0
Expires: Wed, 21 Oct 2015 07:28:00 GMT

```

以上用例中，指定首部字段Trailer的值为Expires，在报文主体之后（分块长度0之后）出现首部字段Expires。

### Transfer-Encoding

规定了报文主体时采用的编码方式。

```
Transfer-Encoding: chunked

```

### Via

用于追踪客户端与服务器之间的请求和响应报文的传输路径。报文经过代理或网关时，会先在首部字段Via中附加该服务器的信息，然后再进行转发。经常和TRACE方法一起使用。

```
Via: 1.0 fred, 1.1 p.example.net

```

### Warning

用于告知用户一些与缓存相关的问题的警告。

```
Warning: <警告码> <警告的主机:端口号> <警告为别把> [<日期时间（可选）>]
Warning: 112 gw.hacker.jp:8080  "cache down" " Wed, 21 Oct 2015 07:28:00 GMT"

```
 **`警告码表`**

| 警告码 | 警告内容 | 说明 |
| - | - | - |
| 110 | Response is Stale | 由缓存服务器提供的响应已过期（设置的失效时间已过）。 |
| 111 | Revalidation Failed | 由于无法访问服务器，响应验证失败。 |
| 112 | Disconnected Operation | 缓存服务器断开连接。 |
| 113 | Heuristic Expiration | 如果缓存服务器采用启发式方法，将缓存的有效时间设定为24小时，而在该响应的年龄超过24小时时发送。 |
| 199 | Miscellaneous Warning | 任意的警告信息。 |
| 214 | Transformation Applied | 由代理服务器添加，如果它对返回的展现内容进行了任何转换，比如改变了内容编码、媒体类型等。 |
| 299 | Miscellaneous Warning | 由于无法访问服务器，响应验证失败。 |

## 实体首部字段

### Allow

告诉客户端资源所支持的HTTP方法。

```
Allow: GET, POST, HEAD

```

### Content-Encoding

告知客户端服务器对实体主体部分选用的内容编码方式。

```
Content-Encoding: gzip

```

### Content-Language

告知客户端，实体主体使用的自然语言。

```
Content-Language: zh-CN

```

### Content-Length

表明实体主体部分的大小，单位是字节。

```
Content-Length:15000

```

### Content-Location

表示返回的数据对应的URI，主要用于指定要访问的资源经过内容协商后的结果的URI。

```
Content-Location:http://www.hacker.jp/index.html

```

### Content-Range

主要用于范围请求，告知客户端当前发送部分的内容范围以及整个实体大小。

```
Content-Range: bytes 200-1000/67589

```

### Content-Type

表明实体主体内对象的媒体类型。

```
Content-Type: text/html; charset=utf-8

```

### Expires

将资源的失效日期告诉客户端。缓存服务器在接收到含有首部字段Expires的响应后，在Expires指定时间之前，响应的副本会一直被保存。反之，缓存服务器会向源服务器请求资源。当Cache-Control有指定max-age或者s-maxage指令时，Expires则会被忽略。

```
Expires: Thu, 01 Dec 1994 16:00:00 GMT

```

### Last-Modified

表明资源最终的修改时间。

```
Last-Modified: Wed, 21 Oct 2015 07:28:00 GMT

```

## 其他拓展首部字段

### Set-Cookie

用来由服务器端向客户端发送cookie

```
Set-Cookie: id=a3fWa;Domain=somecompany.co.uk; Path=/ Expires=Wed, 21 Oct 2015 07:28:00 GMT; Secure; HttpOnly

```
 **`Domain：`** 指定Cookie可以发送的主机名
 **`Path：`** 限定可以发送Cookie的路径
 **`Expires：`** 指定Cookie的有效期
 **`Max-Age：`** 多少秒之后Cookie失效，ie8及其以下不支持这个属性，Max-Age优先级大于Expires
 **`Secure：`** 仅在HTTPS安全通信时才会发送Cookie
 **`HttpOnly：`** 使js无法获取Cookie

### Cookie

含有先前由服务器通过Set-Cookie首部投放并存储到客户端的Cookie。

```
Cookie: PHPSESSID=298zf09hf012fh2; csrftoken=u32t4o3tb3gg43; _gat=1;

```

### DNT

位于HTTP请求首部。全称Do Not Track。表示拒绝被精准广告追踪的一种方法。

```
DNT:0;//同意目标站点追踪用户个人信息。
DNT:1;//不同意目标站点追踪用户个人信息。

```

### X-Frame-Options

主要位于HTTP响应首部，用于控制网站内容在其他Web网站的Frame标签内的显示问题。 其主要目的是为了防止点击劫持（clickjacking）攻击。

有以下三个取值：

* **`DENY`** ：表示该页面不允许在 frame 中展示，即便是在相同域名的页面中嵌套也不允许。
* **`SAMEORIGIN`** ：表示该页面可以在相同域名页面的 frame 中展示。
* **`ALLOW-FROM uri`** ：表示该页面可以在指定来源的 frame 中展示。

```
X-Frame-Options: DENY
X-Frame-Options: SAMEORIGIN
X-Frame-Options: ALLOW-FROM http://caibaojian.com/

```

### X-XSS-Protection

当检测到跨站脚本攻击 (XSS)时，浏览器将停止加载页面。针对现代浏览器，会选择更强大的Content-Security-Policy。关于Content-Security-Policy可参考阮一峰老师的这篇文章《Content Security Policy 入门教程》

```
X-XSS-Protection: 0  //禁止XSS过滤。
X-XSS-Protection: 1  //启用XSS过滤（通常浏览器是默认的）。如果检测到跨站脚本攻击，浏览器将清除页面（删除不安全的部分）。
X-XSS-Protection: 1; mode=block //启用XSS过滤。 如果检测到攻击，浏览器将不会清除页面，而是阻止页面加载。
X-XSS-Protection: 1; report=<reporting-uri> //启用XSS过滤。 如果检测到跨站脚本攻击，浏览器将清除页面并使用CSP report-uri指令的功能发送违规报告。

```

## 确认Web安全的HTTPS

说到HTTPS，相信大家都很熟悉，什么是HTTPS?说白了就是HTTP的安全版，在HTTP层面上加入了安全控制。下面我们要说说HTTP有哪些安全问题？HTTPS又是如何保证安全的。

## HTTP缺点

### 通信使用明文可能会被窃听

由于HTTP本身不具备加密的功能，所以HTTP报文都是以明文的方式发送的。而整个互联网那些网络设备都不可能是你个人的，这就不能排除某个环节中遭到恶意窥探的行为。

![][11]

即使你人为地在发送之前的采用对称加密算法加密了，且不说影响效率，接收方每次都需要解密一下。这样可以真的保证其安全吗？答案是不能保证的。接收方想要解密密文首先得知道这采用什么方式加密的或者密钥又是啥？这个是需要发送方给接收方的，而这个发送密钥或者加密方式的过程依旧是处于被窃听的危险中，所以仅仅靠加密数据是无法保证其安全性的。

### 不验证通信方的身份就有可能遭遇伪装

HTTP协议是不会对通信方进行确认的。无论谁发送过来的请求都会返回响应。这就可能存在有伪装的客户端或者服务器，一些本有权限控制的资源遭到盗取。Dos攻击就是利用HTTP协议不进行通信方确认的漏洞，发送海量无意义请求，超出服务器的负荷，导致服务器奔溃宕机。

### 无法证明报文完整性，可能已遭篡改

前面我们说到互联网上网络设备由于不属于你个人的，很难保证他人会不会劫取你的信息并进行篡改。导致服务器传送给客户端的文件和客户端实际接收到的文件不能保证一致。

![][12]

## HTTP通信过程
 **`第一步：`**  客户端发送一段包含有客户端支持的SSL/TLS协议版本、可支持加密组件（使用的加密算法以及密钥长度等）报文给服务器。
 **`第二步：`**  服务器会根据客户端支持的SSL/TLS协议版本以及其支持的加密中选择一种，作为后续通信过程中所使用的SSL/TLS协议以及加密组件，并放在报文中告知客户端。
 **`第三步：`**  之后服务器会再发送一段报文，其中包含公开密钥证书。与公钥相配对的私钥则留在服务器自己那。
 **`第四步：`**  客户端拿到公开密钥的证书之后，会向颁发数字认证证书的机构确认其合法性。若合法，则会根据与服务器协商确定的加密组件，随机生成一个密钥，而这个密钥将用于后续报文的加密。并将生成好的密钥用服务器给的公钥进行加密，并发送给服务器。
 **`第五步：`**  服务器拿到客户端加密过得密钥之后，会用之前留在自己那的私钥去解密获取到密钥。
 **`第五步：`**  客户端与服务器之间通信的报文都会用到这个密钥进行加密。

![][13]

上面的过程第一次读可能无法理解，现在在重新总结一下。整个HTTPS通信大体可以分为三个阶段：

1. **`协商决定加密组件`** 

2. **`确定加密解密密钥`** 

3. **`客户端与服务器开始报文通信`** 

这里我们理清楚一点，最终用来加密解密的密钥并不是由服务器生成的，而是客户端生成的。其中部分细节我们在后续会讲到。

## HTTP+加密+认证+完整性保护=HTTPS

首先我们弄要弄清楚一件事，HTTPS并非应用层的一种新协议。只是HTTP通信接口部分用SSL或者TLS协议代替而已。原先HTTP协议直接和TCP协议对接，而HTTPS则是HTTP先与SSL/TLS通信,再由SSL/TLS和TCP通信。

### 加密

前面我们说到HTTP是采用明文传输的，这就免不了你的信息会被他人窥探。即使是采用对称加密算法，无法保证你在发送密钥的过程中不被他人窃取到。换句话说我们保证了密钥的安全传输，那么整个通信过程就不怕被窥探到了。HTTPS采用了非对称加密的方式，用公钥去加密，用私钥去解密。服务器把公钥交给客户端去加密通信密钥，然后再用自己的私钥去解密就可获取到通信密钥。常见的非对称加密算法就是RSA，有兴趣的小伙们可以自行了解一下，这里不再赘述。

上面提到了非对称算法感觉是个好东西，服务器和客户端通信的报文采用非对称加密算法不就完了，干嘛还要绕一个弯，采用非对称加密+对称加密的组合。HTTPS这样设计是有其原因的，非对称加密算法看似很美好，但是其对CPU和内存的开销太大。有人做过实验，在加解密同等数量的文件下，非对称算法的开销是对称算法的1000倍以上。由此可见，这个非对称算法是多影响性能。性能这个问题，就算可以忍受，非对称算法有一个致命的缺点就是加密内容的长度不得超过公钥长度，常用的公钥长度是2048位，也就是256个字节，也就意味着加密的密文的大小不能超过256个字节。这个就太坑爹了，现在网络上的图片随随便便就是几千字节，这个就真的不能忍了。如果这样你都能忍，那你是真的diao，是在下输了。

### 认证

前面说到HTTP整个过程中，你的报文是可以被劫取到的，所以在发送公钥的过程中很难保证其不被掉包。为了保证密钥的合法性，HTTPS采用了数字证书这个概念。整个数字证书认证流程是这样子的：

首先服务器的运营人员会想权威的第三方数字证书认证机构申请公开密钥。数字证书认证机构在判明申请者的身份之后，对已申请的公开密钥做数字签名的操作，并把这个数字签名分配给这个公开密钥，并将已签名过的公开密钥放在公钥证书里面。客户端拿到这个公钥证书之后，向数字证书认证机构提出验证公钥证书上数字签名的请求，以确认公开密钥的真实性。有了第三方的数字证书验证机构，我们就可以保证了公开密钥不被他人恶意篡改。

### 完整性保护

有了加密和认证足以保证通信报文可以不被他人看到，但我们报文是可以被他人截取到进行篡改的。比如完整的信息是这样子的“我不要你做我的女朋友了，我要你做我的老婆”，这本来是一句非常浪漫的话，要是被破坏了只传了“我不要你做我的女朋友了”，接收方还不知道这个信息是被篡改过得，那么就出大事了，这对小情侣就拜拜了，一段好姻缘就这么被破坏了。为了世界和平，我们还保护报文的完整性。HTTPS使用了MAC算法来保证其完整性。发送方发送报文会带有一个由MAC算法得出的一个MAC值。接受方拿到报文之后会根据密钥和MAC算法再算出一个MAC值，与传过来的MAC值进行比对，若一致则没有遭受篡改，这样就可以知道报文有没有被篡改过。

## HTTPS相比较HTTP的缺点

HTTPS相比较HTTP并不是各个方面都强于HTTP的。若是这样，那我们现在怕是见不到采用HTTP协议的网站了。HTTPS相比较HTTP主要有三个缺点：

### 速度慢

原本HTTP直接和TCP进行通信，现在中间出现了一个第三者SSL/TLS，势必会造成处理的通信量变大，拖累速度。

### CPU及内存等资源的消耗大

频繁地加解密，毫无疑问就需要更多CPU和内存等资源的支持，导致负载增强。

### 要钱

要进行HTTPS通信，证书是必不可少的，这个得向认证机构购买的，人认证机构不可能白给你的，收取点费用也是理所当然的。前面两个为了安全还是可以接受的，这个原因我个人觉得是一些网站不使用HTTPS的主要原因，毕竟谈钱伤感情嘛。当然穷逼不代表没有活路，总有一些大神在默默地拯救着我们这群穷屌丝，[《Let's Encrypt，免费好用的 HTTPS 证书》][20] 。另外再帮别人打个小广告，[《给网站戴上「安全套」》][21]，请记住我的名字，我叫雷锋！

## 确认访问用户身份的认证

随着Web技术的发展，越来越多的网站都更加地精细化，有些内容或者操作只有特定的用户才能看见或者操作。此时，就需要验证坐在计算机前面的那个人是不是属于那些特定用户，下面我们就说说HTTP使用的认证方式。

## BASIC认证

![][14]
 **`流程1：`**  当客户端访问的资源需要BASIC认证时，服务器会返回401，并在响应头添加首部字段WWW-Authenticate，该字段包含验证方式（BASIC）以及realm（告诉客户端要访问的资源属于服务器众多区域中的哪一片区域里， 如果未指定realm, 客户端通常显示一个格式化的主机名来替代。）
 **`流程2：`**  客户端接收到状态码401之后，就会根据响应头中的WWW-Authenticate指定的认证方式（BASIC）把用户名和密码用冒号（:）进行连接，然后再Base64编码处理，最后塞到请求头首部字段Authorization中发送给服务器。

举个栗子：

```
用户名：admin  密码：123456

admin:123456    =>  YWRtaW46MTIzNDU2

Authorization: BASIC  YWRtaW46MTIzNDU2

```
 **`流程3：`**  服务器接收到含有首部字段Authorization的请求之后，对其认证信息进行确认。通过，则返回其请求的资源。

BASIC认证仅仅做了Base64编码，并未进行进行加密处理，很有可能会被他人窃听盗取，安全性太差。## DIGEST认证

![][15]
 **`流程1：`**  当客户端访问的资源需要DIGEST认证时，服务器会返回401，响应头部比Basic模式复杂，WWW-Authenticate: Digest realm=”myTomcat”,qop="auth",nonce="xxxxxxxxxxx",opaque="xxxxxxxx" 。其中qop的auth表示鉴别方式；nonce是随机字符串；opaque服务端指定的值，客户端需要原值返回。
 **`流程2：`**  浏览器弹出对话框让用户输入用户名和密码，浏览器对用户名、密码、nonce值、HTTP请求方法、被请求资源URI等组合后进行MD5运算，把计算得到的摘要信息发送给服务端。请求头部类似如下，Authorization: Digest username="xxxxx",realm="myTomcat",qop="auth",nonce="xxxxx",uri="xxxx",cnonce="xxxxxx",nc=00000001,response="xxxxxxxxx",opaque="xxxxxxxxx" 。其中username是用户名；cnonce是客户端生成的随机字符串；nc是运行认证的次数；response就是最终计算得到的摘要。
 **`流程3：`** 服务端web容器获取HTTP报文头部相关认证信息，从中获取到username，根据username获取对应的密码，同样对用户名、密码、nonce值、HTTP请求方法、被请求资源URI等组合进行MD5运算，计算结果和response进行比较，如果一致则认证成功并返回相关资源。

DIGEST认证相比BASIC认证没有将密码传输出去，在安全性上要比BASIC认证提高很多。但是要是攻击者截取你的报文便可以用报文首部字段Authorization的值伪装起来向服务器发起请求。## SSL客户端验证

前面我们说到HTTPS的时候，是服务器作为发送证书的一方，而客户端作为接受证书验证证书的一方。这里，正好两者角色对调了一下，通话密钥的生成方在服务器端，而不是客户端。一般不会独立使用，而是和表单验证配合使用。SSL客户端验证用来验证客户端计算机，而表单验证则用来验证坐在计算机前的人。

## 基于表单验证

目前主流的网站都是用的是表单验证的方式。它的原理很简单，客户端提交带有用户名和密码的表单交给后台，后台会生成一个特定的SessionId放在响应头字段Set-Cookie中，客户端接受到之后存储到本地Cookie。之后的请求，都会把Cookie带上，服务器便会根据Cookie里面存的SessionId去识别用户。

![][16]

## 总结

这篇文章是在阅读完《图解HTTP协议》这本书之后作的的一篇总结性文章。HTTP协议我们重点要掌握的是TCP/IP模型、HTTP的通信过程、HTTP状态码、HTTP首部、HTTPS的原理、HTTP和HTTPS的各自优势。其中HTTP状态码和HTTP首部是本文的重中之重，文章花了大量篇幅去讲解这两块。还有HTTPS，现在越来越多的网站开始采用这个协议，所以掌握HTTPS必不可少。希望这篇文章可以帮助那些对HTTP协议还很模糊的小伙伴们可以加深对HTTP协议的理解，本文不足之处欢迎留言指出。最后写博客不易，还望小伙伴们多多点赞收藏支持！

[17]: https://www.taobao.com/%E8%BF%99%E4%B8%AA%E5%9F%9F%E5%90%8D%E5%B0%B1%E6%98%AFURL
[18]: https://developer.mozilla.org/zh-CN/docs/Web/HTTP/Status
[19]: https://developer.mozilla.org/zh-CN/docs/Web/HTTP/Headers
[20]: https://imququ.com/post/letsencrypt-certificate.html
[21]: https://ourai.ws/posts/keep-your-website-safe/
[0]: ./img/bV0osE.png
[1]: ./img/bV0v4M.png
[2]: ./img/bV0zIc.png
[3]: ./img/bV0AOq.png
[4]: ./img/bV0ARg.png
[5]: ./img/bV0A6F.png
[6]: ./img/bV0A65.png
[7]: ./img/bV00rA.png
[8]: ./img/bV00rI.png
[9]: ./img/bV0UHp.png
[10]: ./img/bV0Vco.png
[11]: ./img/bV1KeH.png
[12]: ./img/bV1KEE.png
[13]: ./img/bV1N2S.png
[14]: ./img/bV1PuR.png
[15]: ./img/bV1Pu5.png
[16]: ./img/bV1PGt.png