# shadowsocks源码分析

[![Join the chat at https://gitter.im/lao605/shadowsocks_analysis](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/lao605/shadowsocks_analysis?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

This work is licensed under a [Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License](http://creativecommons.org/licenses/by-nc-sa/4.0/").

*此份代码仅用作学术交流用途，其他行为带来的后果本人概不负责*

*This copy of code is for study of python only*

## 项目结构：
###### 1、asyncdns.py 用于处理dns请求
###### 2、common.py
###### 3、daemon.py，提供daemon(守护进程)运行机制
###### 4、encrypt.py，处理Shadowsocks协议的加密解密
###### 5、eventloop.py，事件循环，使用select、poll、epoll、kequeue实现IO复用，作者将三种底层实现包装成一个类Eventloop
###### 6、local.py讲，在本地(客户端)运行的程序
###### 7、lru_cache.py，作者实现的一个基于LRU的Key-Value缓存
###### 8、server.py，在远程服务端运行的程序
###### 9、tcprelay.py，实现tcp的转达，用在远程端中使远程和dest连接
###### 10、udprelay.py，实现udp的转达，用于local端处理local和 客户器端的SOCKS5协议通信，用于local端和远程端Shadowsocks协议的通信；用于远程端与local端Shadowsocks协议的通信，用于远程端和dest端(destination)的通信
###### 11、utils.py 工具函数

> 代码质量相当的高，感觉都能达到重用的级别。而且由于作者设计的思想是，一个配置文件，同一段程序，在本地和远程通用，所以其中的代码，常常能够达到一个函数，在本地和服务器有不同的功能这样的效果。

===============================================================
## 核心：eventloop.py，udprelay.py，tcprelay.py，asyndns.py
eventloop使用select、epoll、kqueue等IO复用实现异步处理。优先级为epoll\>kqueue\>select。Eventloop将三种复用机制的add，remove，poll，add_handler，remve_handler接口统一起来，程序员只需要使用这些函数即可，不需要处理底层细节。

后三个文件分别实现用来处理udp的请求，tcp的请求，dns的查询请求，并且将三种请求的处理包装成handler。对于tcp，udp的handler，它们bind到特定的端口，并且将socket交给eventloop，并且将自己的处理函数加到eventloop的handlers；对于dns的handler，它接受来自udp handler和tcp handler的dns查询请求，并且向远程dns服务器发出udp请求；

当eventloop监测到socket的数据，程序就将所有监测到的socket和事件交给所有handler去处理，每个handler通过socket和事件判断自己是否要处理该事件，并进行相对的处理：
##### 当local收到udprelay handler绑定的端口的事件，说明客户端发来请求，local对SOCKS5协议的内容进行处理之后经过加密转发给远程；

<pre>
+----+------+------+----------+----------+----------+
|RSV | FRAG | ATYP | DST.ADDR | DST.PORT |   DATA   |
+----+------+------+----------+----------+----------+
| 2  |  1   |  1   | Variable |    2     | Variable |
+----+------+------+----------+----------+----------+
</pre>

trim-\>
<pre>
+------+----------+----------+----------+
| ATYP | DST.ADDR | DST.PORT |   DATA   |
+------+----------+----------+----------+
|  1   | Variable |    2     | Variable |
+------+----------+----------+----------+
</pre>

-\>encrypt
<pre>
+-------+--------------+
|   IV  |    PAYLOAD   |
+-------+--------------+
| Fixed |   Variable   |
+-------+--------------+
</pre>


##### 当local新建的socket收到连接请求时，说明远程向local发送结果，此时对信息进行解密，并且对shadowsocks协议进行适当加工，发回给客户端

<pre>
+-------+--------------+
|   IV  |    PAYLOAD   |
+-------+--------------+
| Fixed |   Variable   |
+-------+--------------+
</pre>

-\>decrypt

<pre>
+------+----------+----------+----------+
| ATYP | DST.ADDR | DST.PORT |   DATA   |
+------+----------+----------+----------+
|  1   | Variable |    2     | Variable |
+------+----------+----------+----------+
</pre>

-\>add

<pre>
+----+------+------+----------+----------+----------+
|RSV | FRAG | ATYP | DST.ADDR | DST.PORT |   DATA   |
+----+------+------+----------+----------+----------+
| 2  |  1   |  1   | Variable |    2     | Variable |
+----+------+------+----------+----------+----------+
</pre>

##### 当远程端收到udp handler绑定的端口的事件，说明local端发来请求，远程端对信息进行解密并根据dest服务器/端口的协议类型对其发出tcp连接或者udp连接；

<pre>
+-------+--------------+
|   IV  |    PAYLOAD   |
+-------+--------------+
| Fixed |   Variable   |
+-------+--------------+
</pre>

-\>decrypt

<pre>
+------+----------+----------+----------+
| ATYP | DST.ADDR | DST.PORT |   DATA   |
+------+----------+----------+----------+
|  1   | Variable |    2     | Variable |
+------+----------+----------+----------+
</pre>

-\>trim

<pre>
+----------+
|   DATA   |
+----------+
| Variable |
+----------+
</pre>

-\>getaddrinfo-\>tcp/udp
-\>send to dest server via tcp/udp 


##### 当远程新建的socket收到连接请求时，说明dest服务器向远程端发出响应，远程端对其进行加密，并且转发给local端

<pre>
+----------+
|   DATA   |
+----------+
| Variable |
+----------+
</pre>

-\>add

<pre>
+------+----------+----------+----------+
| ATYP | DST.ADDR | DST.PORT |   DATA   |
+------+----------+----------+----------+
|  1   | Variable |    2     | Variable |
+------+----------+----------+----------+
</pre>

-\>encrypt

<pre>
+-------+--------------+
|   IV  |    PAYLOAD   |
+-------+--------------+
| Fixed |   Variable   |
+-------+--------------+
</pre>

-\>send to local

在handler函数里面的基本逻辑就是：
<pre>
if sock == self._server_socket:
self._handle_server()
elif sock and (fd in self._sockets):
self._handle_client(sock)
</pre>

协议解析和构建用的struct.pack()和struct.unpack()

===============================================================
##### asyndns.py实现的是一个DNS服务器，封装得相当的好
1.1、读取/etc/hosts和/etc/resolv.conf文件，如果没有设置，就设置dns服务器为8.8.8.8和8.8.4.4
1.2、收到tcp handler和udp handler的dns请求之后，建立socket并且向远程服务器发送请求，并把（hostname：callback）加入_hostname_to_cb
1.3、收到响应之后触发callback _hostname_to_cb[hostname](#)

###### 作者全程用二进制构建dns报文，非常值得学习

<pre>
# 请求
#                                 1  1  1  1  1  1
#   0  1  2  3  4  5  6  7  8  9  0  1  2  3  4  5
# +--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+
# |                      ID                       |
# +--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+
# |QR|   Opcode  |AA|TC|RD|RA|   Z    |   RCODE   |
# +--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+
# |                    QDCOUNT                    |
# +--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+
# |                    ANCOUNT                    |
# +--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+
# |                    NSCOUNT                    |
# +--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+
# |                    ARCOUNT                    |
# +--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+
</pre>

响应：
<pre>
                                 1  1  1  1  1  1
  0  1  2  3  4  5  6  7  8  9  0  1  2  3  4  5
+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+
|                                               |
/                                               /
/                      NAME                     /
|                                               |
+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+
|                      TYPE                     |
+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+
|                     CLASS                     |
+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+
|                      TTL                      |
|                                               |
+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+
|                   RDLENGTH                    |
+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--|
/                     RDATA                     /
/                                               /
+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+
</pre>

===============================================================
##### lru_cache.py实现的是一个缓存

<pre>
self._store = 
self._time_to_keys = collections.defaultdict(list)
self._keys_to_last_time = 
self._last_visits = collections.deque()
</pre>


###### 1、先找访问时间_last_visits中超出timeout的所有键
###### 2、然后去找_time_to_keys，找出所有可能过期的键
###### 3、因为最早访问时间访问过的键之后可能又访问了，所以要_keys_to_last_time
###### 4、找出那些没被访问过的，然后删除



