# (12)TCP/IP协议-TCP建立与终止连接


## 一、引言

TCP是一个面向连接的协议。无论哪一方向另一方发送数据之前，都必须先在双方之间建立一条连接。连接创建与终止的状态变化图如下：

![][1]



图1.tcp建立连接与终止连接状态转换图

## 二、三次握手建立连接

![][2]



图2.tcp建立连接三次握手

  
过程如下：

* 客户端发送一个SYN数据包指明客户端打算连接服务器的端口，初始化序号（ISN）为m。
* 服务器发回包含服务器的ISN作为应答（值为n）。同时，将确认序号设置成客户端ISN+1（m+1）来作为对客户端SYN的确认。
* 客户端发送一个ACK数据包，ack=n+1,作为对服务器的SYN的确认。

#### 1.为什么是三次握手，而不是两次

网络是不可靠的，数据包是可能丢失的。假设没有第三次确认，客户端向服务端发送了 SYN，请求建立连接。由于延迟，服务端没有及时收到这个包。于是客户端重新发送一个 SYN 包。回忆一下介绍 TCP 首部时提到的序列号，这两个包的序列号显然是相同的。假设服务端接收到了第二个 SYN 包，建立了通信，一段时间后通信结束，连接被关闭。这时候最初被发送的 SYN 包刚刚抵达服务端，服务端又会发送一次 ACK 确认。由于两次握手就建立了连接，此时的服务端就会建立一个新的连接，然而客户端觉得自己并没有请求建立连接，所以就不会向服务端发送数据。从而导致服务端建立了一个空的连接，白白浪费资源。  
TCP是双通道，需要双向确定。只有两次握手，客户端知道了服务器收到了，服务器不知道客户端收到了，联想打电话。通讯系统中的占拜庭将军问题。

#### 2.最大报文段长度

最大报文段长度（MSS）表示TCP传往另一端的最大块数据的长度。当一个连接建立时，连接的双方都要通告各自的MSS。在三次握手的时候SYN的TCP首部中的可选字段确定。以太网的默认长度为1460。

#### 3.

## 三、四次握手关闭连接（正常状态）

建立一个连接需要三次握手，而终止一个连接要经过4次握手。这由TCP的半关闭(half-close)造成的。一个TCP连接是全双工（即数据在两个方向上能同时传递），因此每个方向必须单独地进行关闭。

![][3]



图3.四次握手关闭连接

* 主动方想要关闭连接，发送FIN包给被动方，序号为m
* 被动方接收到主动方发送的FIN包，知道了对方要关闭连接，发送ACK确认包，序号m+1。主动方连接关闭。
* 等待片刻（处于半关闭状态），在此期间（fin_wait2,close_wait）。被动方发送最后的数据，主动方接收最后的数据。
* 被动方确认要关闭连接，发送FIN包。序号n。
* 主动方等待片刻（接收网络中，还未到达的数据包），发送ACK确认包。序号n+1。到此连接关闭。

#### 1.TCP的半关闭状态

TCP提供了连接的一端在结束它的发送后还能接收来自另一端数据的能力。如主动方处于fin_wait2状态。

#### 2.TIME_WAIT状态

TIME_WAIT状态也称为2MSL等待状态。每个具体TCP实现必须选择一个报文段最大生存时间MSL（ Maximum Segment Lifetime）。它是任何报文段被丢弃前在网络内的最长时间。因为TCP报文段以IP数据报在网络内传输，而IP数据报则有限制其生存时间的TTL字段。在实际应用中，对 I P数据报TTL的限制是基于跳数，而不是定时器。  
在处于2MSL等待状态的socket(客户端IP与端口，服务器IP与端口)不能再被使用。但在实际的使用中，允许一个新的连接请求到达仍处于time_wait状态的连接，只要新的序号大于该连接的前一个连接的最后序号。

## 四、正常状态抓包

下面是一次完整的tcp建立连接，发送数据，关闭连接过程

![][4]



图4.tcp一次完整请求

该过程为，3次握手建立连接，一次数据发送，4次握手关闭连接

## 五、异常情况

出现异常的时候，服务器通常通过复位报文来通告，复位报文为tcp数据包类型设置为rst。

#### 1.连接超时或到达不存在的端口/服务器

当服务器端没有开或网络问题，会出现连接超时的情况。抓包如下：

![][5]



图5.连接超时

  
客户端尝试3三次来连接，有时候服务器端会发送rst数据包。

#### 2.异常终止一个连接

在TCP通讯中。如果通讯双方应为某种原因（如突然断电等）关闭连接时候一方（如A）没有发送fin数据包。另一端(如B)不知道对方已经关闭了连接。再次发送数据的时候，异常关闭的一方，可能会返回一个rst数据包。通知异常关闭。如果一方已经关闭或异常终止连接而另一方却还不知道，我们将这样的TCP连接称为半打开(Half Open)的。

#### 3.同时打开

两个应用程序同时彼此执行主动打开的情况是可能的。每一方必须发送一个SYN，且这些SYN必须传递给对方。这需要每一方使用一个对方熟知的端口作为本地端口。同时打开的状态迁移图不同于正常状态的三次握手，该情况下需要进行4次握手。如图：

![][6]



图6.同时打开，4次握手建立连接

#### 4.同时关闭

我们在以前讨论过一方（通常但不总是客户方）发送第一个FIN执行主动关闭。双方都执行主动关闭也是可能的，TCP协议也允许这样的同时关闭（simultaneous close）。在同时关闭的时候，双方都进入time_wait状态，如图：

![][7]



图7.TCP同时关闭连接状态转换图

## 六.TCP服务器设计

大多数的TCP服务器进程是并发的。当一个新的连接请求到达服务器时，服务器接受这个请求，并调用一个新进程来处理这个新的客户请求。

#### 1. 接入连接请求队列

一个并发服务器调用一个新的进程来处理每个客户请求，因此处于被动连接请求的服务器应该始终准备处理下一个呼入的连接请求。那正是使用并发服务器的根本原因。但仍有可能出现当服务器在创建一个新的进程时，或操作系统正忙于处理优先级更高的进程时，到达多个连接请求。当服务器正处于忙时，TCP是如何处理这些呼入的连接请求？TCP有这样一个队列来临时存放这些连接-接入连接请求队列。处理方式如下：

* 正等待连接请求的一端有一个固定长度的连接队列，该队列中的连接已被TCP接受（即三次握手已经完成），但还没有被应用层所接受。注意区分TCP接受一个连接是将其放入这个队列，而应用层接受连接是将其从该队列中移出。
* 应用层将指明该队列的最大长度，这个值通常称为积压值 (backlog)。
* 当一个连接请求（SYN）到达时， TCP使用一个算法，根据当前连接队列中的连接数来确定是否接收这个连接。积压值说明的是TCP监听的端口已被TCP接受而等待应用层接受的最大连接数。
* 如果对于新的连接请求，该TCP监听的端口的连接队列中还有空间，TCP模块将对SYN进行确认并完成连接的建立。此时，应用层不一定知道该新的连接，如果对方发送数据，这些数据将放入缓冲队列中。
* 如果对于新的连接请求，连接队列中已没有空间，TCP将不理会收到的SYN。也不发回任何报文段（即不发回 RST）。如果应用层不能及时接受已被TCP接受的连接，这些连接可能占满整个连接队列，客户的主动打开最终将超时。

[1]: ./img/301894-b2d925fb84256996.png
[2]: ./img/301894-946f00197547e420.png
[3]: ./img/301894-c645a9783bb660c4.png
[4]: ./img/301894-c0a08ffc22cf9ea7.png
[5]: ./img/301894-c0d9a3863b0e82ac.png
[6]: ./img/301894-b323d1a8fd95b091.png
[7]: ./img/301894-397d1e6274c502f8.png