<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use swoole_websocket_server;
use Illuminate\Support\Facades\Redis;
use App\User;

class Swoole extends Command
{
    /**
     * @var User
     */
    protected $user;
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
    public function __construct(User $user)
    {
        parent::__construct();
        $this->user = $user;
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

            $res = $this->user->where('OpenID', $data['name'])->first();

            if ($data['type'] == "connect") {                      //加入房间类型
                Redis::zadd("room", intval($res['OpenID']), $frame->fd);            //绑定用户openID值以及回话ID
                $onlinenum = Redis::zcard("room");                          //统计该房间总人数
                $this->send($ws, $res, $onlinenum, 'join');           //群发信息

            } elseif ($data['type'] == "message") {                 //普通信息类型
                $this->send($ws, $res, $data['message'], 'message');           //群发信息
            }
        });

        //监听WebSocket连接关闭事件
        $ws->on('close', function ($ws, $fd) {
            $this->info("client is close\n");
            $data['name'] = intval(Redis::zscore("room", $fd));                     //获取该连接对应的用户信息
            $res = $this->user->where('OpenID', $data['name'])->first();
            Redis::zrem("room", $fd);                                               //移除用户
            $onlinenum = Redis::zcard("room");                          //统计该房间总人数
            $this->send($ws, $res, $onlinenum, 'leave');           //群发信息
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
            'message' => is_string($content) ? nl2br($content) : $content,
            'type' => $type,
            'user' => array(
                'name' => $userinfo['NickName'],                    //用户名称
                'avatar' => $userinfo['Avatar']                     //用户头像
            )
        ]);
        $members = Redis::zrange("room", 0, -1);                    //所有在房间的用户fd

        foreach ($members as $fd) {
            $ws->push(intval($fd), $message);                       //每个用户发送信息
        }
    }
}
