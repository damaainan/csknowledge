## 闲谈GET和POST

2018.10.03 10:44

来源：[https://www.jianshu.com/p/7d762836152b](https://www.jianshu.com/p/7d762836152b)


          
## 0x00 前言

哇~~ 又有好长一算时间没有发过文章了，被骂了，╥﹏╥... 。别打我，我回到正题还不行嘛。

首先，我们先来了解一下HTTP请求方法共有多少种。HTTP1.0定义了三种请求方法：GET，POST，HEAD。HTTP1.1在对原有的HTTP1.0兼容的情况下做了一些改进，并新增了五种请求方法：OPTIONS，PUT，DELETE，TRACE和CONNECT。(PS: 我才不会告诉你，新增的方法中 PUT 和 DELETE 在部分服务器上不合理的设置是可以被用来攻击的)

来张表更清晰的看一下。

| # | 请求方法 | 描述 |
| - | - | - |
| 1 | GET | 请求URI(Request-URI)， **`获取返回的数据`** |
| 2 | POST | 向URI **`提交数据`** 进行处理。 |
| 3 | HEAD | 与 GET 相同，但 **`仅返回 HTTP 报头`** ，不返回文档主体。 |
| 4 | OPTIONS | 用于请求获URI标识的资源在请求/响应的通信过程中 **`可以使用的功能选项`** |
| 5 | PUT | 请求服务器去把请求里的实体 **`存储`** 在请求URI |
| 6 | DELETE | 与PUT过程相反，执行的是 **`删除`** |
| 7 | TRACE | **`回显服务器收到的请求`** ，主要用于测试或诊断 |
| 8 | CONNECT | 把请求连接转换到 **`透明的 TCP/IP 通道`** |


这里我就主要讲一下这里面最常用的两种方法，GET 和 POST。
## 0x01 GET 请求方法

摘自 参考文档1

The GET method means retrieve whatever information (in the form of an entity) is identified by the Request-URI. If the Request-URI refers to a data-producing process, it is the produced data which shall be returned as the entity in the response and not the source text of the process, unless that text happens to be the output of the process.

The semantics of the GET method change to a "conditional GET" if the request message includes an If-Modified-Since, If-Unmodified-Since, If-Match, If-None-Match, or If-Range header field. A conditional GET method requests that the entity be transferred only under the circumstances described by the conditional header field(s). The conditional GET method is intended to reduce unnecessary network usage by allowing cached entities to be refreshed without requiring multiple requests or transferring data already held by the client.

The semantics of the GET method change to a "partial GET" if the request message includes a Range header field. A partial GET requests that only part of the entity be transferred. The partial GET method is intended to reduce unnecessary network usage by allowing partially-retrieved entities to be completed without transferring data already held by the client.

The response to a GET request is cacheable if and only if it meets the requirements for HTTP caching described in section 13.

```
/test.php
/test.php?name1=value1&name2=value2

```

上面的两种通常都是 GET 请求(PS: 在 POST 的请求同时是可以包含GET请求内容。)，可以看出，如果 GET 想携带参数发出请求的话，使用`?`后面接`参数名=参数值`即可。

需要注意的是


* GET 请求可被缓存
* GET 请求保留在浏览器历史记录中
* GET 请求可被收藏为书签
* GET 请求不应在处理敏感数据时使用(PS: 尝试把账号密码 GET 请求一下？[坏笑])
* GET 请求有长度限制，通常认为是2083字符长度


其实我们最常用的就是 GET 请求了呢。再说另一个最常用的请求--POST
## 0x03 POST 请求方法

The POST method is used to request that the origin server accept the entity enclosed in the request as a new subordinate of the resource identified by the Request-URI in the Request-Line. POST is designed to allow a uniform method to cover the following functions:

The actual function performed by the POST method is determined by the server and is usually dependent on the Request-URI. The posted entity is subordinate to that URI in the same way that a file is subordinate to a directory containing it, a news article is subordinate to a newsgroup to which it is posted, or a record is subordinate to a database.

The action performed by the POST method might not result in a resource that can be identified by a URI. In this case, either 200 (OK) or 204 (No Content) is the appropriate response status, depending on whether or not the response includes an entity that describes the result.

If a resource has been created on the origin server, the response SHOULD be 201 (Created) and contain an entity which describes the status of the request and refers to the new resource, and a Location header.

Responses to this method are not cacheable, unless the response includes appropriate Cache-Control or Expires header fields. However, the 303 response can be used to direct the user agent to retrieve a cacheable resource.

```
POST /demo/post.php HTTP/1.1
Host: soudou.net.cn
name1=value1&name2=value2

```

POST 的请求内容在URI中不做体现，具体是在请求体中存在键值对的对应。

再说一个需要注意：


* POST 请求不会被缓存
* POST 请求不会保留在浏览器历史记录中
* POST 不能被收藏为书签
* POST 请求对数据长度没有要求(PS: 请求内容太大的话，还是分开比较好，不然真的会炸的)


再废话几句，POST 请求方式里面其实也是有很多差异的说。我们常见的有四种 POST 请求方式：application/x-www-form-urlencoded，multipart/form-data，application/json，text/xml。这四种应用的领域也都不尽相同。下面来简单说一下。
### 0x03.1 application/x-www-form-urlencoded

这大概就是 POST 中最常用的提交数据的方式之一了。浏览器的原生`<form>`表单，不设置`enctype`属性，那么就会以`application/x-www-form-urlencoded`方式提交数据。请求类似于下面这样：

```
POST /demo/post.php HTTP/1.1
Host: soudou.net.cn
Content-Type: application/x-www-form-urlencoded;charset=utf-8

name1=value1&name2=value2

```

首先，`Content-Type`被指定为`application/x-www-form-urlencoded`；其次，提交的数据按照`name1=value1&name2=value2`的方式进行编码，name 和 value都进行了 URL 转码。

很多时候，我们用 Ajax 提交数据时，也是使用这种方式。例如 [JQuery][2] 的 Ajax，Content-Type 默认值就是`application/x-www-form-urlencoded;charset=utf-8`。
### 0x03.2 multipart/form-data

这是另一个常见的 POST 数据提交的方式。我们使用这种方式上传文件时，必须让`<form>`表单的`enctype`等于`multipart/form-data`。直接来看一个请求示例：

```
POST /demo/post.php HTTP/1.1
Host: soudou.net.cn
Content-Type:multipart/form-data; boundary=----dddddddddddddddddddd

------dddddddddddddddddddd
Content-Disposition: form-data; name="text"

title
------dddddddddddddddddddd
Content-Disposition: form-data; name="file"; filename="demo.png"
Content-Type: image/png

base64...
------dddddddddddddddddddd--

```

这种方式首先会生成了一个 boundary 用于分割不同的字段，为了避免与提交的内容重复，boundary 通常会很长很复杂。然后 Content-Type 里指明了数据是以 multipart/form-data 来编码，本次请求的 boundary 是什么内容。消息主体里按照字段个数又分为多个结构类似的部分，每部分都是以`--boundary`开始，紧接着是内容描述信息，然后是回车，最后是字段具体内容（文本或二进制）。如果传输的是文件，还要包含文件名和文件类型信息。消息主体最后以`--boundary--`标示结束。关于 multipart/form-data 的详细定义，可以前往 [rfc1867][3] 查看。

这种方式通常被用来上传文件。
### 0x03.3 application/json

这种方案，通常用于提交复杂的结构化数据，特别适合 RESTful 的接口。各大抓包工具如 Chrome Developer Tools、Firefox Developer Tools、Charles，都会以树形结构展示 JSON 数据，非常友好。但有些服务端语言还没有支持这种方式，例如 php 就无法通过 $_POST 对象从上面的请求中获得内容。这时候，需要自己动手处理下：在请求头中 Content-Type 为 application/json 时，从`php://input`里获得原始输入流，再`json_decode`成对象。一些 php 框架已经开始这么做了。
### 0x03.4 text/xml

它是一种使用 HTTP 作为传输协议，XML 作为编码方式的远程调用规范。(唔，开发者你开心就好，反正我是不会用的，据说某些支付系统的数据传输用的就是这个)
## 0x04 后记


码字好累的说，心塞塞。吐个槽都不行嘛~~ 超生气



![][0]


超生气


![][1]


给钱


如果帮到了你就点个赞打个赏吧，我才不会谢谢你。

参考文档


* [HTTP/1.1: Method Definitions][4]
* [HTTP 方法：GET 对比 POST][5]
* [四种常见的 POST 提交数据方式][6]



[2]: http://jquery.com/
[3]: http://www.ietf.org/rfc/rfc1867.txt
[4]: https://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html
[5]: http://www.w3school.com.cn/tags/html_ref_httpmethods.asp
[6]: https://imququ.com/post/four-ways-to-post-data-in-http.html
[0]: https://upload-images.jianshu.io/upload_images/4199473-e42c92d264a51b3b.gif
[1]: https://upload-images.jianshu.io/upload_images/4199473-7ee9509f9ae05960.png