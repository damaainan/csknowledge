## 理解TCP/IP底层的结构和传输

来源：[https://segmentfault.com/a/1190000015498651](https://segmentfault.com/a/1190000015498651)


## 0.前言

计算机网络是大学计算机系的核心基础课，可能大部分人上完课也就忘得差不多了，工作之后，若不是和运维相关，就丢得更远了。
作为一名程序员，对于TCP/IP协议的应用层或运输层的协议还是比较熟悉的，下面两层的协议可能就不太关注了。而链路层和网络层，正是构成目前全球互联网的基石。
运输层及应用层协议，是高度抽象的，几乎把所有网络底层的传输机制屏蔽了。作为一个毕业了好几年的程序员，我很好奇网络底层的结构和传输，所以结合原来的课程以及工作中的理解，整理了这篇文章。
计算机网络是一个很复杂的东西，一篇文章不可能把所有问题涵盖。本文主要从工作的网络环境出发，一层层介绍整个互联网是怎么运作起来的。
## 1.协议的分层

TCP/IP协议栈分为四层，每层及其作用如下：


* 链路层：在局域网中，如何解决两个节点之间的通信问题。
* 网络层：在任意网络连成的网络中，如何解决两个节点之间通信的问题。（IP、ICMP、ARP）
* 运输层：如何按可靠性要求来通信。（TCP、UDP）
* 应用层：适合具体应用场景的通信。（HTTP、SMTP、FTP）


协议的分层，是网络设计先驱们最大的成就。他让每层专注一件事，整个协议栈也非常灵活。这样的划分形成了生态和标准，让硬件厂商、软件公司、科研机构等都能参与其中，做自己擅长的那部分，构建整个全球互联网。
## 2.全局概览

按照协议分层去介绍会很枯燥也很难理解。个人觉得从一个具体场景出发，把前后左右融汇贯通，串连起来讲可能更易于理解。本文的所有内容都在下面这张图。
首先介绍在一个办公室的局域网如何通信；其次介绍办公室，家，连接同一个ISP（互联网服务提供商，比如中国电信、中国铁通）构成的AS(自治系统)内如何通信；然后介绍由ISP连成的主干网络内部如何通信；最后介绍一个公司，不同办公地点，不同机房之间如何构造专用网通信。

![][0]
## 3.局域网内部如何通信

主机通过集线器/交换机连接到一起，一般就可以构成一个局域网，比如公司、家庭就是典型的局域网。局域网的通信相对比较简单，链路层就主要干这个事情。
### 3.1标识系统——mac地址

任何通信系统，都会涉及到标识和识别问题。mac地址是链路层通信的标识。mac地址是固化在设备上的，比如网卡、路由器，由全球专门的组织统一分配，不会冲突。mac地址有48位，完全够用。
### 3.2通信载体mac帧

两个节点通信用什么载体很重要，就像送快递的车一样。链路层的通信载体是mac帧，结构如下：

![][1]

核心理解4个字段。目标地址和源地址，就是通信双方的mac地址，可以类比快递的收货和发货地址。数据，就是mac帧传输的内容，载荷。FCS是个校验和，用来检查数据有没有损坏，当然链路层不做可靠性保证，损坏就直接丢弃了。
### 3.3集线器连接的局域网

集线器连接的局域网已经基本淘汰了，我们也可以拿出来看一下局域网原始的样子。如下图：

![][2]

主机通过集线器连接到一起，构成一个星形网络。集线器结构简单，工作在物理层，它的作用是将一个端口的数据广播到其它端口。
两点通信怎么实现呢？比如小A要给小C发一个mac帧。是这样子的，集线器收到小A的mac帧时，会同时发送给小B和小C，小B一看不是给我的，遂丢弃，而小C发现是给自己的，即收下。集线器通过这样的方式，实现了点对点的通信。这种方式最大的缺陷在于，网络中的节点没法同时通信，否则就碰撞乱套了。这种连接方式有一个碰撞检测技术，即检测到其它端口再发数据，则暂停一会再发送。也就是整个网络是串行的，信道的通信效率是很低的。
### 3.4交换机连接的局域网

交换机是目前主流的局域网连接方式。如下图：

![][3]

交换机工作在链路层，相对于集线器，交换机则具备“学习”和更加精确转发的能力。
交换机的学习方法是这样的。还是假设小A给小C发送数据，交换机在第一次收到这个数据时，也是懵逼的，不知道转到哪个端口，所以它需要像集线器一样，做一次广播。但是有心机的交换机同时做了另一件事情，把小A的mac地址和对应的转发端口给记下来了，这样下次再收到给小A的请求，就可以准确转发了。这样的请求来回几次后，交换机就学到了所有的请求如何转发。
交换机连接的网络是可以并行的，效率很高。交换机还有个好处是还可以和其它交换机相连，把局域网扩展成更大的一个局域网，甚至可以突破小范围的地理限制。
### 3.5局域网嗅探

局域网嗅探就是通过技术手段，获取其它主机的网络流量。大致有下面几种：
集线器嗅探，这种嗅探方式非常简单，因为集线器是靠广播工作，嗅探主机不管包是不是给自己的，通通收下就好了。
管理员嗅探，对于交换机连接的网络，一般可以设置将交换机的所有流量，通通抄送给某个接口，从而达到网络管理的目的。
交换机嗅探，可以利用洪泛（产生大量的垃圾帧）技术把交换机的转发表搞乱，根据交换机的特性，查询不到对应的转发规则，就只能傻傻的广播了，从而截获到整个局域网的流量。
所以，连陌生wifi时要特别小心，小心合租的技术男。顶层使用https也很重要，即使截获了，也不知道传输内容，黑客必须结合其它攻击方式才能做坏事。
## 4.局域网之间的通信

局域网之间的通信，包括下一节要讲的主干网之间的通信，是网络层要主要解决的问题。还是按照之前的套路，先把标识和载体搞清楚。
### 4.1网络层的标识系统——IP地址

和mac地址不一样，IP地址是逻辑地址与硬件无关。IP地址也是国际组织统一分配，全球唯一。目前用得最多的ipv4地址只有32位，不是很够用（后面会讲如何解决不够用的问题）。IP一般用点分十进制表示，像这样：113.118.186.179。IP地址在分配的时候，一般分为两部分，网络号和主机号。网络号一般为前若干位，分配给一个机构，机构自己再分配到主机。
### 4.2网络层的传输载体——IP数据包

![][4]

IP数据包核心也是要理解4个字段。目标地址和源地址，即通信双方的IP地址。生存时间表示这个数据包还能走多远，每过一条路由器会减一，这个设计是防止网络上产生大量的垃圾数据包消耗资源。数据部分呢就是IP数据报要传输的内容了。
### 4.3自治系统(AS)

下面就开始介绍本节的重点，这张图的原理：

![][5]

多个局域网，通过路由器连接构成的更大网络，一般叫做自治系统（AS），自治系统使用内部网关协议，保证系统能够独立运行。真实的场景可能是对应某个本地ISP，将附近的家庭或办公室构成的局域网连接而成的网络。
抽象一下，AS大致就是下面这张图的结构。

![][6]

这里多了一种硬件设备——路由器。路由器的作用是把IP数据报转发给下一跳地址，从而达到跨网络的通信的目的。
AS内部的通信，核心是路由的选择协议，这里以RIP协议为例来介绍基本原理。
RIP也是一种自学习的协议，即在没有人为干扰的情况下，能够学习如何转发数据包。
协议通过跳数来表示路由的代价，即隔了多少个路由器，不考虑带宽和网络状态。
相邻的路由器，定时交换路由表，收到路由表后，将目标网络的下一跳地址改成路由器的地址，同时将距离加一。
路由器一开始只知道相邻网络的路由（管理员配置），进过一段时间的传播、交换后，最终收敛。
按照上图的结构，一开始R1的路由表为。

| 目标网络 | 下一跳 | 距离 |
| - | - | - |
| LAN1 | 端口1 | 1 |
| LAN2 | 端口2 | 1 |
| LAN3 | 端口3 | 1 |


R2的路由表为

| LAN2 | 端口1 | 1 |
| - | - | - |
| LAN3 | 端口2 | 1 |
| LAN4 | 端口3 | 1 |

R1收到R2的路由表后，由于LAN2,LAN3的最短距离是1，所以距离为2的会舍弃掉。

| 目标网络 | 下一跳 | 距离 |
| - | - | - |
| LAN1 | 端口1 | 1 |
| LAN2 | 端口2 | 1 |
| LAN3 | 端口3 | 1 |
| LAN2 | R2 | 2 |
| LAN3 | R2 | 2 |
| LAN4 | R2 | 2 |


R2也是同样的道理，最终R2会多一条到LAN1的路由，下一跳是R1，距离是2。至此已经收敛，任意两个网络之间都能够通信了。
## 5.主干网之间的通信

AS连接的网络数是有限的，因为网络上的每个路由器，都要存所有目标网络的下一跳地址，对于主干网，开销是非常大的。还有就是AS连接的网络是一个分布式的网状结构，不利于政治、安全方面的控制。比如在国内的通信，数据包不需要到国外兜个圈子再回来。
### 5.1树形结构的主干网

下面开始介绍这张图的原理。

![][7] 
不同层级的ISP，形成了全球互联网顶层的树状结构。这个结构的通信，使用的是外部网关协议BGP。
我们把外部网关的结构简化成下面这张图：

![][8]

可见是由不同层级AS构成的一个树状结构。AS与其它AS的连接处，会有一个路由器，作为BGP发言人，作用是构建路由信息，并转发数据报。
路由表建立的过程可以总结为：自底向上传播，从上到下扩散。
具体的过程是这样的，比如：
AS4会告诉AS2,到N1、N2可以经过AS4。AS5会告诉AS2,到N3、N4可经过AS5。
AS2收到消息后，记录，并继续向上传播。
AS2告诉AS1到N1、N2、N3、N4可以经过AS2。
AS1继续把这个消息向下扩散，告诉AS3，到N1、N2、N3、N4，可以经过AS1。
AS3继续向下扩散，告诉AS6，到N1、N2、N3、N4可以经过AS3，从而网络N5就能发送数据报给N1、N2、N3、N4了。
其它的节点是一样的道理。
## 6.虚拟专用网络

对于一个结构或组织，他的诉求是专网专用。跨地域构建专用局域网成本是非常高的，但可以通过虚拟化的技术构建一个虚拟专用网。
虚拟专用网的场景如下面这张图，某公司有甲已两地的办公室，还有云计算服务商的机房，构成了一个虚拟的专用网络

![][9]
### 6.1专用IP地址

由于IP地址是也有限的，对应专用网，很难分配全球唯一IP。由于是专网，IP地址冲突了问题也不大，只要内部不冲突。所以IP地址有一部分是保留出来给专网用的，全球互联网的路由器不会转发专用IP地址的包。专用IP地址如下：

![][10]
### 6.2 IP隧道技术

下面是一个典型的虚拟专用网络：

![][11]

隧道技术的核心是，通过隧道服务器，一边连局域网，一边连公网。隧道服务器在公网上构建一条加密的传输通道，让不同局域网构成一个隧道连接，从而达到专网的效果。
大致的过程是，隧道服务器会拦截所有的专网IP的数据报。将其加密，包装成新的IP数据报或者运输层协议的报文，通过公网传递给下一个隧道服务器。隧道服务器收到这样的数据报后，会解密和拆解，送到对应局域网。从而实现了跨网络的通信。
至此，各种网络，不同层级如何通信，基本介绍完毕。
## 7.结语

计算机网络内容很多，还有很多细节。本文主要是介绍了整体的流程和原理。有些基本的思想和方法，是非常巧妙的，值得在解决问题时借鉴。

[0]: ./img/bVbc0U2.png
[1]: ./img/bVbc0W5.png
[2]: ./img/bVbc0XK.png
[3]: ./img/bVbc0YZ.png
[4]: ./img/bVbc0Z6.png
[5]: ./img/bVbc0ZL.png
[6]: ./img/bVbc73X.png
[7]: ./img/bVbc8cw.png
[8]: ./img/bVbc8cE.png
[9]: ./img/bVbdb20.png
[10]: ./img/bVbdb3j.png
[11]: ./img/bVbdb3k.png