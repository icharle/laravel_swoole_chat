<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>聊天室</title>
    <script src="https://cdn.bootcss.com/jquery/2.2.1/jquery.min.js"></script>
</head>
<body>

<div id="chat_nums">0人</div>
<div id="msg"></div>
<input type="text" id="text">
<input type="submit" value="发送数据" id="send">
</body>

<script>
    wsServer = new WebSocket("ws://127.0.0.1:9502");

    // 开启连接
    wsServer.onopen = function () {
        let data = {
            name: '69831B78F073A55BE8CAA9B8BDED0BA1',           //开发先临时写死
            type: 'connect'
        };
        wsServer.send(JSON.stringify(data));
    }

    // 交流通信
    wsServer.onmessage = function (evt) {
        let data = JSON.parse(evt.data);
        if (data.type == "join") {                  // 加入群聊情况
            $('#msg').append('<div> <img src="' + data.user.avatar + '"> <span>欢迎' + data.user.name + '加入群聊</span> </div>');
            $("#chat_nums").html(data.message + "人");
        }
        else if (data.type == "message") {           // 发送信息情况
            $('#msg').append('<div> <img src="' + data.user.avatar + '"> <span>' + data.user.name + '</span><br> <span>' + data.message + '</span> </div>');
        }
        else if (data.type == "leave") {            // 离开群聊情况
            $('#msg').append('<div> <img src="' + data.user.avatar + '"> <span>' + data.user.name + '离开群聊</span> </div>');
            $("#chat_nums").html(data.message + "人");
        }
    };

    // 手动发送信息
    $('#send').click(function (e) {
        let data = {
            message: '嘤嘤嘤嘤嘤嘤嘤',                            //开发先临时写死
            name: '69831B78F073A55BE8CAA9B8BDED0BA1',           //开发先临时写死
            type: 'message'
        };
        wsServer.send(JSON.stringify(data));
        // TODO 清空输入框
    });

    // 关闭连接
    wsServer.onclose = function () {
        let data = {
            name: '69831B78F073A55BE8CAA9B8BDED0BA1',          //开发先临时写死
            type: 'leave'
        };
        wsServer.send(JSON.stringify(data));
    };

    // 出现错误
    wsServer.onerror = function () {
        console.log("未知错误！");
    };
</script>
</html>