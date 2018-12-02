## 不按顺序来的 TCP 包

来源：[https://strcpy.me/index.php/archives/789/](https://strcpy.me/index.php/archives/789/)

时间 2018-11-29 21:13:00

 
关于 TCP 建立连接和断开连接的流程，很多人都能大致说出来，可以参考[协议森林][3]
 
正常的数据传输是在三次握手结束之后进行的，但是如果打破了这个流程，数据传输仍然可能成功，而部分防火墙 IDS 就可能被绕过，下面的两个例子来自 https://github.com/kirillwow/ids_bypass。
 
## CVE-2018-6794

```
# 客户端开始三次握手 发送 SYN
Client    ->  [SYN] [Seq=0 Ack=0]           ->  Evil Server
# 服务器端正常的响应了 SYN-ACK
Client    <-  [SYN, ACK] [Seq=0 Ack=1]      <-  Evil Server
# 但是服务器端在握手结束之前就发送了 PSH，里面包含了一些数据
Client    <-  [PSH, ACK] [Seq=1 Ack=1]      <-  Evil Server
# 服务器端主动关闭了连接
Client    <-  [FIN, ACK] [Seq=83 Ack=1]     <-  Evil Server
# 三次握手完成
Client    ->  [ACK] [Seq=1 Ack=84]          ->  Evil Server
# 客户端正常的发送数据
Client    ->  [PSH, ACK] [Seq=1 Ack= 4]     ->  Evil Server
```
 
Suricata IDS 在 4.0.4 版本之前存在这个问题
 
## RST 导致的绕过
 
有些 Windows 客户端在收到 RST 包之后，如果紧接着又收到了其他的 TCP 数据，那仍然是可以读取和处理的，有些 IDS 正确处理了这个问题，有的在收到 RST 包之后就停止了检查 TCP 包。

```
# Client starts a TCP 3-way handshake
Client    ->  [SYN] [Seq=0 Ack=0]           ->  Evil Server
# Server responses with TCP RST
Client    <-  [RST, ACK] [Seq=0x0 Ack=1]    <-  Evil Server
# And SYN-ACK shortly after RST
Client    <-  [SYN, ACK] [Seq=1 Ack=1]      <-  Evil Server
           ... 三次握手继续 ...
```
 
Suricata IDS（全版本？）存在这个问题。对于 UDP 数据包，也有一个类似的问题。
 
## 应用
 
某些云服务器厂商会实时的去过滤每台机器的 HTTP 请求的域名，也就是 Host 字段，一旦发现是没有[[(备)]]案的，就会返回一个拦截页面，怎么绕过这个呢。经过测试发现某云应该是不检测 HTTPS的，如果可以让 80 端口重定向到 443，然后设置 HSTS 头，这样基本长时间内浏览器就不会再访问 80 端口了，虽然 SSL SNI 和 证书中也是含有域名信息的。
 
访问 80 端口，发现三次握手是正常进行的，而拦截发生在客户端发送了 HTTP 请求包之后，这也说明，防火墙不是无条件封禁的和屏蔽端口的，而是实时的过滤。如果可以抢在防火墙发包之前发送，那就可以实现重定向了。
 
写了一个 Python 的脚本来完成这个事情

```python
# coding=utf-8
from scapy.all import IP, TCP, send, sniff

SERVER_DOMAIN = "example.me"
SERVER_PORT = 4445

FIN = 0x01
SYN = 0x02
ACK = 0x10


def build_synack(syn):
    seq = 1
    # 确认 SYN
    ack = syn[TCP].seq + 1

    ip = IP(src=syn[IP].dst, dst=syn[IP].src)
    tcp = TCP(
            sport=syn[IP].dport,
            dport=syn[TCP].sport,
            flags="SA",
            seq=seq,
            ack=ack,
            options=[("MSS", 1460)]
    )
    return ip / tcp


def build_finack(syn):
    """
    带重定向指令的包
    """
    seq = 2
    ack = syn[TCP].seq + 1

    ip = IP(src=syn[IP].dst, dst=syn[IP].src)
    tcp = TCP(
            sport=syn[IP].dport,
            dport=syn[TCP].sport,
            flags="FA",
            seq=seq,
            ack=ack,
            options=[("MSS", 1460)]
    )
    resp = b"HTTP/1.1 307 Internal Redirect\r\n" \
           b"Content-Length: 0\r\n" \
           b"Location: https://%s:443\r\n" \
           b"Strict-Transport-Security: max-age=31536000\r\n" \
           b"\r\n" % SERVER_DOMAIN
    return ip / tcp / resp


def build_ack(p):
    seq = 3
    ack = p[TCP].seq + 1

    ip = IP(src=p[IP].dst, dst=p[IP].src)
    tcp = TCP(
            sport=p[IP].dport,
            dport=p[TCP].sport,
            flags="A",
            seq=seq,
            ack=ack,
            options=[("MSS", 1460)]
    )
    return ip / tcp


def handle_packet(p):
    # 如果是 SYN 就回复 SYN-ACK 和 FIN-ACK
    if p[TCP].flags & SYN and not p[TCP].flags & ACK:
        send(build_synack(p))
        print("SYN ACK sent")
        send(build_finack(p))
        send("FIN ACK sent")
    elif p[TCP].flags & FIN and p[TCP].flags & ACK:
        # 如果不 ACK，客户端可能一直重传
        send(build_ack(p))
        send("ACK sent")


if __name__ == "__main__":
    # 对于 TCP 和 SERVER PORT 端口的包，回调 handle_packet 函数
    sniff(filter="tcp and port %d" % SERVER_PORT, prn=handle_packet)
```
 
使用 scapy 框架，监听一个端口，在接收到 SYN 包之后，按照正常的握手流程返回 SYN-ACK，然后不等接收到 ACK 就继续发送 FIN-ACK，告诉客户端我要断开连接了，然后在这个包中包含有重定向的 HTTP 包。
 
在服务器端视角看是这样的
 
![][0]
 
在客户端视角看是这样的
 
![][1]
 
42 号包是代码的重定向，47 号包就是防火墙的重定向，可以看到 TTL 明显不一致，而且 seq 被我们代码扰乱，导致被认为 out-of-order 了。
 
![][2]
 
因为 scapy 是用户态的，防止内核不知道整个连接流程而发送 rst 包，可以使用下面的命令屏蔽掉

```
iptables -A OUTPUT -p tcp --tcp-flags RST RST -s 172.21.0.3 -j DROP
```
 
也有人使用内核模块实现了这个功能
 
https://github.com/ptpt52/hstshack


[3]: http://www.cnblogs.com/vamei/archive/2012/12/16/2812188.html
[0]: ./img/6nYfAju.jpg
[1]: ./img/iu6zMzF.jpg
[2]: ./img/jAbUFfN.jpg