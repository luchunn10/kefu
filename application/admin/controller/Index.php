<?php
/**
 * Handler File Class
 *
 * @author liliang <liliang@wolive.cc>
 * @email liliang@wolive.cc
 * @date 2017/06/01
 */

namespace app\admin\controller;

use app\admin\model\Admins;
use app\admin\model\WechatPlatform;
use app\admin\model\WechatService;
use think\Db;
use think\Paginator;
use app\Common;

date_default_timezone_set("Asia/shanghai");

/**
 *
 * 后台页面控制器.
 */
class Index extends Base
{

    /**
     * 后台首页.
     *
     * @return mixed
     */
    public function index()
    {
        $common = new Common();

        if ($common->isMobile()) {
            $this->redirect('mobile/admin/index');
        }

        $login = $_SESSION['Msg'];
        $time = date('Y-m-d', time());
        $t = strtotime(date('Y-m-d'));
        $times = date('Y-m-d H:i', time());
        $ftime = date('Y-m-d', time());
        $frtime = strtotime($ftime);

        // 接入总量
        $getinall = Admins::table("wolive_chats")->distinct(true)->field('visiter_id')->where('business_id', $login['business_id'])->count();
        // 获取总会话量
        $chatsall = Admins::table("wolive_chats")->where('business_id', $login['business_id'])->count();
        // 正在排队人数
        $waiter = Admins::table("wolive_queue")->where(['business_id' => $login['business_id'], 'state' => 'normal'])->where("service_id", 0)->count();
        // 正在咨询的人
        $talking = Admins::table('wolive_queue')->where(['business_id' => $login['business_id']])->where('state', 'normal')->where("service_id", '<>', 0)->count();
        // 在线客服人数
        $services = Admins::table("wolive_service")->where(['business_id' => $login['business_id'], 'state' => 'online'])->count();
        // 今日会话量
        $nowchats = Admins::table("wolive_chats")->where('business_id', $login['business_id'])->where('timestamp', '>', "{$t}")->where('timestamp', '<=', time())->count();

        //今日评价人数
        $nowcomments = Admins::table("wolive_comment")->where('business_id', $login['business_id'])->where('add_time', '>', "{$time}")->where('add_time', '<=', $times)->count();

        //评价总数
        $allcomments = Admins::table("wolive_comment")->where('business_id', $login['business_id'])->count();

        // 今日留言量
        $message = Admins::table('wolive_message')->where('business_id', $login['business_id'])->where('timestamp', '>', $time)->where('timestamp', '<=', $times)->count();
        // 留言总量
        $messageall = Admins::table('wolive_message')->where('business_id', $login['business_id'])->count();


        if ($times > $cutime = $time . " 08:00") {
            $time8 = strtotime($cutime);
            $chats8 = Admins::table('wolive_chats')->where('business_id', $login['business_id'])->where('timestamp', '>', "{$frtime}%")->where('timestamp', '<', "{$time8}%")->count();
            $chatsdata[] = $chats8;

            $message8 = Admins::table("wolive_message")->where('business_id', $login['business_id'])->where('timestamp', '>', "{$ftime}")->where('timestamp', '<', "{$cutime}")->count();
            $messagedata[] = $message8;

            $getin8 = Admins::table("wolive_chats")->distinct(true)->field('visiter_id')->where('business_id', $login['business_id'])->where('timestamp', '>', "{$frtime}%")->where('timestamp', '<', "{$time8}%")->count();

            $getindata[] = $getin8;

        } else {
            $chatsdata[] = "";
            $messagedata[] = "";
            $getindata[] = "";

        }

        if ($times > $cutime = $time . " 10:00") {

            $time10 = strtotime($cutime);

            $chats10 = Admins::table('wolive_chats')->where('business_id', $login['business_id'])->where('timestamp', '>', "{$frtime}%")->where('timestamp', '<', "{$time10}%")->count();
            $chatsdata[] = $chats10;

            $message10 = Admins::table("wolive_message")->where('business_id', $login['business_id'])->where('timestamp', '>', "{$ftime}")->where('timestamp', '<', "{$cutime}")->count();

            $messagedata[] = $message10;

            $getin10 = Admins::table("wolive_chats")->distinct(true)->field('visiter_id')->where('business_id', $login['business_id'])->where('timestamp', '>', "{$frtime}%")->where('timestamp', '<', "{$time10}%")->count();


            $getindata[] = $getin10;

        } else {
            $chatsdata[] = "";
            $messagedata[] = "";
            $getindata[] = "";

        }

        if ($times > $cutime = $time . " 12:00") {
            $time12 = strtotime($cutime);
            $chats12 = Admins::table('wolive_chats')->where('business_id', $login['business_id'])->where('timestamp', '>', "{$frtime}%")->where('timestamp', '<', "{$time12}%")->count();
            $chatsdata[] = $chats12;

            $message12 = Admins::table("wolive_message")->where('business_id', $login['business_id'])->where('timestamp', '>', "{$ftime}")->where('timestamp', '<', "{$cutime}")->count();

            $messagedata[] = $message12;

            $getin12 = Admins::table("wolive_chats")->distinct(true)->field('visiter_id')->where('business_id', $login['business_id'])->where('timestamp', '>', "{$frtime}%")->where('timestamp', '<', "{$time12}%")->count();


            $getindata[] = $getin12;

        } else {
            $chatsdata[] = "";
            $messagedata[] = "";
            $getindata[] = "";

        }


        if ($times > $cutime = $time . " 14:00") {
            $time14 = strtotime($cutime);

            $chats14 = Admins::table('wolive_chats')->where('business_id', $login['business_id'])->where('timestamp', '>', "{$frtime}%")->where('timestamp', '<', "{$time14}%")->count();
            $chatsdata[] = $chats14;

            $message14 = Admins::table("wolive_message")->where('business_id', $login['business_id'])->where('timestamp', '>', "{$ftime}")->where('timestamp', '<', "{$cutime}")->count();

            $messagedata[] = $message14;

            $getin14 = Admins::table("wolive_chats")->distinct(true)->field('visiter_id')->where('business_id', $login['business_id'])->where('timestamp', '>', "{$frtime}%")->where('timestamp', '<', "{$time14}%")->count();


            $getindata[] = $getin14;

        } else {
            $chatsdata[] = "";
            $messagedata[] = "";
            $getindata[] = "";

        }

        if ($times > $cutime = $time . " 16:00") {
            $time16 = strtotime($cutime);

            $chats16 = Admins::table('wolive_chats')->where('business_id', $login['business_id'])->where('timestamp', '>', "{$frtime}%")->where('timestamp', '<', "{$time16}%")->count();
            $chatsdata[] = $chats16;

            $message16 = Admins::table("wolive_message")->where('business_id', $login['business_id'])->where('timestamp', '>', "{$ftime}")->where('timestamp', '<', "{$cutime}")->count();

            $messagedata[] = $message16;

            $getin16 = Admins::table("wolive_chats")->distinct(true)->field('visiter_id')->where('business_id', $login['business_id'])->where('timestamp', '>', "{$frtime}%")->where('timestamp', '<', "{$time16}%")->count();


            $getindata[] = $getin16;

        } else {
            $chatsdata[] = "";
            $messagedata[] = "";
            $getindata[] = "";

        }

        if ($times > $cutime = $time . " 18:00") {
            $time18 = strtotime($cutime);

            $chats18 = Admins::table('wolive_chats')->where('business_id', $login['business_id'])->where('timestamp', '>', "{$frtime}%")->where('timestamp', '<', "{$time18}%")->count();
            $chatsdata[] = $chats18;

            $message18 = Admins::table("wolive_message")->where('business_id', $login['business_id'])->where('timestamp', '>', "{$ftime}")->where('timestamp', '<', "{$cutime}")->count();

            $messagedata[] = $message18;

            $getin18 = Admins::table("wolive_chats")->distinct(true)->field('visiter_id')->where('business_id', $login['business_id'])->where('timestamp', '>', "{$frtime}%")->where('timestamp', '<', "{$time18}%")->count();


            $getindata[] = $getin18;

        } else {
            $chatsdata[] = "";
            $messagedata[] = "";
            $getindata[] = "";
        }

        if ($times > $cutime = $time . " 20:00") {
            $time20 = strtotime($cutime);
            $chats20 = Admins::table('wolive_chats')->where('business_id', $login['business_id'])->where('timestamp', '>', "{$frtime}%")->where('timestamp', '<', "{$time20}%")->count();
            $chatsdata[] = $chats20;

            $message20 = Admins::table("wolive_message")->where('business_id', $login['business_id'])->where('timestamp', '>', "{$ftime}")->where('timestamp', '<', "{$cutime}")->count();

            $messagedata[] = $message20;

            $getin20 = Admins::table("wolive_chats")->distinct(true)->field('visiter_id')->where('business_id', $login['business_id'])->where('timestamp', '>', "{$frtime}%")->where('timestamp', '<', "{$time20}%")->count();


            $getindata[] = $getin20;

        } else {
            $chatsdata[] = "";
            $messagedata[] = "";
            $getindata[] = "";

        }

        if ($times > $cutime = $time . " 22:00") {
            $time22 = strtotime($cutime);
            $chats22 = Admins::table('wolive_chats')->where('business_id', $login['business_id'])->where('timestamp', '>', "{$frtime}%")->where('timestamp', '<', "{$time22}%")->count();
            $chatsdata[] = $chats22;

            $message22 = Admins::table("wolive_message")->where('business_id', $login['business_id'])->where('timestamp', '>', "{$ftime}%")->where('timestamp', '<', "{$cutime}")->count();

            $messagedata[] = $message22;

            $getin22 = Admins::table("wolive_chats")->distinct(true)->field('visiter_id')->where('business_id', $login['business_id'])->where('timestamp', '>', "{$frtime}%")->where('timestamp', '<', "{$time22}%")->count();


            $getindata[] = $getin22;

        } else {
            $chatsdata[] = "";
            $messagedata[] = "";
            $getindata[] = "";

        }

        if ($times > $cutime = $time . " 00:00") {
            $time00 = strtotime($cutime);
            $chats00 = Admins::table('wolive_chats')->where('business_id', $login['business_id'])->where('timestamp', '>', "{$frtime}%")->where('timestamp', '<', "{$time00}%")->count();
            $chatsdata[] = $chats00;

            $message00 = Admins::table("wolive_message")->where('business_id', $login['business_id'])->where('timestamp', '>', "{$ftime}%")->where('timestamp', '<', "{$cutime}")->count();

            $messagedata[] = $message00;

            $getin00 = Admins::table("wolive_chats")->distinct(true)->field('visiter_id')->where('business_id', $login['business_id'])->where('timestamp', '>', "{$frtime}%")->where('timestamp', '<', "{$time00}%")->count();


            $getindata[] = $getin00;

        } else {
            $chatsdata[] = "";
            $messagedata[] = "";
            $getindata[] = "";

        }

        $this->assign('nowcomments',$nowcomments);
        $this->assign('allcomments',$allcomments);

        $this->assign('chatsdata', $chatsdata);
        $this->assign('messagedata', $messagedata);
        $this->assign('getindata', $getindata);

        $this->assign('getinall', $getinall);
        $this->assign('waiter', $waiter);
        $this->assign('chatsall', $chatsall);
        $this->assign('talking', $talking);
        $this->assign('services', $services);
        $this->assign('nowchats', $nowchats);
        $this->assign('message', $message);
        $this->assign('messageall', $messageall);
        $this->assign("part", "首页");
        $this->assign('title', '首页');

        return $this->fetch();
    }

    /**
     * 后台对话页面.
     *
     * @return mixed
     */
    public function chats()
    {
        $login = $_SESSION['Msg'];
        $res = Admins::table('wolive_business')->where('id', $login['business_id'])->find();
        $this->assign("type", $res['video_state']);
        $this->assign('atype', $res['audio_state']);
        $this->assign("title", "对话平台");
        $this->assign('part', '对话平台');
        return $this->fetch();
    }


    /**
     * 常用语页面.
     *
     * @return mixed
     */
    public function custom()
    {
        $login = $_SESSION['Msg'];
        $data = Admins::table("wolive_sentence")->where('service_id', $login['service_id'])->paginate(9);
        $page = $data->render();
        $this->assign('page', $page);
        $this->assign('lister', $data);
        $this->assign('title', "问候语设置");
        $this->assign('part', "设置");

        return $this->fetch();
    }

    /**
     * 常见问题设置.
     *
     * @return mixed
     */
    public function question()
    {
        $login = $_SESSION['Msg'];
        if ($login['level'] == 'service') {
            $this->redirect('admin/index/index');
        }
        $data = Admins::table("wolive_question")
            ->where('business_id', $login['business_id'])
            ->order('sort desc')
            ->paginate();
        $page = $data->render();
        $this->assign('page', $page);
        $this->assign('lister', $data);
        $this->assign('title', "常见问题设置");
        $this->assign('part', "设置");
        return $this->fetch();
    }


    /**
     * 生成前台文件页面.
     *
     * @return mixed
     */
    public function front()
    {
        $http_type = ((isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';

        $web = $http_type . $_SERVER['HTTP_HOST'];
        $action = $web.request()->root();

        $login = $_SESSION['Msg'];
        $class = Admins::table('wolive_group')->where('business_id', $login['business_id'])->select();

        $html = "";
        foreach ($class as $v) {
            $html .= ' <form class="dianqilai-item" action="' . $action . '/index/index/home?visiter_id=&visiter_name=&avatar=&business_id=' . $login['business_id'] . '&groupid=' . $v['id'] . '" method="post" target="_blank" >';
            $html .= ' <input type="hidden" name="product" value="">';
            $html .= ' <input type="submit" value="' . $v['groupname'] . '"></form>';
        }


        $str = '<link rel="stylesheet" href="' . $web . '__style__/index/dianqilai_online.css">';
        $str .= '<div class="dianqilai-form"  id="dianqilai-kefu"><i class="dianqilai-icon"></i> ';
        $str .= '<form class="dianqilai-item" action="' . $action . '/index/index/home?visiter_id=&visiter_name=&avatar=&business_id=' . $login['business_id'] . '&groupid=0" method="post" target="_blank" >';
        $str .= '<input type="hidden" name="product"  value="">';
        $str .= '<input type="submit" value="在线咨询"></form>' . $html;
        $str .= '</div>';

        $groups = Db::table('wolive_group')
            ->where('business_id',$login['business_id'])
            ->select();
        $wechat = [];
        foreach ($groups as $v) {
            $temp['group_name'] = $v['groupname'];
            $temp['url'] = $web.url('index/index/wechat',['business_id'=>$login['business_id'],'groupid'=>$v['id']]);
            $wechat[] = $temp;
        }
        $wechat[] = ['group_name'=>'通用分组','url'=> $web.url('index/index/wechat',['business_id'=>$login['business_id'],'groupid'=>0])];

        $this->assign('class', $class);
        $this->assign('business', $login['business_id']);
        $this->assign('web', $web);
        $this->assign('wechat', $wechat);
        $this->assign('personal', $action.'/index/index/home?visiter_id=&visiter_name=&avatar=&business_id='.$login['business_id'].'&groupid='.$login['groupid'].'&special='.$login['service_id']);
        $this->assign('action', $action);
        $this->assign('html', $str);
        $this->assign("title", "网页部署");
        $this->assign("part", "网页部署");

        return $this->fetch();
    }


    /**
     * 所有聊天记录页面。
     * [history description]
     * @return [type] [description]
     */
    public function history()
    {
        $visiter_id = $this->request->param('visiter_id');
        $this->assign('visiter_id',$visiter_id);
        return $this->fetch();
    }

    /**
     * 留言页面.
     *
     * @return mixed
     */
    private function message()
    {
        $login = $_SESSION['Msg'];
        $post = $this->request->get();
        $userAdmin = Admins::table('wolive_message');
        $pageParam = ['query' => []];
        unset($post['page']);
        if ($post) {
            $pushtime = $post['pushtime'];

            if ($pushtime) {
                if ($pushtime == 1) {
                    $timetoday = date("Y-m-d", time());
                    $userAdmin->where('timestamp', 'like', $timetoday . "%");
                    $this->assign('pushtime', $pushtime);
                    $pageParam['query']['timestamp'] = $pushtime;
                } elseif ($pushtime == 7) {
                    $timechou = strtotime("-1 week");
                    $times = date("Y-m-d", $timechou);
                    $userAdmin->where('timestamp', ">", $times);
                    $this->assign('pushtime', $pushtime);
                    $pageParam['query']['timestamp'] = $pushtime;
                }
            }
        }

        $data = $userAdmin->where('business_id', $login['business_id'])->paginate(8, false, $pageParam);
        $page = $data->render();
        $this->assign('page', $page);
        $this->assign('msgdata', $data);
        $this->assign('title', "留言查看");
        $this->assign('part', "留言查看");

        return $this->fetch();
    }

    /**
     * 转接客服页面
     * @return [type] [description]
     */
    public function service()
    {

        $get = $_GET;

        $visiter_id = $_GET['visiter_id'];

        $login = $_SESSION['Msg'];

        $business_id = $login['business_id'];

        $res = Admins::table('wolive_service')->where('business_id', "{$business_id}")->where('service_id', '<>', $login['service_id'])->select();

        $this->assign('service', $res);
        $this->assign('visiter_id', $visiter_id);
        $this->assign('name', $get['name']);

        return $this->fetch();
    }

    /**
     * 常见问题编辑页面
     * [editer description]
     * @return [type] [description]
     */
    public function editer()
    {
        $login = $_SESSION['Msg'];
        if ($login['level'] == 'service') {
            $this->redirect('admin/index/index');
        }

        $get = $this->request->get();

        $res = Admins::table('wolive_question')
            ->where('qid', $get['qid'])
            ->order('sort desc')
            ->find();

        $this->assign('question', $res['question']);
        $this->assign('keyword',$res['keyword']);
        $this->assign('answer', $res['answer_read']);
        $this->assign('qid', $get['qid']);
        $this->assign('sort', $res['sort']);

        return $this->fetch();
    }


    /**
     * 编辑tab页面
     * [editertab description]
     * @return [type] [description]
     */
    public function editertab()
    {

        $login = $_SESSION['Msg'];
        if ($login['level'] == 'service') {
            $this->redirect('admin/index/index');
        }

        $get = $this->request->get();

        $res = Admins::table('wolive_tablist')->where('tid', $get['tid'])->find();

        $this->assign('title', $res['title']);
        $this->assign('content', $res['content_read']);
        $this->assign('tid', $get['tid']);

        return $this->fetch();
    }

    public function editercustom()
    {
        $login = $_SESSION['Msg'];
        $get = $this->request->get();

        $res = Admins::table('wolive_sentence')
            ->where('sid', $get['sid'])
            ->where('service_id',$login['service_id'])
            ->find();

        $this->assign('content', $res['content']);
        $this->assign('sid', $get['sid']);

        return $this->fetch();
    }

    /**
     * 设置页面
     * [set description]
     */
    public function set()
    {

        $this->assign('user', $_SESSION['Msg']);
        $this->assign('title', '设置');
        $this->assign('part', '设置');
        return $this->fetch();
    }


    public function setup()
    {

        $login = $_SESSION['Msg'];
        if ($login['level'] == 'service') {
            $this->redirect('admin/index/index');
        }
        $res = Admins::table("wolive_business")->where('id', $login['business_id'])->find();

        $this->assign('video', $res['video_state']);
        $this->assign('audio', $res['audio_state']);
        $this->assign('voice', $res['voice_state']);
        $this->assign('voice_addr', $res['voice_address']);
        $this->assign('template', $res['template_state']);
        $this->assign('method', $res['distribution_rule']);
        $this->assign('push_url',$res['push_url']);
        $this->assign('title', '通用设置');
        $this->assign('part', '设置');

        return $this->fetch();
    }

    /**
     * tab面版页面。
     * [tablist description]
     * @return [type] [description]
     */
    public function tablist()
    {


        if ($_SESSION['Msg']['level'] == 'service') {
            $this->redirect('admin/index/index');
        }

        $business_id = $_SESSION['Msg']['business_id'];

        $res = Admins::table('wolive_tablist')->where('business_id', $business_id)->select();

        $this->assign('tablist', $res);

        $this->assign('title', '编辑前端tab面版');
        $this->assign('part', '设置');

        return $this->fetch();
    }


    /**
     *
     * [replylist description]
     * @return [type] [description]
     */
    public function replylist()
    {

        $id = $_SESSION['Msg']['service_id'];
        $res = Admins::table('wolive_reply')->where('service_id', $id)->paginate(8);
        $page = $res->render();
        $this->assign('page', $page);
        $this->assign('replyword', $res);

        return $this->fetch();
    }

    public function template()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $post['business_id'] = $_SESSION['Msg']['business_id'];

            $res = WechatPlatform::edit($post);

            $arr = $res!== false ? ['code' => 0, 'msg' => '成功']: ['code' => 1, 'msg' => '失败'];
            return $arr;
        } else {
            $template = WechatPlatform::get(['business_id'=>$_SESSION['Msg']['business_id']]);
            $this->assign('template',$template);
            $this->assign('title', '公众号与模板消息设置');
            $this->assign('part', "设置");
            return $this->fetch();
        }
    }

    public function qrcode()
    {
        $qrcode = WechatService::get()->qrcode;
        $result = $qrcode->temporary($_SESSION['Msg']['service_id'], 6 * 24 * 3600);

        $ticket = $result->ticket;// 或者 $result['ticket']
        $url = $qrcode->url($ticket);
        return json(['code'=>0,'data'=>$url]);
    }
}
