<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use swoole_websocket_server;

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
            

            //广播(除开自己本身)
            foreach ($ws->connections as $i) {
                if ($i == $frame->fd) {
                    continue;
                }
                $ws->push($i, $data['avatar']);
            }

        });

        //监听WebSocket连接关闭事件
        $ws->on('close', function ($ws, $fd) {
            $this->info("client is close\n");
        });

        $ws->start();
    }
}
