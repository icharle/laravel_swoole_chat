<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <script src="https://cdn.bootcss.com/jquery/2.2.1/jquery.min.js"></script>
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
    var wsServer = 'ws://chat.test:9502/';
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
        if (websocket.readyState == 1) {
            msg.innerHTML += '<div style="text-align:center">连接成功...正在进入聊天室...</div>';
        } else {
            msg.innerHTML += '<div style="text-align:center">连接失败</div>';
        }
    };

    function song() {
        var text = document.getElementById('text').value;
        document.getElementById('text').value = '';

        //向服务器发送数据
        var data = {
            content: text,
            user_name: "Pad",
            avatar: "http://thirdqq.qlogo.cn/qqapp/101490714/69831B78F073A55BE8CAA9B8BDED0BA1/100"
        };
        websocket.send(JSON.stringify(data));

    }

    //监听连接关闭
    //    websocket.onclose = function (evt) {
    //        console.log("Disconnected");
    //    };

    //onmessage 监听服务器数据推送
    websocket.onmessage = function (evt) {
        var bigImg = document.createElement("img");
        bigImg.src = evt.data;
        msg.appendChild(bigImg);
    };
    //监听连接错误信息
    //    websocket.onerror = function (evt, e) {
    //        console.log('Error occured: ' + evt.data);
    //    };


    $.ajax({
        url: 'http://chat.test/api/auth/me',
        beforeSend: function (request) {
            request.setRequestHeader("Authorization", "bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9jaGF0LnRlc3RcL2NhbGxiYWNrIiwiaWF0IjoxNTMyNzYzNjIwLCJleHAiOjE1MzI3NjcyMjAsIm5iZiI6MTUzMjc2MzYyMCwianRpIjoia1RZWHRTcE9sNUdhQnRERCIsInN1YiI6MSwicHJ2IjoiODdlMGFmMWVmOWZkMTU4MTJmZGVjOTcxNTNhMTRlMGIwNDc1NDZhYSJ9.SwuA0fMi68MUxTQJBM_VQh2vFGPd45ceyBJ_ERqGlRM");
        },
        dataType: 'JSON',
        type: 'post',
        success: function (list) {
            console.log(list)
        },
        error: function () {
        }
    });


</script>
</html>