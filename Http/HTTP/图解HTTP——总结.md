## 图解HTTP——总结

来源：[https://segmentfault.com/a/1190000014572024](https://segmentfault.com/a/1190000014572024)

 来源书籍《图解HTTP》学习总结。 

## TCP/IP协议族

协议中存在各式各样的内容。 从电缆的规格到 IP 地址的选定方法、寻找异地用户的方法、 双方建立通信的顺序， 以及 Web 页面显示需要处理的步骤，![][0]

1、TCP/IP 的分层管理：应用层、 传输层、 网络层和数据链路层。

（1）、应用层：应用层决定了向用户提供应用服务时通信的活动，如：FTP（File Transfer Protocol， 文件传输协议） 和 DNS（Domain Name System， 域名系统）服务，HTTP协议；

（2）、传输层：对上层应用层， 提供处于网络连接中的两台计算机之间的`数据传输`，`TCP`（Transmission Control Protocol，传输控制协议）和`UDP`（User Data Protocol， 用户数据报协议）；

（3）、网络层：用来`处理`在网络上流动的`数据包`。该层规定了通过怎样的路径（所谓的 **`传输路线`** ）到达对方计算机，并把数据包传送给对方；

（4）、链路层（又名数据链路层， 网络接口层：用来处理连接网络的硬件部分。包括控制操作系统、硬件的设备驱动、NIC（Network Interface Card，网络适配器，即网卡），及光纤等 **`物理可见部分`** 。2、TCP/IP 通信传输流

![][1]

TCP/IP协议族进行网络通信时，会通过分层顺序与对方进行通信。发送端从应用层往下走，接收端则往应用层往上走。![][2]

发送端在层与层之间传输数据时，每经过一层时必定会被打上一个该层所属的首部信息。反之，接收端在层与层传输数据时，每经过一层时会把对应的首部消去。

3、与 HTTP 关系密切的协议：IP、TCP、DNS

1）IP协议：（Internet Protocol）协议位于 **`网络层`** ，作用是把各种数据包传送给对方。其中两个重要的条件是 IP 地址和 MAC地址（Media Access Control Address）。IP 地址指明了节点被分配到的地址，MAC 地址是指网卡所属的固定地址。IP 地址可以和 MAC 地址进行配对。IP 地址可变换，但 MAC地址基本上不会更改。

![][3]

2）TCP协议：位于 **`传输层`** ， 提供可靠的 **`字节流服务`** ,TCP 协议为了更容易传送大数据才把数据分割， 而且 TCP 协议能够确认数据最终是否送达到对方。

TCP 协议采用了三次握手three-way handshaking）策略,握手过程中使用了 TCP 的标志 —— SYN（synchronize）和 ACK（acknowledgement）。

![][4]

3）负责域名解析的 DNS 服务：和 HTTP 协议一样位于应用层的协议。它提供域名到 IP 地址之间的解析服务。DNS 协议提供通过域名查找 IP 地址，或逆向从 IP 地址反查域名的服务。

![][5]

HTTP 协议的通信过程：

![][6]

4、URI（统一资源标识符）和 URL（Uniform Resource Locator，统一资源定位符）

URI 用字符串标识某一互联网资源，而 URL 表示资源的地点（互联网上所处的位置） 。可见 URL 是 URI 的子集。URI格式如下：

![][7] 

如下URI例子：

ftp://ftp.is.co.za/rfc/rfc1808.txt

[http://www.ietf.org/rfc/rfc23...][43]

ldap://[2001:db8::7]/c=GB?objectClass?one

mailto:John.Doe@example.com

news:comp.infosystems.www.servers.unix

tel:+1-816-555-1212

telnet://192.0.2.16:80/

urn:oasis:names:specification:docbook:dtd:xml:4.1.2

## 简单的 HTTP 协议

1、HTTP 协议用于客户端和服务器端之间的通信

请求网页资源得例子：

![][8]
 **`请求报文`** ：是由请求方法、请求 URI、协议版本、可选的请求首部字段和内容实体构成的。

![][9] 
 **`响应报文`** ：基本上由协议版本、状态码（表示请求成功或失败的数字代码）、用以解释状态码的原因短语、可选的响应首部字段以及实体主体构成。稍后我们会对这些内容进行详细说明。

![][10]

HTTP 是一种不保存状态，即无状态（stateless）协议。不具备保存之前发送过的请求或响应的功能。为了实现期望的保持状态功能，于是引入了 Cookie 技术。2、请求 URI 定位资源

HTTP 协议使用 URI 定位互联网上的资源。指定请求URI得方式：（ 如果不是访问特定资源而是对服务器本身发起请求，可以用一个 * 来代替请求 URI。）

![][11]

3、告知服务器意图的 HTTP 方法

1）、 **`GET`** ：获取资源,用来请求访问已被 URI 识别的资源;

2）、 **`POST`** ：传输实体主体；

3）、PUT：传输文件——鉴于 HTTP/1.1 的 PUT 方法自身不带验证机制，任何人都可以上传文件 , 存在安全性问题， 因此一般的 Web 网站不使用该方法；

4）、 **`HEAD`** ：获得报文首部,和 GET 方法一样，只是不返回报文主体部分。用于确认URI 的有效性及资源更新的日期时间等；

5）、 **`DELETE`** ：删除文件，与 PUT 相反的方法。按请求 URI 删除指定的资源；

6）、OPTIONS：询问支持的方法；

7）、TRACE：追踪路径，让 Web 服务器端将之前的请求通信环回给客户端的方法。发送请求时，在 Max-Forwards 首部字段中填入数值，每经过一个服务器端就将该数字减 1，当数值刚好减到 0 时，就停止继续传输，最后接收到请求的服务器端则返回状态码 200 OK 的响应。客户端通过 TRACE 方法可以查询发送出去的请求是怎样被加工修改 / 篡改的。不常用易引发XST（Cross-Site Tracing， 跨站追踪）攻击；

8）、CONNECT：要求用隧道协议连接代理，要求在与代理服务器通信时建立隧道，实现用隧道协议进行 TCP 通信。主要使用 SSL（Secure Sockets Layer，安全套接层）和 TLS（Transport Layer Security，传输层安全）协议把通信内容加密后经网络隧道传输。

![][12]

4、持久连接节省通信量

1）、持久连接

HTTP 协议的初始版本中，每进行一次 HTTP 通信就要断开一次 TCP连接。每次的请求都会造成无谓的 TCP 连接建立和断开，增加通信量的开销。为解决上述 TCP 连接的问题，HTTP/1.1 和一部分的 HTTP/1.0 想出了持久连接（HTTP Persistent Connections，也称为 HTTP keep-alive 或 HTTP connection reuse）的方法。持久连接的特点是，只要任意一端没有明确提出断开连接，则保持 TCP 连接状态。![][13]

2）、管线化

从前发送请求后需等待并收到响应， 才能发送下一个请求。管线化技术出现后，不用等待响应亦可直接发送下一个请求![][14]

5、使用 Cookie 的状态管理

Cookie 会根据从服务器端发送的响应报文内的一个叫做 Set-Cookie 的首部字段信息， 通知客户端保存 Cookie。当下次客户端再往该服务器发送请求时，客户端会自动在请求报文中加入 Cookie 值后发送出去。服务器端发现客户端发送过来的 Cookie 后，会去检查究竟是从哪一个客户端发来的连接请求，然后对比服务器上的记录，最后得到之前的状态信息。![][15]

![][16]

## HTTP 报文内的 HTTP信息

1、HTTP 报文

用于 HTTP 协议交互的信息被称为 HTTP 报文。

请求端（客户端）的HTTP 报文叫做请求报文，响应端（服务器端）的叫做响应报文。TTP 报文本身是由多行（用 CR+LF 作换行符）数据构成的字符串文本。并不一定要有报文主体。请求行：包含用于请求的方法， 请求 URI 和 HTTP 版本。

状态行：包含表明响应结果的状态码， 原因短语和 HTTP 版本。

首部字段：包含表示请求和响应的各种条件和属性的各类首部。一般有 4 种首部，分别是：通用首部、请求首部、响应首部和实体首部。

![][17]

![][18]

![][19]

2、编码提升传输速率

可以在传输过程中通过编码提升传输速率。通过在传输时编码，能有效地处理大量的访问请求。当传输中进行编码操作时，实体主体的内容发生变化，将会导致它和报文主体产生差异。通常， 报文主体等于实体主体。报文（message）：是 HTTP 通信中的基本单位，由 8 位组字节流（octet sequence，其中 octet 为 8 个比特）组成，通过 HTTP 通信传输。

实体（entity）：作为请求或响应的有效载荷数据（补充项）被传输，其内容由实体首部和实体主体组成。

1）、压缩传输的内容编码

常用的内容编码有以下几种。

gzip（GNU zip）

compress（UNIX 系统的标准压缩）

deflate（zlib）

identity（不进行编码）

2）、分割发送的分块传输编码

分块传输编码会将实体主体分成多个部分（块）。每一块都会用十六进制来标记块的大小，而实体主体的最后一块会使用“0(CR+LF)”来标记。

3、发送多种数据的多部分对象集合

就是利用 MIME 来描述标记数据类型。而在 MIME 扩展中会使用一种称为多部分对象集合（Multipart）的方法，来容纳多份不同类型的数据。多部分对象集合包含的对象如下。

multipart/form-data:在 Web 表单文件上传时使用。

multipart/byteranges:状态码 206（Partial Content，部分内容）响应报文包含了多个范围的内容时使用。

在 HTTP报文中使用多部分对象集合时， 需要在首部字段里加上Content-type。使用 boundary字符串来划分多部分对象集合指明的各类实体。在boundary字符串指定的各个实体的起始行之前插入“--”标记（例如： --AaB03x、 --THIS_STRING_SEPARATES） ， 而在多部分对象集合对应的字符串的最后插入“--”标记（例如： --AaB03x--、 --THIS_STRING_SEPARATES--） 作为结束。

![][20]

4、获取部分内容的范围请求

执行范围请求时，会用到首部字段 Range （Range: bytes=-3000, 5000-7000，7000-）来指定资源的 byte 范围。响应会返回状态码为 206 Partial Content 的响应报文。另外，对于多重范围的范围请求， 响应会在首部字段 ContentType 标明 multipart/byteranges 后返回响应报文。如果服务器端无法响应范围请求，则会返回状态码 200 OK 和完整的实体内容。

![][21]

5、内容协商返回最合适的内容

## 返回结果的 HTTP 状态码

状态码的职责是当客户端向服务器端发送请求时，描述返回的请求结果。 如 200 OK，以 3 位数字和原因短语组，数字中的第一位指定了响应类别， 后两位无分类。成。![][22]

1、2XX成功：的响应结果表明请求被正常处理了。

1)、200 ok：请求成功并根据方法的不同，返回不同的实体；

2)、204 No Content：该状态码代表服务器接收的请求已成功处理，但在返回的响应报文中不含实体的主体部分。无资源可返回；

3)、206 Partial Content：范围请求后，服务器成功执行了该请求；

2、3XX重定向：表明浏览器需要执行某些特殊的处理以正确处理请求。

1)、 301 Moved Permanently：永久性重定向，表示请求的资源已被分配了新的 URI，以后应使用资源现在所指的 URI。

2)、302 Found：临时重定向，该状态码表示请求的资源已被分配了新的 URI，希望用户（本次）能使用新的 URI 访问。

3)、303 See Other:由于请求对应的资源存在着另一个 URI，应使用 GET方法定向获取请求的资源。303 状态码和 302 Found 状态码有着相同的功能，但 303 状态码明确表示客户端应当采用 GET方法获取资源，这点与 302 状态码有区别。

当 301、 02、303响应状态码返回时，几乎所有的浏览器都会把POST 改成 GET，并删除请求报文内的主体，之后请求会自动再次发送。301、302 标准是禁止将 POST 方法改变成 GET 方法的，但实际使用时大家都会这么做。4)、304 Not Modified:表示客户端发送附带条件的请求 2 时，服务器端允许请求访资源，但未满足条件的情况。 (附带条件的请求是指采用 GET方法的请求报文中包含 If-Match， If-ModifiedSince， If-None-Match， If-Range， If-Unmodified-Since 中任一首部。)

5)、307 Temporary Redirect:临时重定向

3、4XX 客户端错误

1)、400 Bad Request：表示请求报文中存在语法错误。

2)、401 Unauthorized：表示发送的请求需要有通过 HTTP 认证（BASIC 认证、DIGEST 认证）的认证信息。另外若之前已进行过 1 次请求， 则表示用户认证失败。返回含有 401 的响应必须包含一个适用于被请求资源的 WWW-Authenticate 首部用以质询（challenge） 用户信息。

3)、403 Forbidden：表明对请求资源的访问被服务器拒绝了。

4)、404 Not Found：表明服务器上无法找到请求的资源。 

4、5XX 服务器错误

1)、500 Internal Server Error：表明服务器端在执行请求时发生了错误。有可能是 Web应用存在的 bug 或某些临时的故障。

2)、503 Service Unavailable：表明服务器暂时处于超负载或正在进行停机维护，现在无法处理请求。

## 与 HTTP 协作的 Web 服务器

一台 Web 服务器可搭建多个独立域名的 Web 网站，也可作为通信路径上的中转服务器提升传输效率。利用虚拟主机（Virtual Host，又称虚拟服务器）的功能,在相同的 IP 地址下，由于虚拟主机可以寄存多个不同主机名和域名的 Web 网站，因此在发送 HTTP 请求时，必须在 Host 首部内完整指定主机名或域名的 URI。1、通信数据转发程序 ： 代理、网关、隧道

1）、 **`代理`** ：是一种有转发功能的应用程序，代理不改变请求 URI，会直接发送给前方持有资源的目标服务器。可级联多台代理服务器。需要附加Via 首部字段以标记出经过的主机信息；

代理有多种使用方法，按两种基准分类。 一种是是否使用缓存，另一种是是否会修改报文。

`缓存代理（Caching Proxy）`会预先将资源的副本（缓存）保存在代理服务器上。代理再次接收到对相同资源的请求时，就可以不从源服务器那里获取资源， 而是将之前缓存的资源作为响应返回。

不对报文做任何加工的代理类型被称为`透明代理（Transparent Proxy）`。反之，对报文内容进行加工的代理被称为`非透明代理`。

![][23]

2）、 **`网关`** ：是转发其他服务器通信数据的服务器，接收从客户端发送来的请求时，它就像自己拥有资源的源服务器一样对请求进行处理。利用网关能`提高通信的安全性`，因为可以在客户端与网关之间的通信线路上加密以确保连接的安全。 

3）、 **`隧道`** ：是在相隔甚远的客户端和服务器两者之间进行中转，并保持双方通信连接的应用程序。可按要求建立起一条与其他服务器的通信线路，届时使用 SSL等加密手段进行通信。确保客户端能与服务器`进行安全的通信`。

2、保存资源的缓存

 **`缓存`** 是指代理服务器或客户端本地磁盘内保存的`资源副本`。`缓存服务器`是代理服务器的一种，并归类在缓存代理类型中。可避免多次从源服务器转发资源。缓存是有`有效期限`的，缓存失效， 缓存服务器将会再次从源服务器上获取“新”资源。## HTTP 首部

![][24]

1、在请求中， HTTP 报文由方法、 URI、 HTTP 版本、 HTTP 首部字段等部分构成。 

2、在响应中， HTTP 报文由HTTP 版本、 状态码（数字和原因短语） 、HTTP 首部字段 3 部分构成。

使用首部字段是为了给浏览器和服务器提供报文主体大小、 所使用的语言、 认证信息等内容。

首部字段结构：首部字段名和字段值构成的， 中间用冒号“:” 分隔,eg:Content-Type: text/html,Keep-Alive:timeout=15, max=100。1、首部字段类型

通用首部字段（General Header Fields）：请求报文和响应报文两方都会使用的首部。

请求首部字段（Request Header Fields）：从客户端向服务器端发送请求报文时使用的首部。补充了请求的附加内容、 客户端信息、 响应内容相关优先级等信息。

响应首部字段（Response Header Fields）：从服务器端向客户端返回响应报文时使用的首部。补充了响应的附加内容， 也会要求客户端附加额外的内容信息。

实体首部字段（Entity Header Fields）：针对请求报文和响应报文的实体部分使用的首部。补充了资源内容更新时间等与实体有关的信息。

![][25]

表 6-2： 请求首部字段

![][26]

![][27]

![][28]

2、End-to-end 首部和 Hop-by-hop 首部
 **`端到端首部（End-to-end Header）`** ：会转发给请求 / 响应对应的最终接收目标，且必须保存在由缓存生成的响应中，另外规定它`必须被转发`。
 **`逐跳首部（Hop-by-hop Header）`** ：首部只对单次转发有效，会因通过缓存或代理而不再转发。 使用 hop-by-hop 首部，需提供 Connection 首部字段。只有这8个字段Connection，Keep-Alive，Proxy-Authenticate，Proxy-Authorization，Trailer，TE，Transfer-Encoding，Upgrade

3、 **`通用首部字段`** 

1）、Cache-Control：eg Cache-Control: private, max-age=0, no-cache,利用缓存代理服务器进行缓存的管理。

![][29]

![][30]
 **`no-cache指令`** ：客户端请求：不接受缓存过的响应，服务器端的响应：包含该字段，表示，询问后缓存服务器不能缓存该资源，no-cache=Location：响应指令中指定该参数。客户端在接收到这个被指定参数值的首部字段对应的响应报文后， 不能使用缓存。

![][31] 
 **`no-store`**  ：暗示请求（和对应的响应） 或响应中包含机密信息。不能对其进行缓存。
 **`s-maxage`** ：Cache-Control: s-maxage=604800（单位 ：秒），指定缓存期限和认证的指令，和 max-age 指令的相同，只适用于供多位用户使用的公共缓存服务器，当使用 s-maxage 指令后，则直接忽略对 Expires 首部字段及max-age 指令的处理。
 **`max-age`** ：Cache-Control: max-age=604800（单位： 秒）,当缓存时间小于该值值，客户端可接受缓存的值，服务端指定该指令时，小于指定时间不用确认缓存有效性直接返回该缓存。指定 max-age 值为 0，那么缓存服务器通常需要将请求转发给源服务器。
 **`min-fresh`**  ：Cache-Control: min-fresh=60（单位： 秒），请求指令：要求缓存服务器返回至少还未过指定时间的缓存资源。
 **`max-stale`**  ：Cache-Control: max-stale=3600（单位： 秒），请求指令：即使过期缓存但仍处于 max-stale指定的时间内，仍旧会被客户端接收。不指定参数则为永久。
 **`only-if-cached`** ：Cache-Control: only-if-cached，请求指令：仅在接受缓存服务器内的资源，若缓存服务器无该资源则返回 504 Gateway Timeout。
 **`must-revalidate`** ：Cache-Control: must-revalidate，响应指令：必须向服务器确认资源的有效性，若服务器无响应返回 504 Gateway Timeout，使用该指令会忽略 max-stale。
 **`proxy-revalidate`** ：Cache-Control: proxy-revalidate，请求指令，要求缓存服务器，在响应之前必须确认缓存的有效性。
 **`no-transform`** ：Cache-Control: no-transform，请求和相应中，缓存不能改变实体主体的媒体类型。

Cache-Control 扩展：cache-extension token，扩展Cache-Control的指令。2）、Connection：作用有，控制不再转发给代理的首部字段，管理持久连接。

不再转发给代理的首部字段——Hop-by-hop 首部，如图，Upgrade首部字段。

![][32]

管理持久连接：Close指令:关闭该连接，Keep-Alive:HTTP/1.1 之前的 HTTP 版本的默认连接都是非持久连接,使用该字段建立持久连接。

3）、Date：创建HTTP报文的日期和时间。

4）、Pragma： HTTP/1.1 之前版本的历史遗留字段，Pragma: no-cache，可使用Cache-Control:no-cache替代。

5）、Trailer:事先说明在报文主体后记录了哪些首部字段。

![][33]

6）、Transfer-Encoding：规定传输报文主体时采用的编码方式。HTTP/1.1 的传输编码方式仅对分块传输编码有效。Transfer-Encoding: chunked。分块传输。

7）、Upgrade：用于检测 HTTP 协议及其他协议是否可使用更高的版本进行通信，其参数值可以用来指定一个完全不同的通信协议。

Upgrade 首部字段产生作用的 Upgrade 对象仅限于客户端和邻接服务器之间。因此，使用首部字段 Upgrade时，还需要额外指定Connection:Upgrade。![][34]

8）、Via ：为了追踪客户端与服务器之间的请求和响应报文的传输路径，还可避免请求回环的发生。。每通过一个代理服务器或网管追加一个via首部字段内容（服务器信息）。经常会和 TRACE 方法一起使用。代理服务器接收到TRACE 方法发送过来的请求（Max-Forwards: 0） 时，代理服务器就不能再转发该请求了，代理服务器会将自身的信息附加到 Via 首部后， 返回该请求的响

应。

9）、Warning：告知用户一些与缓存相关的问题的警告。结构：Warning: 警告码“[警告内容]”([日期时间]), eg:Warning: 113 gw.hackr.jp:8080 "Heuristic expiration" Tue, 03

![][35]

4、 **`请求首部字段`** ：`客户端往服务器端`发送请求报文中所使用的字段

1）、Accept：用户代理能够处理的`媒体类型`及媒体类型的相对优先级。 type/subtype 结构一次指定多种媒体类型。用分号分隔多种文件类型，用q=权重值(0~1),来表示优先级。1最大，默认为1。

```LANG

文本文件：text/html, text/plain, text/css ...，application/xhtml+xml, application/xml ...
图片文件：image/jpeg, image/gif, image/png ...
视频文件：video/mpeg, video/quicktime ...
应用程序使用的二进制文件：application/octet-stream, application/zip ...

```

2）、Accept-Charset：支持的`字符集`及字符集的相对优先顺序（权重 q 值表示）。eg:Accept-Charset: iso-8859-5, unicode-1-1;q=0.8

3）、Accept-Encoding：支持的`内容编码`及内容编码的优先级顺序（权重 q 值表示），也可用“*”,指定任意彪马格式。

```LANG

gzip：由文件压缩程序 gzip（GNU zip） 生成的编码格式RFC1952），采用 Lempel-Ziv 算法（LZ77）及 32 位循环冗余校验（Cyclic Redundancy Check，称 CRC）。
compress：由 UNIX 文件压缩程序 compress 生成的编码格式， 采用 LempelZiv-Welch 算法（LZW） 。
deflate： 组合使用 zlib 格式（RFC1950） 及由 deflate 压缩算法（RFC1951） 生成的编码格式。
identity：不执行压缩或不会变化的默认编码格式

```

4）、Accept-Language：Accept-Language: zh-cn,zh;q=0.7,en-us,en;q=0.3，告知服务器用户代理能够处理的自然语言集（指中文或英文等）

5）、Authorization：认证信息。

![][36]

6）、Expect：期望服务器出现指定的行为。若不能服务器返回错误状态码 417 Expectation Failed。

7）、From：告知服务器使用用户代理的用户的电子邮件地址。目的就是为了显示搜索引擎等用户代理的负责人的电子邮件联系方式。 使用代理时，应尽可能包含 From 首部字段（但可能会因代理不同， 将电子邮件地址记录在 User-Agent 首部字段内）。

8）、Host：虚拟主机运行在同一个 IP 上，因此使用首部字段 Host 加以区分，告知服务器，请求的资源所处的互联网`主机名`和`端口号`， HTTP/1.1规范`必须`请求首部信息。服务器未设定主机名，那直接发送一个`空值`即可。

形如 If-xxx 这种样式的`请求首部字段`， 都可称为`条件请求`。只有判断指定条件为真时，才会执行请求。否则返回错误412 Precondition Failed。9）、If-Match：告知服务器匹配资源所用`实体标记（ETag）`值比较。If-Match: "123456"。

10）、If-Modified-Since：判断资源指定时间之后是否已更新。若已更新则处理该请求，否则返回状态码 304 Not Modified，用于确认代理或客户端拥有的本地资源的有效性。获取资源的更新日期时间，可通过确认首部字段 Last-Modified 来确定。If-Modified-Since: Thu, 15 Apr 2004 00:00:00 GMT。

11）、If-None-Match： 判断字段值与`ETag`值不一致时，处理该请求。在 GET 或 HEAD 方法中使用首部字段 If-None-Match 可获取最新的资源。与 If-Modified-Since 有些类似，与 If-Match 首部字段相反。

12）、If-Range：告知服务器若指定的 IfRange 字段值（`ETag 值`或者`时间`）和请求资源的 ETag 值或时间相一致时，则作为范围请求处理。反之，则返回全体资源。

13）、If-Unmodified-Since：If-Unmodified-Since: Thu, 03 Jul 2012 00:00:00 GMT，指定时间之后未更新才处理该请求。否则返回 412 Precondition Failed。

14）、Max-Forwards：Max-Forwards: 10（10进制整数），TRACE 方法或 OPTIONS 方法，发送包含首部字段 MaxForwards 的请求时，指定可经过的服务器最大数目。通过一个代理服务器减一重新赋值，为0时，处理请求，返回响应。

15）、Proxy-Authorization：Proxy-Authorization: Basic dGlwOjkpNLAGfFY5，代理服务器发来的认证信息。

16）、Range：Range: bytes=5001-10000，请求资源的范围信息。成功处理请求之后返回状态码为 206 Partial Content 的响应，无法处理该范围请求时，则返回状态码 200 OK 的响应及全部资源。

17）、Referer：告知服务器请求的原始资源的 URI。

18）、TE：TE: gzip, deflate;q=0.5，客户端能够处理响应的`传输编码方式`及相对优先级。 TE: trailers，分块传输方式。

19）、User-Agent：请求的浏览器和用户代理名称等信息。请求经过代理， 那么中间也很可能被添加上代理服

务器的名称。

![][37]

5、 **`响应首部字段`** ：`服务器端向客户端`返回响应报文中所使用的字段

1）、Accept-Ranges：告知客户端服务器是否能处理范围请求，可处理范围请求：Accept-Ranges:`bytes`，不能处理范围请求： Accept-Ranges:`none`2）、Age：Age: 600（秒）告知客户端， 源服务器在多久前创建了响应

![][38]

3）、ETag：告知客户端实体标识。

资源被缓存时，就会被分配唯一性标识。是一种可将资源以字符串形式做唯一性标识的方式，资源更新时，ETag 值也需要更新。 

强 ETag 值：不论实体发生多么细微的变化都会改变其值。

弱 ETag 值：只用于提示资源是否相同。ETag: "usagi-1234"，产生差异时最开始的地方附加 W/：ETag: W/"usagi-1234"4）、Location：将响应接收方引导至某个与请求 URI 位置不同的资源。配合3xx ： Redirection，提供重定向的URI

5）、Proxy-Authenticate：代理服务器所要求的认证信息发送给客户端。

6）、Retry-After：告知客户端应该在多久之后再次发送请求。配合状态码503 Service Unavailable。或 3xx Redirect。Retry-After: 120（秒）/ 日期

7）、Server：告知客户端当前服务器上安装的 HTTP 服务器应用程序的信息。Server: Apache/2.2.17 (Unix)，Server: Apache/2.2.6 (Unix) PHP/5.2.5

8）、Vary：可对缓存进行控制。`源服务器`会向`代理服务器`传达关于本地缓存使用方法的命令。仅对请求中含有相同 Vary 指定首部字段的请求返回缓存。 否则即使资源相同也要向服务器重新获取资源。

![][39]

9）、WWW-Authenticate：知客户端适用于访问请求 URI 所指定资源的`认证方案`（Basic 或是 Digest）和带参数提示的`质询`（challenge）。状态码 401 Unauthorized 响应中，肯定带有首部字段 WWW-Authenticate。WWW-Authenticate: Basic realm="Usagidesign Auth"

6、 **`实体首部字段`** ：请求报文和响应报文中的实体部分所使用的首

部

1）、Allow：通知客户端能够支持 Request-URI 指定资源的所有 HTTP 方法。服务器接收到不支持的 HTTP 方法时，会以状态码405 Method Not Allowed 作为响应返回。Allow: GET, HEAD。

2）、Content-Encoding：告知客户端服务器对实体的主体部分选用的内容编码方式。内容编码方式： gzip，compress，deflate，identity

3）、Content-Language：告知客户端，实体主体使用的自然语言。Content-Language: zh-CN

4）、Content-Length ：表明了实体主体部分的大小（单位是字节）。Content-Length: 15000

5）、Content-Location：与报文主体部分相对应的 URI。和首部字段 Location 不同，Content-Location 表示的是报文主体返回资源对应的 URI。

6）、Content-MD5：目的在于检查报文主体在传输过程中是否保持完整，以及确认传输到达。Content-MD5: OGFkZDUwNGVhNGY3N2MxMDIwZmQ4NTBmY2IyTY==，对报文主体执行 MD5 算法获得的 128 位二进制数，再通过Base64 编码后将结果写入 Content-MD5 字段值。由于 HTTP 首部无法记录二进制值，所以要通过 Base64 编码处理。为确保报文的有效性，作为接收方的客户端会对报文主体再执行一次相同的 MD5 算法。计算出的值与字段值作比较后，即可判断出报文主体的准确性。

![][40]

7）、Content-Range：告知客户端作为响应返回的实体的哪个部分符合范围请求。Content-Range: bytes 5001-10000/10000（字节为单位）

8）、Content-Type：实体主体内对象的媒体类型。

9）、Expires：首部字段 Expires 会将资源失效的日期告知客户端。

10）、Last-Modified：指明资源最终修改的时间。

7、 **`为Cookie 服务`** 的首部字段

Cookie 的工作机制是用户识别及状态管理。 把一些数据临时写入用户的计算机内。 接当用户访问该Web网站时， 可通过通信方式取回之前发放的Cookie。调用 Cookie 时，由于可校验 Cookie 的有效期，以及发送方的域、路径、 协议等信息，![][41]

1)、 **`Set-Cookie`** : status=enable; expires=Tue, 05 Jul 2011 07:26:31 GMT; path

表 6-9： Set-Cookie 字段的属性

![][42]

expires:浏览器可发送 Cookie 的有效期。省略 expires 属性时，其有效期仅限于维持浏览器会话（Session）时间段内。

一旦 Cookie 从服务器端发送至客户端，服务器端就不存在可以显式删除 Cookie 的方法。但可通过`覆盖已过期的 Cookie`，实现对客户端 Cookie 的实质性删除操作。path:限制指定 Cookie 的发送范围的`文件目录`。

domain:`指定的域名`可做到与结尾匹配一致,当指定 example.com 后，除 example.com 以外，www.example.com或 www2.example.com 等都可以发送 Cookie。

secure:用于限制 Web 页面仅在`HTTPS`安全连接时，才可以发送 Cookie。Set-Cookie: name=value; secure

HttpOnly:它使 JavaScript 脚本无法获得 Cookie。其主要目的为防止跨站脚本攻击（Cross-site scripting， XSS）对 Cookie 的信息窃取。Set-Cookie: name=value; HttpOnly

2）、 **`Cookie`** ：Cookie: status=enable，会告知服务器，当客户端想获得 HTTP 状态管理支持时，就会在请求中包含从服务器接收到的 Cookie。

8、 **`其他首部字段`** 

HTTP 首部字段是可以自行扩展的。所以在 Web 服务器和浏览器的应用上，会出现各种非标准的首部字段。X-Frame-Options：X-Frame-Options: DENY（拒绝显示），HTTP`响应首部`，用于控制网站内容在其他 Web 网站的 Frame 标签内的显示问题。目的是为了防止点击劫持（clickjacking）攻击。SAMEORIGIN：仅同源域名下的页面（Top-level-browsingcontext） 匹配时可以显示。

X-XSS-Protection： HTTP`响应首部`，它是针对跨站脚本攻击（XSS）的一种对策，用于控制浏览器 XSS 防护机制的开关。0，关，1：开

DNT： HTTP 请求首部， 拒绝个人信息被收集，0：同意被追踪，1：拒绝被追踪。

P3P： HTTP 响应首部，可以让 Web 网站上的个人隐私变成一种`仅供程序可理解的形式`，以达到保护用户隐私的目的。

[43]: http://www.ietf.org/rfc/rfc2396.txt
[0]: ./img/bV89KH.png
[1]: ./img/bV89Si.png
[2]: ./img/bV89UR.png
[3]: ./img/bV896y.png
[4]: ./img/bV9a5A.png
[5]: ./img/bV9a6F.png
[6]: ./img/bV9a61.png
[7]: ./img/bV9bdO.png
[8]: ./img/bV9bkI.png
[9]: ./img/bV9bkd.png
[10]: ./img/bV9blf.png
[11]: ./img/bV9btR.png
[12]: ./img/bV9b8Z.png
[13]: ./img/bV9cbQ.png
[14]: ./img/bV9cdZ.png
[15]: ./img/bV9chF.png
[16]: ./img/bV9chQ.png
[17]: ./img/bV9cj4.png
[18]: ./img/bV9crM.png
[19]: ./img/bV9crU.png
[20]: ./img/bV9hGP.png
[21]: ./img/bV9hLV.png
[22]: ./img/bV9hNs.png
[23]: ./img/bV9wVU.png
[24]: ./img/bV9w1q.png
[25]: ./img/bV9w5K.png
[26]: ./img/bV9w54.png
[27]: ./img/bV9w6a.png
[28]: ./img/bV9w6o.png
[29]: ./img/bV9xoL.png
[30]: ./img/bV9xoU.png
[31]: ./img/bV9xC6.png
[32]: ./img/bV9xXZ.png
[33]: ./img/bV9yqM.png
[34]: ./img/bV9zul.png
[35]: ./img/bV9ytd.png
[36]: ./img/bV9ztS.png
[37]: ./img/bV9y9m.png
[38]: ./img/bV9zb0.png
[39]: ./img/bV9znq.png
[40]: ./img/bV9zB2.png
[41]: ./img/bV9z73.png
[42]: ./img/bV9CW4.png