<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>

@if (session('token'))
    <div>{{ session('token') }}</div>
@endif

<div id="msg"></div>
<input type="text" id="text">
<input type="submit" value="发送数据" onclick="song()">
</body>
<script>
    document.querySelector("body").style.fontSize = '28px';
    var msg = document.getElementById("msg");
    var wsServer = 'ws://chat.test:9502/'
    //调用websocket对象建立连接：
    //参数：ws/wss(加密)：//ip:port （字符串）
    var websocket = new WebSocket(wsServer);
    //onopen监听连接打开
    websocket.onopen = function (evt) {
        //websocket.readyState 属性：
        /*
        CONNECTING    0    The connection is not yet open.
        OPEN    1    The connection is open and ready to communicate.
        CLOSING    2    The connection is in the process of closing.
        CLOSED    3    The connection is closed or couldn't be opened.
        */
        msg.innerHTML = websocket.readyState;
    };

    function song(){
        var text = document.getElementById('text').value;
        document.getElementById('text').value = '';

        //向服务器发送数据
        var data = {
            content: text,
            token: 'bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9jaGF0LnRlc3RcL2FwaVwvY2FsbGJhY2siLCJpYXQiOjE1MzIzMzE3NDcsImV4cCI6MTUzMjMzNTM0NywibmJmIjoxNTMyMzMxNzQ3LCJqdGkiOiJWY2J0ZHAzc2gxdld4OUt3Iiwic3ViIjoxLCJwcnYiOiI4N2UwYWYxZWY5ZmQxNTgxMmZkZWM5NzE1M2ExNGUwYjA0NzU0NmFhIn0.MRfiAJWqiADeHMdCOIM5Xux32NJ8yKzlT7PWbZT0eos'
        };
        websocket.send(JSON.stringify(data));

    }
    //监听连接关闭
    //    websocket.onclose = function (evt) {
    //        console.log("Disconnected");
    //    };

    //onmessage 监听服务器数据推送
    websocket.onmessage = function (evt) {
        msg.innerHTML += evt.data +'<br>';
//        console.log('Retrieved data from server: ' + evt.data);
    };
    //监听连接错误信息
    //    websocket.onerror = function (evt, e) {
    //        console.log('Error occured: ' + evt.data);
    //    };

</script>
</html>