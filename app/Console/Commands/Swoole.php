<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use swoole_websocket_server;
use App\User;

class Swoole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swoole {action}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Chat Living System';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $arg = $this->argument('action');
        switch ($arg) {
            case 'start':
                $this->info('swoole server started');
                $this->start();
                break;
            case 'stop':
                $this->info('swoole server stoped');
                break;
            case 'restart':
                $this->info('swoole server restarted');
                break;
        }
    }

    private function start()
    {
        $ws = new swoole_websocket_server("0.0.0.0", 9502);

        //监听WebSocket连接打开事件
        $ws->on('open', function ($ws, $request) {
            $this->info("client is open\n");
        });

        //监听WebSocket消息事件
        $ws->on('message', function ($ws, $frame) {
            $data = json_decode($frame->data, true);        //收到发送数据

            $res = User::where('OpenID', $data['name'])->get(['Avatar', 'NickName', 'OpenID']);

            if ($data['type'] == "connect") {                      //加入房间类型
                Redis::zadd("room", $res['OpenID'], $frame->id);            //绑定用户openID值以及回话ID
                $onlinenum = Redis::zcard("room");                          //统计该房间总人数
                $this->send($ws, $res, $onlinenum, 'join');           //群发信息

            } elseif ($data['type'] == "message") {                 //普通信息类型
                $this->send($ws, $res, $data['message'], 'message');           //群发信息
            } elseif ($data['type'] == "leave") {                   //离开房间类型
                Redis::zrem("room", $frame->fd);                                   //移除用户
                $this->send($ws, $res, '', 'leave');           //群发信息
            }
        });

        //监听WebSocket连接关闭事件
        $ws->on('close', function ($ws, $fd) {
            $this->info("client is close\n");
        });

        $ws->start();
    }

    /**
     * 发送信息封装方法
     * @param $ws           WebSocket连接对象
     * @param $userinfo     发送者用户信息
     * @param $content      发送内容
     * @param $type         类型(加入房间、发送普通信息、离开房间)
     */
    private function send($ws, $userinfo, $content, $type)
    {
        $message = json_encode([
            'message' => $content,
            'type' => $type,
            'user' => array(
                'name' => $userinfo['NickName'],
                'Avatar' => $userinfo['Avatar']
            )
        ]);
        $members = Redis::zrange("room", 0, -1);
        foreach ($members as $fd) {
            $ws->push($fd, $message);
        }
    }
}
