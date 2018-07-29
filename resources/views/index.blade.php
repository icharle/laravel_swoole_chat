<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>QQ登录</title>
</head>
<body>

{{--<a onclick='toQQLogin()'>QQ登录</a>--}}
<a href="http://chat.test/qq">QQ登录</a>

<script>
    function toQQLogin() {
        var _url="http://chat.test/qq";  //转向网页的地址;
        var name='QQ授权登录';    //网页名称，可为空;
        var iWidth=800; //弹出窗口的宽度;
        var iHeight=600;   //弹出窗口的高度;
        //获得窗口的垂直位置
        var iTop = (window.screen.availHeight - 30 - iHeight) / 2;
        //获得窗口的水平位置
        var iLeft = (window.screen.availWidth - 10 - iWidth) / 2;
        window.open(_url, name, 'height=' + iHeight +
            ',innerHeight=' + iHeight + ',width=' + iWidth +
            ',innerWidth=' + iWidth + ',top=' + iTop + ',left=' + iLeft +
            ',status=1,toolbar=no,menubar=no,location=1,resizable=no,scrollbars=0,titlebar=no');
    }
</script>

</body>
</html>