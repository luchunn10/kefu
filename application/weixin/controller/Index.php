<?php

namespace app\weixin\controller;
use app\admin\model\WechatPlatform;
use app\extra\push\Pusher;
use app\weixin\model\Weixin;
use think\Controller;
use EasyWeChat\Foundation\Application;
use app\weixin\model\Admins;
use think\Log;

class  Index extends Controller
{
   public function index()
   {
       $business_id = $this->request->param('business_id',null);
       empty($business_id) && abort(500,'参数错误');
       $wechat = WechatPlatform::get(['business_id' => $business_id]);
       // config配置
       $options=[
           'debug'  => true,
            'app_id' => $wechat['app_id'],
            'secret' => $wechat['app_secret'],
            'token'  => token,
            'log' => [
                'level' => 'debug',
                'file'  => '/tmp/easywechat.log', // XXX: 绝对路径！！！！
            ],
       ];

     
    
       $url = domain;

       $app = new Application($options);
       $server = $app->server;
       // 消息回复
       $server->setMessageHandler(function ($message) {
           Log::info($message);
           // $message->FromUserName // 用户的 openid
           // $message->MsgType // 消息类型：event, text....
           switch ($message->MsgType) {
               case 'event':
                   switch ($message->Event) {
                       case 'subscribe':
                           return '欢迎关注';
                           break;
                       case 'unsubscribe':
                           Weixin::destroy([
                               'open_id' => $message->FromUserName,
                           ]);
                           return '取消关注';
                           break;
                       case 'SCAN':
//                           return '用户通过扫描带参二维码'.$message->EventKey;
                           Weixin::create([
                               'open_id' => $message->FromUserName,
                               'service_id' => $message->EventKey,
                           ]);

                           $sarr = parse_url(ahost);
                           if ($sarr['scheme'] == 'https') {
                               $state = true;
                           } else {
                               $state = false;
                           }

                           $app_key = app_key;
                           $app_secret = app_secret;
                           $app_id = app_id;
                           $options = array(
                               'encrypted' => $state
                           );
                           $host = ahost;
                           $port = aport;

                           $pusher = new Pusher(
                               $app_key,
                               $app_secret,
                               $app_id,
                               $options,
                               $host,
                               $port
                           );

                           $pusher->trigger("kefu" . $message->EventKey, "bind-wechat", array("message" => "绑定账号成功！"));
                           return 'success';
                           break;
                   }
                   break;
                   break;
               case 'text':
                   switch ($message->Content) {
                       case 'qq':
                           return 'qq:2202055656';
                           break;
                       case '价格':
                           return '价格：￥4999';
                           break;
                       case '官网':
                           return '请访问公司网址:<a href="https://www.dianqilai.com">WoLive官网</a>';
                           break;
                   }
                   break;
               default:
                   return '收到其它消息';
                   break;
           }

       });

       // 自定义菜单
       $menu = $app->menu;
       $buttons =[
         
            [
                "name"=>"客服系统", 
               "sub_button"=>[ 
                  [
                  "type"=>"view", 
                  "name"=>"工作台",
                  "url"=>$url."/weixin/login/callback/business_id/".$business_id
                  ],
                 ],
            ], 
       ];


       $menu->add($buttons);
       $response = $server->serve();
       $response->send();
   }

}
