<?php
/**
 * Handler File Class
 *
 * @author liliang <liliang@wolive.cc>
 * @email liliang@wolive.cc
 * @date 2017/06/01
 */

namespace app\index\controller;

use app\admin\model\RestSetting;
use app\admin\model\WechatPlatform;
use think\Controller;
use app\extra\push\Pusher;
use app\index\model\User;
use app\Common;
use think\Cookie;
use think\Exception;

/**
 *
 * 前台Pc端对话窗口.
 */
class Index extends Controller
{

    public function _initialize()
    {
        $basename = request()->root();
        if (pathinfo($basename, PATHINFO_EXTENSION) == 'php') {
            $basename = dirname($basename);
        }
        $this->assign('basename',$basename);
    }

    /**
     *
     * [home description]
     * @return [type] [description]
     */
    public function home()
    {
        $data = $this->request->request('',null,null);
        $data['business_id'] = $this->request->param('business_id');
        $data['groupid'] = $this->request->param('groupid');
        if (isset($data['code']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            try{
                $_SESSION['Custom'] = null;
                Cookie::delete('product_id');
                $wechat = WechatPlatform::get(['business_id' => $data['business_id']]);
                $appid = $wechat['app_id'];
                $appsecret = $wechat['app_secret'];
                $weixin = file_get_contents("https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$appsecret&code={$data['code']}&grant_type=authorization_code");//通过code换取网页授权access_token
                $array = json_decode($weixin,true); //对JSON格式的字符串进行编码
                $info = file_get_contents("https://api.weixin.qq.com/sns/userinfo?access_token={$array['access_token']}&openid={$array['openid']}&lang=zh_CN");
                $infoarray = json_decode($info,true);
                $data['visiter_id'] = $infoarray['openid'];
                $common = new Common();
                $data['visiter_name'] = $common->remove_emoji($infoarray['nickname']);
                $data['avatar'] = $infoarray['headimgurl'];
                if (!isset($data['groupid'])) {
                    $data['groupid'] = 0;
                }

            }catch (Exception $exception) {
                $this->error($exception->getMessage());
            }
        }

        if (!isset($data['product'])) {
            $data['product'] = "";
        }

        if (!isset($data['special'])) {
            $data['special'] = "";
        }

        $str = "visiter_id=" . $data['visiter_id'] . "&visiter_name=" . $data['visiter_name'] . "&avatar=" . $data['avatar'] . "&business_id=" . $data['business_id'] . "&groupid=" . $data['groupid'] . "&product=" . $data['product']."&special=" . $data['special'];

        $common = new Common();

        $newstr = $common->encrypt($str, 'E', 'wolive');

        $a = urlencode($newstr);

        $this->redirect(request()->root().'/index/index?code=' . $a);

    }

    /**
     * 对话窗口页面.
     *
     * @return mixed
     */
    public function index()
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

        $common = new Common();

        $is_mobile = $common->isMobile();

        $url = domain;

        if (isset($_SERVER['HTTP_REFERER'])) {
            $from_url = $_SERVER['HTTP_REFERER'];
        } else {
            $from_url = '';
        }


        $arr = $this->request->get();

        $data = $common->encrypt($arr['code'], 'D', 'wolive');

        if (!$data) {
            $this->redirect(request()->root().'/index/index/errors');
        }

        parse_str($data, $arr2);
        $special = isset($arr2['special']) ? $arr2['special']:null;

        if (!isset($arr2['visiter_id']) || !isset($arr2['visiter_name']) || !isset($arr2['product']) || !isset($arr2['groupid']) || !isset($arr2['business_id']) || !isset($arr2['avatar'])) {
            $this->redirect(request()->root().'/index/index/errors');
        }


        if ($is_mobile) {
            $this->redirect(request()->root().'/mobile/index/home?visiter_id=' . $arr2['visiter_id'] . '&visiter_name=' . $arr2['visiter_name'] . '&avatar=' . $arr2['avatar'] . '&business_id=' . $arr2['business_id'] . '&product=' . $arr2['product'] . '&groupid=' . $arr2['groupid']."&special=".$special);
        }


        $content = json_decode($arr2['product'], true);
        if (!$content) {
            $arr2['product'] = NULL;

        }
        $business_id = htmlspecialchars($arr2['business_id']);
        $visiter_id = htmlspecialchars($arr2['visiter_id']);
        if ($visiter_id === '') {
            if (empty($_SESSION['Custom']['visiter_id'])) {
                $visiter_id = bin2hex(pack('N', time()));
                $_SESSION['Custom']['visiter_id'] = $visiter_id;
            } else {
                $visiter_id = $_SESSION['Custom']['visiter_id'];
            }
        }

        // 判断是否访问过
        if ($visiter_id) {

            if (!isset($_COOKIE['product_id'])) {

                if ($arr2['product'] != NULL) {

                    $product = $arr2['product'];
                    $content = json_decode($arr2['product'], true);
                    if (isset($content['pid']) && isset($content['url']) && isset($content['img']) && isset($content['title']) && isset($content['info']) && isset($content['price'])) {
                        setcookie("product_id", $content['pid'], time() + 3600 * 12);
                        $arr2['timestamp'] = time();

                        $service = User::table('wolive_queue')->where(['visiter_id' => $visiter_id, 'business_id' => $business_id])->find();

                        if ($service) {
                            $service_id = $service['service_id'];
                        } else {
                            $service_id = 0;
                        }

                        $str = '<a href="' . $content['url'] . '" target="_blank" class="wolive_product">';
                        $str .= '<div class="wolive_img"><img src="' . $content['img'] . '" width="100px"></div>';
                        $str .= '<div class="wolive_head"><p class="wolive_info">' . $content['title'] . '</p><p class="wolive_price">' . $content['price'] . '</p>';
                        $str .= '<p class="wolive_info">' . $content['info'] . '</p>';
                        $str .= '</div></a>';


                        $mydata = ['service_id' => $service_id, 'visiter_id' => $visiter_id, 'content' => $str, 'timestamp' => time(), 'business_id' => $business_id, 'direction' => 'to_service'];

                        $pusher->trigger('kefu' . $service_id, 'cu-event', array('message' => $mydata));

                        $chats = User::table('wolive_chats')->insert($mydata);
                    }

                }
            } else {

                $pid = isset($_COOKIE['product_id']) ? $_COOKIE['product_id'] : '';

                if ($arr2['product'] != NULL) {
                    $product = $arr2['product'];
                    $content = json_decode($arr2['product'], true);

                    if (isset($content['pid']) && isset($content['url']) && isset($content['img']) && isset($content['title']) && isset($content['info']) && isset($content['price']) && $content['pid'] != $pid) {

                        $service = User::table('wolive_queue')->where(['visiter_id' => $visiter_id, 'business_id' => $business_id])->find();

                        if ($service) {
                            $service_id = $service['service_id'];
                        } else {
                            $service_id = 0;
                        }

                        $str = '<a href="' . $content['url'] . '" target="_blank" class="wolive_product">';
                        $str .= '<div class="wolive_img"><img src="' . $content['img'] . '" width="100px"></div>';
                        $str .= '<div class="wolive_head"><p class="wolive_info">' . $content['title'] . '</p><p class="wolive_price">' . $content['price'] . '</p>';
                        $str .= '<p class="wolive_info">' . $content['info'] . '</p>';
                        $str .= '</div></a>';

                        $mydata = ['service_id' => $service_id, 'visiter_id' => $visiter_id, 'content' => $str, 'timestamp' => time(), 'business_id' => $business_id, 'direction' => 'to_service'];

                        $pusher->trigger('kefu' . $service_id, 'cu-event', array('message' => $mydata));
                        $chats = User::table('wolive_chats')->insert($mydata);

                    }
                }
            }

        } else {

            if (!isset($_COOKIE['product_id'])) {
			
                if ($arr2['product'] != NULL) {
                    $product = $arr2['product'];
                    $content = json_decode($arr2['product'], true);
                    if (isset($content['pid']) && isset($content['url']) && isset($content['img']) && isset($content['title']) && isset($content['info']) && isset($content['price'])) {
                        setcookie("product_id", $content['pid'], time() + 3600 * 12);
                        $arr2['timestamp'] = time();

                        $service = User::table('wolive_queue')->where(['visiter_id' => $visiter_id, 'business_id' => $business_id])->find();

                        if ($service) {
                            $service_id = $service['service_id'];
                        } else {
                            $service_id = 0;
                        }

                        $str = '<a href="' . $content['url'] . '" target="_blank" class="wolive_product">';
                        $str .= '<div class="wolive_img"><img src="' . $content['img'] . '" width="100px"></div>';
                        $str .= '<div class="wolive_head"><p class="wolive_info">' . $content['title'] . '</p><p class="wolive_price">' . $content['price'] . '</p>';
                        $str .= '<p class="wolive_info">' . $content['info'] . '</p>';
                        $str .= '</div></a>';


                        $mydata = ['service_id' => $service_id, 'visiter_id' => $visiter_id, 'content' => $str, 'timestamp' => time(), 'business_id' => $business_id, 'direction' => 'to_service'];

                        $pusher->trigger('kefu' . $service_id, 'cu-event', array('message' => $mydata));

                        $chats = User::table('wolive_chats')->insert($mydata);
                    }

                }
            } else {

                if ($arr2['product'] != NULL) {		

                    if ($arr2['visiter_id'] != $_SESSION['Custom']['visiter_id']) {

                        $product = $arr2['product'];
                        $content = json_decode($arr2['product'], true);

                        if (isset($content['pid']) && isset($content['url']) && isset($content['img']) && isset($content['title']) && isset($content['info']) && isset($content['price'])) {

                            $service = User::table('wolive_queue')->where(['visiter_id' => $visiter_id, 'business_id' => $business_id])->find();

                            if ($service) {
                                $service_id = $service['service_id'];
                            } else {
                                $service_id = 0;
                            }

                            $str = '<a href="' . $content['url'] . '" target="_blank" class="wolive_product">';
                            $str .= '<div class="wolive_img"><img src="' . $content['img'] . '" width="100px"></div>';
                            $str .= '<div class="wolive_head"><p class="wolive_info">' . $content['title'] . '</p><p class="wolive_price">' . $content['price'] . '</p><p>';
                            $str .= '<p class="wolive_info">' . $content['info'] . '</p>';
                            $str .= '</div></a>';


                            $mydata = ['service_id' => $service_id, 'visiter_id' => $visiter_id, 'content' => $str, 'timestamp' => time(), 'business_id' => $business_id, 'direction' => 'to_service'];

                            $pusher->trigger('kefu' . $service_id, 'cu-event', array('message' => $mydata));
                            $chats = User::table('wolive_chats')->insert($mydata);
                        }

                    } else {

                        $pid = $_COOKIE['product_id'];

                        $product = $arr2['product'];
                        $content = json_decode($arr2['product'], true);
                        // 判断是否是同个商品
                        if (isset($content['pid']) && isset($content['url']) && isset($content['img']) && isset($content['title']) && isset($content['info']) && isset($content['price']) && $content['pid'] != $pid) {

                            $service = User::table('wolive_queue')->where(['visiter_id' => $visiter_id, 'business_id' => $business_id])->find();

                            if ($service) {
                                $service_id = $service['service_id'];
                            } else {
                                $service_id = 0;
                            }

                            $str = '<a href="' . $content['url'] . '" target="_blank" class="wolive_product">';
                            $str .= '<div class="wolive_img"><img src="' . $content['img'] . '" width="100px"></div>';
                            $str .= '<div class="wolive_head"><p class="wolive_info">' . $content['title'] . '</p><p class="wolive_price">' . $content['price'] . '</p>';
                            $str .= '<p class="wolive_info">' . $content['info'] . '</p>';
                            $str .= '</div></a>';


                            $mydata = ['service_id' => $service_id, 'visiter_id' => $visiter_id, 'content' => $str, 'timestamp' => time(), 'business_id' => $business_id, 'direction' => 'to_service'];

                            $pusher->trigger('kefu' . $service_id, 'cu-event', array('message' => $mydata));
                            $chats = User::table('wolive_chats')->insert($mydata);
                        }
                    }

                }
            }
        }

        $channel = bin2hex($visiter_id . '/' . $business_id);
        $visiter_name = htmlspecialchars($arr2['visiter_name']);

        $avatar = htmlspecialchars($arr2['avatar']);

        if ($visiter_name == '') {
            $visiter_name = '游客' . $visiter_id;
        }

        $groupid = htmlspecialchars($arr2['groupid']);

        $app_key = app_key;
        $whost = whost;
        $arr = parse_url($whost);
        if ($arr['scheme'] == 'ws') {

            $port = 'wsPort';
            $value = 'false';
        } else {

            $value = 'true';
            $port = 'wssPort';
        }

        $business = User::table('wolive_business')->where('id', $business_id)->find();
        $rest = RestSetting::get(['business_id'=>$business_id]);
        $state = empty($rest) ? false : $rest->isOpen($business_id,$visiter_id) ;

		$ad= $business['is_ad'];
		$ad_url=$business['ad_url'];

        $this->assign('reststate', $state);
        $this->assign('restsetting',$rest);
        $this->assign('business_name',$business['business_name']);
        $this->assign("type", $business['video_state']);
        $this->assign("atype", $business['audio_state']);
		$this->assign("ad",$ad);
        $this->assign("ad_url",$ad_url);
		$this->assign('app_key', $app_key);
        $this->assign('whost', $arr['host']);
        $this->assign('value', $value);
        $this->assign('wport', wport);;
        $this->assign('port', $port);
        $this->assign('url', $url);
        $this->assign('groupid', $groupid);
        $this->assign('visiter', $visiter_name);
        $this->assign('business_id', $business_id);
        $this->assign('from_url', $from_url);
        $this->assign('channel', $channel);
        $this->assign('visiter_id', $visiter_id);
        $this->assign('avatar', $avatar);
        $this->assign('special',$special);

        return $this->fetch();
    }

    /**
     * 404页面
     */

    public function errors()
    {
        return $this->fetch();
    }

    /**
     * 获取排队数量.
     *
     * @return mixed
     */
    public function getwaitnum()
    {
        $post = $this->request->post();
        $num = User::table('wolive_queue')->where('visiter_id', $post['visiter_id'])->where("service_id", 0)->count();
        return $num;
    }

    public function wechat()
    {
        $business_id = $this->request->param('business_id', '');
        $group_id = $this->request->param('groupid',0);
        empty($business_id) ? abort(500) : '';
        $wechat = WechatPlatform::get(['business_id' => $business_id]);
        $APPID = $wechat['app_id'];
        $REDIRECT_URI = url('index/index/home',['business_id'=>$business_id,'groupid'=>$group_id],true,true);
        $scope = 'snsapi_userinfo';
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $APPID . '&redirect_uri=' . urlencode($REDIRECT_URI) . '&response_type=code&scope=' . $scope . '&state=123#wechat_redirect';
        $this->redirect($url);
    }
}
