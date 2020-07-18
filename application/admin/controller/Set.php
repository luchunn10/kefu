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
use app\admin\model\Chats;
use app\admin\model\CommentSetting;
use app\admin\model\Queue;
use app\admin\model\TplService;
use app\admin\model\Visiter;
use app\admin\model\WechatPlatform;
use app\common\lib\CurlUtils;
use app\common\lib\Lock;
use app\extra\push\Pusher;
use think\Db;
use think\Exception;
use think\Log;
use app\admin\iplocation\Ip;

date_default_timezone_set("Asia/shanghai");

/**
 *
 * 设置控制器.
 */
class Set extends Base
{
    /**
     * 对话pusher类.
     *
     * @return void
     */
    public function chats()
    {
        $login = $_SESSION['Msg'];
        $arr = $this->request->post('',null,null);

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

        $arr['business_id'] = $login['business_id'];
        $arr['service_id'] = $login['service_id'];

        $arr['direction'] = 'to_visiter';
        $arr["timestamp"] = time();
        $channel = bin2hex($arr['visiter_id'] . '/' . $arr['business_id']);
        $visiter = Db::table('wolive_visiter')
            ->where('visiter_id',$arr['visiter_id'])
            ->where('business_id',$login['business_id'])
            ->find();

        $queue = Db::table('wolive_queue')
            ->where('visiter_id',$arr['visiter_id'])
            ->where('business_id',$login['business_id'])
            ->find();

        try {
            if ($visiter['state'] == 'offline') {
                $wechat = WechatPlatform::get(['business_id'=>$arr['business_id']]);
                TplService::send($arr["business_id"],$visiter['visiter_id'],url('index/index/wechat',['business_id'=>$arr['business_id'],'groupid'=>$queue['groupid']],true,true),$wechat['customer_tpl'],[
                    "first"  => "你有一条新的信息!",
                    "keyword1"   => $arr["content"],
                    "keyword2"  => $login['nick_name'],
                    "remark" => $login['business']['business_name']."提示:客服有新的消息,快去看看吧~",
                ]);
            }
        } catch (\EasyWeChat\Core\Exceptions\HttpException $e) {

        } catch (\EasyWeChat\Core\Exceptions\InvalidArgumentException $exception) {

        }

        try {

            $arr['avatar'] = $login['avatar'];

            $pusher->trigger("cu" . $channel, 'my-event', array('message' => $arr));
            $key = "callback_".$_SESSION['Msg']['business_id']."_".$_SESSION['Msg']['service_id'];
            //针对同一客户端的锁，防止同一客户端多次回调
            $_SESSION[$key] = md5(microtime(true));
            $pusher->trigger('kefu' .  $login['service_id'], 'callbackpusher', array('message' => $arr));

            unset($arr['avatar']);


            $res = Admins::table('wolive_chats')->insert($arr);

            $data = ['code' => 0, 'msg' => 'success'];
            return $data;

        } catch (Exception $e) {

            $error = $e->getMessage();
            $data = ['code' => 3, 'msg' => $error];
            return $data;
        }

    }

    /**
     * 删除访客类.
     *
     * @return mixed
     */
    public function deletes()
    {

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


        $login = $_SESSION['Msg'];
        $post = $this->request->post();
        $result = Admins::table('wolive_queue')->where('visiter_id', $post['visiter_id'])->where('business_id', $login['business_id'])->update(['state' => 'complete']);


        $pusher->trigger("ud" . $login['service_id'], 'on_chat', array('message' => ''));

        $data = ['code' => 0, 'msg' => 'success'];

        return $data;

    }


    /**
     * 转接客服类.
     * @return [type] [description]
     */
    public function getswitch()
    {
        $login = $_SESSION['Msg'];
        $post = $this->request->post();

        $admin = Admins::table('wolive_service')->where('service_id', $post['id'])->find();

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

        $channel = bin2hex($post['visiter_id'] . '/' . $login['business_id']);


        $pusher->trigger("cu" . $channel, 'getswitch', array('message' => $admin));

        $pusher->trigger('kefu' . $post['id'], 'getswitch', array('message' => $post['name'] . "  转接访客给你"));

        $result = Admins::table('wolive_queue')->where("visiter_id", $post['visiter_id'])->where('business_id', $login['business_id'])->where('state', 'normal')->update(['service_id' => $post['id']]);


        if ($result) {
            $arr = ['code' => 0, 'msg' => '转接成功！'];
            return $arr;
        } else {
            $arr = ['code' => 1, 'msg' => '转接失败！'];
            return $arr;
        }

    }


    /**
     * 认领访客类.
     *
     * @return mixed
     */
    public function get()
    {
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

        $login = $_SESSION['Msg'];
        $post = $this->request->post();
        // 避免重复认领
        if ($login['groupid'] == 0) {
            $res = Admins::table("wolive_queue")->where("visiter_id", $post['visiter_id'])->where('business_id', $login['business_id'])->where('state', 'normal')->find();
        } else {
            $res = Admins::table("wolive_queue")->where("visiter_id", $post['visiter_id'])->where('business_id', $login['business_id'])->where('state', 'normal')->where('groupid', $login['groupid'])->find();
        }


        if ($res['service_id'] == 0) {

            $result = Admins::table('wolive_queue')->where(['qid' => $res['qid'], 'state' => 'normal'])->update(['service_id' => $login['service_id']]);


            $channel = bin2hex($post['visiter_id'] . '/' . $login['business_id']);

            $resdata = Admins::table('wolive_chats')->where(['visiter_id' => $post['visiter_id'], 'business_id' => $login['business_id']])->update(['service_id' => $login['service_id']]);


            // 获取默认的常用语
            $words = Admins::table('wolive_sentence')->where('service_id', $login['service_id'])->where('state', 'using')->find();
            if ($words['content'] == "") {
                $words['content'] = "你好!";
            }
            $chats = array(
                'avatar' => $login['avatar'],
                'content' => $words['content']
            );

            $arr = ['msg' => '访客被认领', 'groupid' => $login['groupid']];
            $pusher->trigger("all" . $login['business_id'], 'on_notice', array('message' => $arr));
            $pusher->trigger("cu" . $channel, 'first_word', array('message' => $chats));
            $pusher->trigger("cu" . $channel, 'cu_notice', array('message' => $login));

            $data = ['code' => 0, 'msg' => 'success'];
            return $data;

        } else {

            $res = Admins::table('wolive_queue')->where(['visiter_id' => $post['visiter_id'], 'business_id' => $login['business_id'], 'service_id' => 0, 'state' => "normal"])->delete();

            $arr = ['msg' => '该客服已经被认领', 'groupid' => $login['groupid']];
            $pusher->trigger("all" . $login['business_id'], 'on_notice', array('message' => $arr));
        }
    }

    /**
     * 排队列表类.
     *
     * @return mixed
     */
    public function getwait()
    {
        $login = $_SESSION['Msg'];

        if ($login['groupid'] == 0) {
            $visiters = Admins::table('wolive_queue')->where('service_id', 0)->where('business_id', $login['business_id'])->where('state', 'normal')->select();
        } else {
            $visiters = Admins::table('wolive_queue')->where('service_id', 0)->where('business_id', $login['business_id'])->where('state', 'normal')->where('groupid', $login['groupid'])->select();
        }


        $waiters = [];

        foreach ($visiters as $v) {
            $data = Admins::table('wolive_visiter')->where('visiter_id', $v['visiter_id'])->where('business_id', $login['business_id'])->find();

            $class = Admins::table('wolive_group')->where('id', $v['groupid'])->find();

            if ($data) {
                $waiters[] = $data;
            }

            if ($class) {
                $data['groupname'] = $class['groupname'];
            } else {
                $data['groupname'] = '普通咨询';
            }

            if (!empty($data['timestamp'])) {
                $data['timestamp'] = $this->formatTime(strtotime($data['timestamp']));
            }
        }
        $count = count($waiters);

        $data = ['code' => 0, 'data' => $waiters, 'num' => $count];
        return $data;
    }

    /**
     * 对话列表类.
     *
     * @return mixed
     */
    public function getchats()
    {

        $login = $_SESSION['Msg'];

        $visiters = Admins::table('wolive_queue')->distinct(true)->field('visiter_id')->where(['service_id' => $login['service_id'], 'business_id' => $login['business_id']])->where('state', 'normal')->order('timestamp desc')->select();
        $visiters = array_column(collection($visiters)->toArray(),'visiter_id');

        if (empty($visiters)) {
            $data = ['code' => 1, 'msg' => '暂时没有数据！'];
            return $data;
        }

        function extract_attrib($tag)
        {
            preg_match_all('/(id|alt|title|src)=("[^"]*")/i', $tag, $matches);
            $ret = array();
            foreach ($matches[1] as $i => $v) {
                $ret[$v] = $matches[2][$i];
            }
            return $ret;
        }

        $chatonlinearr = [];
        $chatonlineunread = [];
        $chatofflinearr = [];
        $chatofflineunread = [];

        $data = Visiter::all(['business_id'=>$login['business_id'],'visiter_id'=>['in',$visiters]]);

        $chatids = Chats::field('max(cid) as cid')
            ->where('business_id',$login['business_id'])
            ->where('visiter_id','in',$visiters)
            ->where('service_id',$login['service_id'])
            ->group('visiter_id')
            ->order('timestamp desc')
            ->select();

        $cids = array_column(collection($chatids)->toArray(),'cid');
        if (empty($cids)) {
            $chatsList = [];
        } else {
            $chats = Chats::where('cid','in',$cids)
                ->select();
            $chatsList = array_column(collection($chats)->toArray(),null,'visiter_id');
        }


        $result = Chats::where('business_id',$login['business_id'])
            ->where('state','unread')
            ->where('direction', 'to_service')
            ->field('visiter_id,count(visiter_id) as count')
            ->group('visiter_id')
            ->select();

        $resultList = array_column(collection($result)->toArray(),null,'visiter_id');

        foreach ($data as $v) {
            $chats2['content'] = isset($chatsList[$v['visiter_id']]['content']) ? $chatsList[$v['visiter_id']]['content'] : '' ;
            $chats2['timestamp'] = isset($chatsList[$v['visiter_id']]['timestamp']) ? $chatsList[$v['visiter_id']]['timestamp']:0;

            $values = preg_match_all('/<img.*\>/isU', $chats2['content'], $out);
            if ($values) {

                $img = $out[0];

                if ($img) {

                    $chats = "";
                    foreach ($img as $value) {
                        $attr = extract_attrib($value);

                        if ($attr) {
                            $src = $attr["src"];
                            if ($src) {
                                if (strpos($src, "emo_")) {
                                    $newimg = "<img src={$src}>";
                                } else {
                                    $newimg = "[图片]";
                                }
                            }
                        } else {
                            $newimg = '[图片]';
                        }
                        $chats .= $newimg;
                    }
                }
                $newstr = preg_replace('/<img.*\>/isU', "", $chats2['content']);
                $newcontent = $chats . $newstr;

            } else {

                if (strpos($chats2['content'], '</i>') !== false) {

                    $newcontent = '[文件]';
                } elseif (strpos($chats2['content'], '</audio>') !== false) {

                    $newcontent = '[音频]';

                } elseif (strpos($chats2['content'], '</a>') !== false) {
                    $newcontent = '[超链接]';
                } else {

                    if ($chats2['content'] == null) {
                        $newcontent = '';
                    } else {
                        $newcontent = $chats2['content'];
                    }
                }
            }
            $v['content'] = $newcontent;
            if (isset($resultList[$v['visiter_id']]['count'])) {
                $v['count'] = $resultList[$v['visiter_id']]['count'];
            } else {
                $v['count'] = 0;
            }

            if (!empty($chats2['timestamp'])) {
                $time = $chats2['timestamp'];
                $v['order'] = $chats2['timestamp'];
            } else {
                $time = strtotime($v['timestamp']);
                $v['order'] = 0;
            }
            $v['timestamp'] = $this->formatTime($time);
            $url = url('mobile/admin/talk',null,true,true);
            $v['mobile_route_url'] = $url."?channel=".$v['channel']."&avatar=".urlencode($v['avatar'])."&visiter_id=".$v['visiter_id'];
            if ($v['count'] > 0) {
                if ($v['state'] == 'online') {
                    $chatonlineunread[] = $v;
                } else {
                    $chatofflineunread[] = $v;
                }
            } else {
                if ($v['state'] == 'online') {
                    $chatonlinearr[] = $v;
                } else {
                    $chatofflinearr[] = $v;
                }
            }

        }

        reset($chatonlineunread);
        reset($chatofflineunread);
        reset($chatonlinearr);
        reset($chatofflinearr);
        array_multisort(array_column(collection($chatonlineunread)->toArray(),'order'),SORT_DESC,$chatonlineunread);
        array_multisort(array_column(collection($chatofflineunread)->toArray(),'order'),SORT_DESC,$chatofflineunread);
        array_multisort(array_column(collection($chatonlinearr)->toArray(),'order'),SORT_DESC,array_column(collection($chatonlinearr)->toArray(),'vid'),SORT_DESC,$chatonlinearr);
        array_multisort(array_column(collection($chatofflinearr)->toArray(),'order'),SORT_DESC,array_column(collection($chatofflinearr)->toArray(),'vid'),SORT_DESC,$chatofflinearr);
        $chatarr = array_merge($chatonlineunread, $chatofflineunread,$chatonlinearr,$chatofflinearr);

        $result = Admins::table('wolive_chats')->where('service_id', $login['service_id'])->where('business_id', $login['business_id'])->where('state', 'unread')->where('direction', 'to_service')->count();
        if ($chatarr) {
            $data = ['code' => 0, 'data' => $chatarr,'all_unread_count'=>$result];
            return $data;
        } else {
            $data = ['code' => 1, 'msg' => '暂时没有数据！'];
            return $data;
        }
    }


    /**
     * 获取当前聊天信息类.
     *
     * @return string
     */
    public function chatdata()
    {

        $login = $_SESSION['Msg'];
        $service_id = $login['service_id'];
        $post = $this->request->post();

        if ($post["hid"] == '') {

            $data = Admins::table('wolive_chats')->where(['service_id' => $service_id, 'visiter_id' => $post['visiter_id'], 'business_id' => $login['business_id']])->order('timestamp desc,cid asc')->limit(10)->select();

            $vdata = Admins::table('wolive_visiter')->where('visiter_id', $post['visiter_id'])->where('business_id', $login['business_id'])->find();

            $sdata = Admins::table('wolive_service')->where('service_id', $service_id)->find();

            foreach ($data as $v) {

                if ($v['direction'] == 'to_service') {
                    $v['avatar'] = $vdata['avatar'];
                } else {

                    $v['avatar'] = $sdata['avatar'];
                }

            }

            reset($data);

        } else {


            $data = Admins::table('wolive_chats')->where(['service_id' => $service_id, 'visiter_id' => $post['visiter_id'], 'business_id' => $login['business_id']])->where('cid', '<', $post['hid'])->order('timestamp desc,cid asc')->limit(10)->select();

            $vdata = Admins::table('wolive_visiter')->where('visiter_id', $post['visiter_id'])->where('business_id', $login['business_id'])->find();

            $sdata = Admins::table('wolive_service')->where('service_id', $service_id)->find();


            foreach ($data as $v) {

                if ($v['direction'] == 'to_service') {
                    $v['avatar'] = $vdata['avatar'];
                } else {
                    $v['avatar'] = $sdata['avatar'];
                }

            }

            reset($data);

        }

        $result = array_reverse($data);
        $user = Admins::table('wolive_visiter')
            ->field('v.*,GROUP_CONCAT(g.group_name) as group_name')
            ->alias('v')
            ->where('v.visiter_id', $post['visiter_id'])
            ->where('v.business_id', $login['business_id'])
            ->join('wolive_visiter_vgroup vg',"v.vid = vg.vid and vg.service_id = {$login['service_id']}",'LEFT')
            ->join('wolive_vgroup g',"g.id = vg.group_id and g.service_id = {$login['service_id']}",'LEFT')
            ->group('v.vid')
            ->find();
        if (!empty($user['group_name'])) {
            $user['group_name_array'] = explode(',',$user['group_name']);
        } else {
            $user['group_name_array'] = [];
        }
        $data = ['code' => 0, 'data' => $result,'user'=>$user];
        return $data;
    }

    /**
     * 获取所有该用户的聊天记录。
     * [history description]
     * @return [type] [description]
     */
    public function history()
    {
        $post = $this->request->post();
        $login = $_SESSION['Msg'];

        if ($post["hid"] == '') {
            $data = Admins::table('wolive_chats')->where('visiter_id', $post['visiter_id'])->where('business_id', $login['business_id'])->order('timestamp desc,cid asc')->limit(10)->select();
            $vdata = Admins::table('wolive_visiter')->where('visiter_id', $post['visiter_id'])->where('business_id', $login['business_id'])->find();

            foreach ($data as $v) {

                if ($v['direction'] == 'to_service') {
                    $v['avatar'] = $vdata['avatar'];
                    $v['name'] = $vdata['visiter_name'];
                } else {
                    $sdata = Admins::table('wolive_service')->where('service_id', $v['service_id'])->find();
                    $v['avatar'] = $sdata['avatar'];
                    $v['name'] = $sdata['nick_name'];
                }
            }
            reset($data);
        } else {

            $data = Admins::table('wolive_chats')->where('visiter_id', $post['visiter_id'])->where('business_id', $login['business_id'])->where('cid', '<', $post['hid'])->order('timestamp desc,cid asc')->limit(10)->select();
            $vdata = Admins::table('wolive_visiter')->where('visiter_id', $post['visiter_id'])->where('business_id', $login['business_id'])->find();

            foreach ($data as $v) {

                if ($v['direction'] == 'to_service') {
                    $v['avatar'] = $vdata['avatar'];
                    $v['name'] = $vdata['visiter_name'];
                } else {
                    $sdata = Admins::table('wolive_service')->where('service_id', $v['service_id'])->find();
                    $v['avatar'] = $sdata['avatar'];
                    $v['name'] = $sdata['nick_name'];
                }
            }
            reset($data);
        }

        $result = array_reverse($data);
        $data = ['code' => 0, 'data' => $result];
        return $data;
    }


    /**
     * 获取ip地址类.
     *
     * @return string
     */
    public function getipinfo()
    {

        $post = $this->request->get();
        $ip = $post['ip'];
        $data = Ip::find($ip);
        $arr = ['code' => 0, 'data' => $data];
        return $arr;
    }

    /**
     * 标记已看信息.
     *
     * @return mixed
     */
    public function getwatch()
    {
        $login = $_SESSION['Msg'];
        $business_id = $login['business_id'];
        $post = $this->request->post();
        $result = Admins::table('wolive_chats')->where('visiter_id', $post['visiter_id'])->where('business_id', $business_id)->update(['state' => 1]);
        $arr = ['code' => 0, 'msg' => 'success', 'data' => ''];
        return $arr;
    }

    /**
     * 获取未看信息条数类.
     *
     * @return mixed
     */
    public function getmessage()
    {
        $login = $_SESSION['Msg'];
        $business_id = $login['business_id'];
        $post = $this->request->post();

        $channel = $post['channel'];

        $str = pack('H*', $channel);

        $arr = explode("/", $str);

        $visiter_id = $arr[0];

        $result = Admins::table('wolive_chats')->where('visiter_id', $visiter_id)->where('business_id', $business_id)->where('state', 'unread')->count();
        $data = ['code' => 0, 'data' => $result];
        return $data;
    }

    /**
     * 拖到黑名单.
     *
     * @return mixed
     */
    public function blacklist()
    {
        $post = $this->request->post();
        $login = $_SESSION['Msg'];

        $result = Admins::table('wolive_queue')->where('visiter_id', $post['visiter_id'])->where('business_id', $login['business_id'])->update(['state' => 'in_black_list']);

        $arr = ['code' => 0, 'msg' => 'success', 'data' => $result];
        return $arr;

    }

    /**
     * 移出黑名单
     *
     * @return array
     */
    public function removeblacklist()
    {
        $post = $this->request->post();
        $login = $_SESSION['Msg'];

        $result = Admins::table('wolive_queue')->where('visiter_id', $post['visiter_id'])->where('business_id', $login['business_id'])->update(['state' => 'normal']);

        $arr = ['code' => 0, 'msg' => 'success', 'data' => $result];
        return $arr;
    }

    /**
     * 获取该business_id下所用的黑名单信息.
     *
     * @return bool|string
     */
    public function getblackdata()
    {
        $login = $_SESSION['Msg'];

        $visiters = Admins::table('wolive_queue')->where('state', 'in_black_list')->where('business_id', $login['business_id'])->select();

        $blackers = [];
        foreach ($visiters as $v) {
            $data = Admins::table('wolive_visiter')->where('visiter_id', $v['visiter_id'])->where('business_id', $login['business_id'])->find();
            $blackers [] = $data;
        }

        $data = ['code' => 0, 'data' => $blackers];

        return $data;
    }


    /**
     * 设置常用语默认值.
     *
     * @return mixed
     */
    public function settalkdefa()
    {
        $login = $_SESSION['Msg'];
        $post = $this->request->post();

        $result = Admins::table('wolive_sentence')->where('sid', $post['tid'])->update(['state' => 'using']);
        $res = Admins::table("wolive_sentence")->where('sid', '<>', $post['tid'])->where('service_id',$login['service_id'])->update(['state' => 'unuse']);

        $arr = ['code' => 0, 'msg' => 'success', 'data' => $result];
        return $arr;
    }

    /**
     * 设置客服离线优先
     *
     * @return array
     */
    public function setofflinefirst()
    {
        $post = $this->request->post();
        $type = $post['type']==1 ? 1:0;
        $result = Admins::table('wolive_service')->where('service_id', $post['id'])->update(['offline_first' => $type]);

        $arr = ['code' => 0, 'msg' => 'success', 'data' => $result];
        return $arr;
    }
    /**
     * 常用语删除.
     *
     * @return bool
     */
    public function tdelete()
    {
        $post = $this->request->post();
        $result = Admins::table('wolive_sentence')->where('sid', $post['tid'])->delete();


        $arr = ['code' => 0, 'msg' => 'success', 'data' => $result];

        return $arr;
    }

    /**
     * 查看历史记录类.
     *
     * @return mixed
     */
    public function getviews()
    {

        $login = $_SESSION['Msg'];
        $post = $this->request->post();
        $vid = $post["visiter_id"];
        $service_id = $post['service_id'];

        $business_id = $login['business_id'];
        $time = $post['puttime'];
        if ($time == 1) {
            $puttime = strtotime("-1 month");
            $result = Admins::table('wolive_chats')->where(['visiter_id' => $vid, 'business_id' => $business_id])->where('timestamp', '>', $puttime)->order('timestamp')->select();

            $vdata = Admins::table('wolive_visiter')->where('visiter_id', $vid)->where('business_id', $login['business_id'])->find();
            $sdata = Admins::table('wolive_service')->where('service_id', $service_id)->find();

            foreach ($result as $v) {

                if ($v['direction'] == 'to_service') {

                    $v['avatar'] = $vdata['avatar'];
                } else {

                    $v['avatar'] = $sdata['avatar'];
                }

            }

            reset($result);


        } else if ($time == 7) {
            $puttime = strtotime("-1 week");
            $result = Admins::table('wolive_chats')->where(['visiter_id' => $vid, 'business_id' => $business_id])->where('timestamp', '>', $puttime)->order('timestamp')->select();
            $vdata = Admins::table('wolive_visiter')->where('visiter_id', $vid)->where('business_id', $login['business_id'])->find();
            $sdata = Admins::table('wolive_service')->where('service_id', $service_id)->find();

            foreach ($result as $v) {
                if ($v['direction'] == 'to_service') {
                    $v['avatar'] = $vdata['avatar'];
                } else {
                    $v['avatar'] = $sdata['avatar'];
                }
            }

            reset($result);


        }

        $data = ['code' => 0, 'data' => $result];
        return $data;
    }

    /**
     * 自定义查看历史对话记录类.
     *
     * @return mixed
     */
    public function getdesignForViews()
    {
        $login = $_SESSION['Msg'];
        $post = $this->request->post();
        $cha = $post["channel"];

        $s_time = strtotime($post['start']);
        $e_time = strtotime($post['end']) + 24 * 60 * 60;

        $result = Admins::table('wolive_chats')->where('visiter_id', $cha)->where('timestamp', '>=', $s_time)->where('timestamp', '<=', $e_time)->select();

        foreach ($result as $v) {
            if ($v['direction'] == 'to_service') {
                $data = Admins::table('wolive_visiter')->where('visiter_id', $v['visiter_id'])->where('business_id', $login['business_id'])->find();
                $v['avatar'] = $data['avatar'];
            } else {

                $data = Admins::table('wolive_service')->where('service_id', $v['service_id'])->find();
                $v['avatar'] = $data['avatar'];
            }
        }

        reset($result);


        $data = ['code' => 0, 'data' => $result];
        return $data;
    }

    /**
     * 获取该客服交谈过的所有客服.
     *
     * @return mixed
     */
    public function getvisiters()
    {
        $login = $_SESSION['Msg'];
        $post = $this->request->post();
        $id = $post["service"];

        $visiters = Admins::table('wolive_chats')->field('visiter_id,max(cid) as cid')->where(['service_id' => $id, 'business_id' => $login['business_id']])->group('visiter_id')->order('cid','desc')->select();

        $visiterdata = [];
        foreach ($visiters as $v) {
            $visiterdata[] = $v['visiter_id'];
        }

        if ($visiterdata) {
            $data = Admins::table('wolive_visiter')->where('business_id', $login['business_id'])->where('visiter_id', 'in', $visiterdata)->select();

            $data = array_column(collection($data)->toArray(),null,'visiter_id');
            $datas = [];
            foreach ($visiterdata as $v){
                $datas[] = $data[$v];
            }
        } else {
            $datas = '';
        }


        $data = ['code' => 0, 'data' => $datas];
        return $data;
    }

    /**
     * 获取排队人数.
     *
     * @return mixed
     */
    public function getwaitnum()
    {
        $login = $_SESSION['Msg'];

        if ($login['groupid'] == 0) {
            $result = Admins::table('wolive_queue')->where(['service_id' => 0, 'business_id' => $login['business_id']])->where('state', 'normal')->count();

        } else {
            $result = Admins::table('wolive_queue')->where(['service_id' => 0, 'business_id' => $login['business_id'], 'groupid' => $login['groupid']])->where('state', 'normal')->count();

        }


        $data = ['code' => 0, 'data' => $result];
        return $data;
    }

    /**
     * 获取访客的状态
     *
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getstatus()
    {
        $post = $this->request->post();
        $res = Admins::table('wolive_visiter')->where('channel', $post['channel'])->find();
        $data = ['code' => 0, 'data' => $res];
        return $data;
    }

    /**
     * 获取当前对话信息
     *
     */
    public function getchatnow()
    {
        $post = $this->request->post();
        $login = $_SESSION['Msg'];
        $visiter_id = $post['sdata']['visiter_id'];
        $res = Admins::table('wolive_queue')->where('visiter_id', $visiter_id)->where('business_id', $login['business_id'])->where('service_id', $login['service_id'])->find();

        // var_dump($res['state']);exit;

        $sdata = Admins::table('wolive_visiter')->where('visiter_id', $visiter_id)->where('business_id', $login['business_id'])->find();

        $chats = Admins::table('wolive_chats')->where(['visiter_id' => $visiter_id, 'service_id' => $login['service_id'], 'direction' => 'to_service'])->group('cid desc')->find();

        if ($res['state'] == 'complete') {

            $res = Admins::table('wolive_queue')->where('visiter_id', $visiter_id)->where('business_id', $login['business_id'])->update(['state' => 'normal']);
        }

        $data = ['code' => 0, 'msg' => 'success'];
        return $data;
    }


    public function apply()
    {
        $login = $_SESSION['Msg'];
        $post = $this->request->post();

        $visiter = Admins::table('wolive_visiter')->where('visiter_id', $post['id'])->where('business_id', $login['business_id'])->find();

        if (!$visiter) {

            $data = ['code' => 1, 'msg' => '该访客数据已被清理！'];

            return $data;
        }

        $type = $visiter['state'];

        if ($type == -1) {

            $data = ['code' => 1, 'msg' => '对方不在线'];
            return $data;
        }

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

        $pusher->trigger("cu" . $visiter['channel'], "video", array("message" => "申请视频连接", "channel" => $post['channel'], "avatar" => $post['avatar'], 'username' => $post['name']));
        $data = ['code' => 0, 'msg' => 'success'];

        return $data;

    }

    /**
     * 拒绝视屏类方法
     *
     */
    public function refuse()
    {
        $post = $this->request->post();

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

        $pusher->trigger("cu" . $post['channel'], "video-refuse", array("message" => "对方拒绝视频连接！"));
    }


    public function templateswitch()
    {
        $login = $_SESSION['Msg'];
        if($login['level'] == 'service'){
            return [
                'code' => 1,
                'data' => '没有权限',
            ];
        }
        $post = $this->request->post();
        $type = $post['type'];
        if ($type == 'open') {
            $res = Admins::table('wolive_business')->where('id', $login['business_id'])->update(['template_state' => 'open']);

        } else {
            $res = Admins::table('wolive_business')->where('id', $login['business_id'])->update(['template_state' => 'close']);
        }

        if ($res) {
            $arr = [
                "code" => 0,
                "data" => "修改成功"
            ];
            return $arr;
        } else {
            $arr = [
                "code" => 0,
                "data" => "修改失败"
            ];
            return $arr;
        }
    }

    /**
     * 更改 video
     * @return array
     */
    private function videoswitch()
    {
        $login = $_SESSION['Msg'];
        if($login['level'] == 'service'){
            return [
                'code' => 1,
                'data' => '没有权限',
            ];
        }
        $post = $this->request->post();
        $type = $post['type'];
        if ($type == 'open') {
            $res = Admins::table('wolive_business')->where('id', $login['business_id'])->update(['video_state' => 'open']);

        } else {
            $res = Admins::table('wolive_business')->where('id', $login['business_id'])->update(['video_state' => 'close']);

        }

        if ($res) {
            $arr = [
                "code" => 0,
                "data" => "修改成功"
            ];
            return $arr;
        } else {
            $arr = [
                "code" => 0,
                "data" => "修改失败"
            ];
            return $arr;
        }
    }

    /**
     * 更改 audio
     * @return array
     */
    private function audioswitch()
    {
        $login = $_SESSION['Msg'];
        if($login['level'] == 'service'){
            return [
                'code' => 1,
                'data' => '没有权限',
            ];
        }
        $post = $this->request->post();
        $type = $post['type'];
        if ($type == 'open') {
            $res = Admins::table('wolive_business')->where('id', $login['business_id'])->update(['audio_state' => 'open']);

        } else {
            $res = Admins::table('wolive_business')->where('id', $login['business_id'])->update(['audio_state' => 'close']);

        }

        if ($res) {
            $arr = [
                "code" => 0,
                "data" => "修改成功"
            ];
            return $arr;
        } else {
            $arr = [
                "code" => 0,
                "data" => "修改失败"
            ];
            return $arr;
        }
    }

    /**
     * 更改 voice
     * [voiceswitch description]
     * @return [type] [description]
     */
    public function voiceswitch()
    {
        $login = $_SESSION['Msg'];
        if($login['level'] == 'service'){
            return [
                'code' => 1,
                'data' => '没有权限',
            ];
        }
        $post = $this->request->post();
        $type = $post['type'];
        $res = Admins::table('wolive_business')->where('id', $login['business_id'])->update(['voice_state' => $type]);

        if ($res) {
            $arr = [
                "code" => 0,
                "data" => "修改成功"
            ];
            return $arr;
        } else {
            $arr = [
                "code" => 0,
                "data" => "修改失败"
            ];
            return $arr;
        }
    }

    /**
     * 更改 分配模式
     * [getswitchmodel description]
     * @return [type] [description]
     */
    public function getswitchmodel()
    {
        $login = $_SESSION['Msg'];
        if($login['level'] == 'service'){
            return [
                'code' => 1,
                'data' => '没有权限',
            ];
        }
        $post = $this->request->post();
        $type = $post['type'];
        $res = Admins::table('wolive_business')->where('id', $login['business_id'])->update(['distribution_rule' => $type]);
        if ($res) {
            $arr = [
                "code" => 0,
                "data" => "修改成功"
            ];
            return $arr;
        } else {
            $arr = [
                "code" => 0,
                "data" => "修改失败"
            ];
            return $arr;
        }

    }

    /**
     * 添加或修改tab面版内容
     * [gettab description]
     * @return [type] [description]
     */
    public function gettab()
    {

        $post = $this->request->post('',null,null);
        $post['business_id'] = $_SESSION['Msg']['business_id'];

        if (isset($post['tid'])) {

            $res = Admins::table('wolive_tablist')->where('tid', $post['tid'])->update(['title' => $post['title'], 'content' => $post['content'], 'content_read' => $post['content_read']]);
            $flag = '编辑';
        } else {
            $num = Admins::table('wolive_tablist')->where('business_id',$post['business_id'])->count();
            if ($num>10) {
                return ['code'=>1,'msg'=>'tab已达到上限'];
            }
            $res = Admins::table('wolive_tablist')->insert($post);
            $flag = '添加';
        }

        $arr = ['code' => 0, 'msg' => $flag.'成功'];
        return $arr;
    }

    public function setcustom()
    {
        $post = $this->request->post();
        $post['business_id'] = $_SESSION['Msg']['business_id'];
        if (isset($post['sid'])) {
            $res = Admins::table('wolive_sentence')->where('sid', $post['sid'])->update(['sid' => $post['sid'], 'content' => $post['content']]);
            $arr = ['code' => 0, 'msg' => '编辑成功'];
            return $arr;
        } else {
            $arr = ['code' => 1, 'msg' => '编辑失败'];
            return $arr;
        }
    }

    /**
     * 删除tab面版
     * [getdeleteTab description]
     * @return [type] [description]
     */
    public function getdeleteTab()
    {

        $post = $this->request->post();
        $id = $post['tid'];
        $result = Admins::table('wolive_tablist')->where('tid', $id)->delete();

        if ($result) {
            $arr = ['code' => 0, 'msg' => '删除成功'];
            return $arr;
        }
    }

    /**
     *
     * [getshowTab description]
     * @return [type] [description]
     */
    public function getshowTab()
    {
        $post = $this->request->post();
        $id = $post['tid'];
        $res = Admins::table('wolive_tablist')->where('tid', $id)->find();

        if ($res) {
            $arr = ['code' => 0, 'msg' => 'success', 'data' => $res];
            return $arr;
        }

    }

    /**
     * 删除常见问题
     * [getdeleteQuestion description]
     * @return [type] [description]
     */
    public function getdeleteQuestion()
    {
        $post = $this->request->post();
        $id = $post['qid'];

        $result = Admins::table("wolive_question")->where('qid', $id)->delete();

        if ($result) {
            $arr = ['code' => 0, 'msg' => '删除成功'];
            return $arr;
        }
    }

    /**
     *
     * [uploadVoice description]
     * @return [type] [description]
     */
    public function uploadVoice()
    {

        $file = $this->request->file('file');

        if ($file) {
            $newpath = ROOT_PATH . "/public/assets/upload/voices/";
            $info = $file->move($newpath, time() . ".wav");

            if ($info) {
                $imgname = $info->getFilename();

                $imgpath = $this->base_root . "/assets/upload/voices/" . $imgname;
                $arr = [
                    'data' => [
                        'src' => $imgpath
                    ]
                ];
                return json_encode($arr);
            } else {
                return false;
            }
        }

    }


    /**
     * 图片上传.
     *
     * @return [type] [description]
     */
    public function upload()
    {
        $file = $this->request->file("upload");
        $name = $_FILES["upload"]["name"];

        $arr = explode(".", $name);
        $ext = $arr[1];

        if ($ext == 'html' || $ext == 'htm' || $ext == 'jsp' || $ext == 'php' || $ext == 'js') {

            $error = "不支持该文件格式！";


            $data = [
                "code" => -1,
                "msg" => $error,
                "data" => ""
            ];

            return $data;
        }

        if ($file) {
            $newpath = ROOT_PATH . "/public/upload/images/{$_SESSION['Msg']['business_id']}/";
            $info = $file->move($newpath, time());
            if ($info) {
                $imgname = $info->getFilename();
                $imgpath = $this->base_root."/upload/images/{$_SESSION['Msg']['business_id']}/" . $imgname;

                $data = [
                    "code" => 0,
                    "msg" => "",
                    "data" => $imgpath
                ];

                return $data;
            }
        }
    }

    /**
     * 文件上传.
     *
     * @return [type] [description]
     */
    public function uploadfile()
    {
        $file = $this->request->file("folder");

        $name = $_FILES["folder"]["name"];

        $ext = substr($name, strripos($name, ".") + 1);

        if ($ext == 'html' || $ext == 'htm' || $ext == 'jsp' || $ext == 'php' || $ext == 'js') {

            $error = "不支持该文件格式！";


            $data = [
                "code" => -1,
                "msg" => $error,
                "data" => ""
            ];

            return $data;
        }


        if ($file) {
            $newpaths = ROOT_PATH . "/public/upload/files/{$_SESSION['Msg']['business_id']}";
            $info = $file->move($newpaths, time());
            if ($info) {
                $imgname = $info->getFilename();
                $imgpath = $this->base_root."/upload/files/{$_SESSION['Msg']['business_id']}" . $imgname;

                $data = [
                    "code" => 0,
                    "msg" => "",
                    "data" => $imgpath
                ];

                return $data;
            }
        }
    }

    /**
     *
     * [createFrontjs description]
     * @return [type] [description]
     */
    public function createFrontjs()
    {


        $http_type = ((isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';

        $login = $_SESSION['Msg'];

        $business_id = $login['business_id'];
        $url = $http_type . $_SERVER['HTTP_HOST'];
        $js = @file_get_contents($url.$this->base_root . "/assets/front/dianqilai_" . $login['business_id'] . ".js");


        $group = Admins::table('wolive_group')->where('business_id', $login['business_id'])->select();

        $html = '';
        foreach ($group as $v) {

            $html .= '<p class="dianqilai-item" onclick="dianqilai.blank(' . $v['id'] . ')">' . $v['groupname'] . '</p>';
        }

        try {

            $file = file_put_contents(ROOT_PATH . "/public/assets/front/dianqilai_" . $login['business_id'] . ".js", "
             
             /**
              *
              * wolive 标准窗口,js代码
              * 
              */
             
            var head = document.getElementsByTagName('head')[0];
            var link = document.createElement('link');
                link.type='text/css';
                link.rel = 'stylesheet';
                link.href ='{$url}{$this->base_root}/assets/css/index/dianqilai_online.css';
                head.appendChild(link);

            var dianqilai ={
                 visiter_id:'',
                 visiter_name:'',
                 avatar:'',
                 product:'',
                 open:function(){
                    var d =document.getElementById('wolive-box');
                    if(!d){
                      var div =document.createElement('div');
                      div.id ='dianqilai-kefu';
                      div.className +='dianqilai-form';
                      document.body.appendChild(div);
                      var w =document.getElementById('dianqilai-kefu');
                      w.innerHTML=' <i class=\"dianqilai-icon\"></i><p class=\"dianqilai-item\" onclick=\"dianqilai.blank(0)\" >在线咨询</p>" . $html . "';
                    }

                 },
                 blank:function(groupid){

                

                  var web =encodeURI('{$url}{$this->base_root}/index/index/home?visiter_id='+this.visiter_id+'&visiter_name='+this.visiter_name+'&avatar='+this.avatar+'&business_id={$business_id}&groupid='+groupid+'&product='+this.product);
                      
                  var moblieweb = encodeURI('{$url}{$this->base_root}/mobile/index/home?visiter_id='+this.visiter_id+'&visiter_name='+this.visiter_name+'&avatar='+this.avatar+'&business_id={$business_id}&groupid='+groupid+'&product='+this.product);

                   if ((navigator.userAgent.match(/(phone|pad|pod|iPhone|iPod|ios|iPad|Android|Mobile|BlackBerry|IEMobile|MQQBrowser|JUC|Fennec|wOSBrowser|BrowserNG|WebOS|Symbian|Windows Phone)/i))) {
                     window.open(moblieweb); 
                   }else{
                     window.open(web); 
                   }
                 },
            }

            window.onload = dianqilai.open();
        ");

            if ($file) {
                if ($js) {
                    $path = $url.$this->base_root . "/assets/front/dianqilai_" . $login['business_id'] . ".js?v=" . time();
                    $data = ['code' => 0, 'msg' => '新生成js成功!', 'data' => $path];
                    return $data;
                } else {
                    $path = $url.$this->base_root . "/assets/front/dianqilai_" . $login['business_id'] . ".js";
                    $data = ['code' => 0, 'msg' => '生成js成功!', 'data' => $path];
                    return $data;
                }
            }
        } catch (\Exception $e) {

            $error = $e->getMessage();
            $data = ['code' => 1, 'msg' => $error, 'data' => ''];
            return $data;
        }
    }

    /**
     *
     * [createMinjs description]
     * @return [type] [description]
     */
    public function createMinjs()
    {

        $http_type = ((isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';


        $login = $_SESSION['Msg'];
        $business_id = $login['business_id'];
        $url = $http_type . $_SERVER['HTTP_HOST'];

        $js = @file_get_contents($url.$this->base_root . "/assets/layer/dianqilai_" . $login['business_id'] . ".js");

        $group = Admins::table('wolive_group')->where('business_id', $login['business_id'])->select();

        $html = '';
        foreach ($group as $v) {
            $html .= '<p class="dianqilai-item" onclick="dianqilai.connenct(' . $v['id'] . ')">' . $v['groupname'] . '</p>';
        }

        try {

            $file = file_put_contents(ROOT_PATH . "/public/assets/layer/dianqilai_" . $login['business_id'] . ".js", "
            /**
             *
             * wolive 浮层版 客服咨询js
             * [wolive description]
             * @return {[type]} [description]
             */

                var head = document.getElementsByTagName('head')[0];
                var link = document.createElement('link');
                    link.type='text/css';
                    link.rel = 'stylesheet';
                    link.href ='{$url}{$this->base_root}/assets/css/index/dianqilai_online.css';
                    head.appendChild(link);

                var dianqilai ={
                     visiter_id:'',
                     visiter_name:'',
                     avatar:'',
                     product:'',
                     open:function(){
                        var d =document.getElementById('wolive-box');
                        if(!d){
                            var div =document.createElement('div');
                            div.id =\"dianqilai-kefu\";
                            div.className +='dianqilai-form';
                            document.body.appendChild(div);
                            var w =document.getElementById('dianqilai-kefu');
                            w.innerHTML=' <i class=\"dianqilai-icon\"></i><p class=\"dianqilai-item\" onclick=\"dianqilai.connenct(0)\" >在线咨询</p>" . $html . "';
                        }
                     },
                     connenct:function(groupid){
                      var id =groupid;
                      var web =encodeURI('{$url}{$this->base_root}/layer?visiter_id='+this.visiter_id+'&visiter_name='+this.visiter_name+'&avatar='+this.avatar+'&business_id={$business_id}&groupid='+groupid+'&product='+this.product);
                      
                      var moblieweb = encodeURI('{$url}{$this->base_root}/mobile/index/home?visiter_id='+this.visiter_id+'&visiter_name='+this.visiter_name+'&avatar='+this.avatar+'&business_id={$business_id}&groupid='+groupid+'&product='+this.product);

                       if ((navigator.userAgent.match(/(phone|pad|pod|iPhone|iPod|ios|iPad|Android|Mobile|BlackBerry|IEMobile|MQQBrowser|JUC|Fennec|wOSBrowser|BrowserNG|WebOS|Symbian|Windows Phone)/i))) {
                        
                         window.open(moblieweb); 
                       
                       }else{

                       var s =document.getElementById('wolive-talk');
                        
                       if(!s){

                            var div = document.createElement('div');
                            div.id =\"wolive-talk\";
                            div.name=id;
                            document.body.appendChild(div);
                            div.innerHTML='<i class=\"dianqilai-close\" onclick=\"dianqilai.narrow()\"></i><iframe id=\"wolive-iframe\" src=\"'+web+'\"></iframe>'
                          
                        }else{
                           
                            var title =s.name;
                            if(title == groupid){
                                s.style ='display:block';
                            }else{
                                s.parentNode.removeChild(s);
                                dianqilai.connenct(groupid); 
                            }
                        }
                      }
                     },
                     narrow:function(){
                        document.getElementById('wolive-talk').style=\"display:none\";
                     }
                }

                window.onload =dianqilai.open();

        ");

            if ($file) {
                if ($js) {
                    $path = $url.$this->base_root . "/assets/layer/dianqilai_" . $login['business_id'] . ".js?v=" . time();
                    $data = ['code' => 0, 'msg' => '新生成js成功!', 'data' => $path];
                    return $data;
                } else {
                    $path = $url.$this->base_root . "/assets/layer/dianqilai_" . $login['business_id'] . ".js";
                    $data = ['code' => 0, 'msg' => '生成js成功!', 'data' => $path];
                    return $data;
                }
            }

        } catch (\Exception $e) {
            $error = $e->getMessage();
            $data = ['code' => 1, 'msg' => $error, 'data' => ''];
            return $data;
        }

    }

    protected function formatTime($time)
    {
        $rtime = date("m-d H:i", $time);
        $htime = date("H:i", $time);
        $time = time() - $time;
        if ($time < 60) {
            $str = '刚刚';
        } elseif ($time < 60 * 60) {
            $min = floor($time / 60);
            $str = $min . '分钟前';
        } elseif ($time < 60 * 60 * 24) {
            $h = floor($time / (60 * 60));
            $str = $h . '小时前 ';
        } elseif ($time < 60 * 60 * 24 * 3) {
            $d = floor($time / (60 * 60 * 24));
            if ($d == 1) {
                $str = '昨天 ' . $htime;
            } else {
                $str = '前天 ' . $htime;
            }
        } else {
            $str = $rtime;
        }
        return $str;
    }

    public function callback()
    {
        $http_type = ((isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        $web = $http_type . $_SERVER['HTTP_HOST'];
        $action = $web.request()->root();
        $url = $_SESSION['Msg']['business']['push_url'];
        $data = $this->request->param();
        $message = $data['message'];
        $group = Db::name('wolive_queue')->where('visiter_id',$message['visiter_id'])->where('business_id',$message['business_id'])->value('groupid');
        $visiter = Db::name('wolive_visiter')->where('visiter_id',$message['visiter_id'])->where('business_id',$message['business_id'])->find();
        $message['group'] = $group;
        $message['state'] = $visiter['state'];
        $message['service_name'] = $_SESSION['Msg']['nick_name'];
        $query = http_build_query([
            "visiter_id" => $message['visiter_id'],
            "visiter_name" => $visiter['visiter_name'],
            "business_id" => $message['business_id'],
            "avatar" => '',
            "groupid" => $group
        ]);
        $returnurl = "{$action}/index/index/home?$query";
        $message['service_url'] = urlencode($returnurl);

        $key = "callback_".$_SESSION['Msg']['business_id']."_".$_SESSION['Msg']['service_id'];
        if (isset($_SESSION[$key])) {
            $curl = new CurlUtils($url);
            $res = $curl->post($message);
            if ($res !== false) {
                echo 'success';
            } else {
                echo 'error';
            }
            unset($_SESSION[$key]);
        } else {
            echo 'request lock~';
        }
    }

    public function setpushurl()
    {
        $pushurl = $this->request->param('push_url','',null);
        $pattern="#(http|https)://(.*\.)?.*\..*#i";
        $login = $_SESSION['Msg'];
        if(preg_match($pattern,$pushurl)){
            if($login['level'] == 'service'){
                return [
                    'code' => 1,
                    'data' => '没有权限',
                ];
            }
            $res = Admins::table('wolive_business')->where('id', $login['business_id'])->update(['push_url' => $pushurl]);
            if ($res !== false) {
                return json(['code'=>0,'msg'=>'保存成功']);
            } else {
                return json(['code'=>1,'msg'=>'保存失败']);
            }
        } elseif(empty($pushurl)){
            Admins::table('wolive_business')->where('id', $login['business_id'])->update(['push_url' => $pushurl]);
            return json(['code'=>0,'msg'=>'修改成功']);
        } else{
            return json(['code'=>1,'msg'=>'url格式不正确']);
        }
    }

    public function testtpl(){
        Log::info("===============Test Tpl Message Begin===============");
        $login = $_SESSION['Msg'];
        Log::info($login);
        $open_id = $this->request->post('open_id');
        Log::info('open_id:'.$open_id);
        $service_id = $this->request->post('service_id');
        Log::info('service_id:'.$service_id);
        $type = $this->request->post('type');
        Log::info('type:'.$type);
        $wechat = WechatPlatform::get(['business_id'=>$login['business_id']]);
        Log::info($wechat);
        if ($login['business']['template_state'] == 'close') {
            Log::error('Tpl is closed');
            Log::info("===============Test Tpl Message End===============");
            return json(['code'=>1,'msg'=>'请先打开模板消息开关(设置-通用设置)']);
        }
        try {
            if ($type == 'visitor_tpl') {
                TplService::send($login['business_id'],$open_id,url('weixin/login/callback',['business_id'=>$login['business_id'],'service_id'=>$service_id],true,true),$wechat['visitor_tpl'],[
                    "first"  => "您有新访客！",
                    "keyword1"   => "测试发送新访客提醒模板消息",
                    "keyword2"  => date('Y-m-d H:i:s',time()),
                    "remark" => $login['business']['business_name']."提示:有新客户啦,快去撩一把~",
                ]);
            } else {
                TplService::send($login['business_id'],$open_id,url('weixin/login/callback',['business_id'=>$login['business_id'],'service_id'=>$service_id],true,true),$wechat['msg_tpl'],[
                    "first"  => "你有一条新的信息!",
                    "keyword1"   => "测试",
                    "keyword2"  => "测试发送新消息提醒模板消息",
                    "keyword3"  => "测试",
                    "remark" => $login['business']['business_name']."提示:客户等不及啦,快去回复吧~",
                ]);
            }
        }catch (\Exception $exception) {
            Log::error($exception->getMessage());
            Log::info("===============Test Tpl Message End===============");
            return json(['code'=>1,'msg'=>$exception->getMessage()]);
        }
        Log::info("===============Test Tpl Message End===============");
        return json(['code'=>0,'msg'=>'success']);
    }

    /**
     * 推送客服评价
     */
    public function pushComment()
    {
        $login = $_SESSION['Msg'];
        $arr = $this->request->post('',null,null);

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

        $visiter = Visiter::get(['business_id'=>$login['business_id'],'visiter_id'=>$arr['visiter_id']]);

        if ($visiter['state'] == 'offline') {
            return json([
                'code' => 1,
                'msg' => '访客已离线,无法推送评价'
            ]);
        }

        $queue = Queue::get(['business_id'=>$login['business_id'],'visiter_id'=>$arr['visiter_id']]);
        if (!$queue['remind_comment']) {
            $data = CommentSetting::get(['business_id'=>$login['business_id']]);
            if (!empty($data)) {
                $data['comments'] = json_decode($data['comments'],true);
            } else {
                return json([
                    'code' => 1,
                    'msg' => '客服评价尚未设置'
                ]);
            }
            $queue->save(['remind_comment'=>1]);
            $arr['business_id'] = $login['business_id'];
            $channel = bin2hex($arr['visiter_id'] . '/' . $arr['business_id']);
            $pusher->trigger("cu" . $channel, 'push-comment', array('message' => $data));
            return json([
                'code' => 0,
                'msg' => '已推送'
            ]);
        } else {
            return json([
                'code' => 1,
                'msg' => '您已经推送过评价了'
            ]);
        }
    }
}
