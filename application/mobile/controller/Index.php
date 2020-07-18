<?php
/**
 * Handler File Class
 *
 * @author liliang <liliang@wolive.cc>
 * @email liliang@wolive.cc
 * @date 2017/06/01
 */

namespace app\mobile\controller;

use app\admin\model\RestSetting;
use app\extra\push\Pusher;
use think\Controller;
use app\mobile\model\User;
use app\Common;


/**
 *
 * 前台手机端控制器.
 * Class Index
 * @package app\mobile\controller
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
     * 唯一随机数方法
     * [rand description]
     * @param  [type] $len [description]
     * @return [type]      [description]
     */
    public function rand($len)
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
        $string = substr(time(), -3);
        for (; $len >= 1; $len--) {
            $position = rand() % strlen($chars);
            $position2 = rand() % strlen($string);
            $string = substr_replace($string, substr($chars, $position, 1), $position2, 0);
        }
        return $string;
    }


    /**
     *
     * [home description]
     * @return [type] [description]
     */
    public function home()
    {

        $data = $this->request->request('',null,null);

        if (!isset($post['product'])) {
            $post['product'] = "";
        }

        $data['product'] = empty($data['product']) ? '' : $data['product'];
        $data['special'] = isset($data['special']) ? $data['special']:null;

        $str = "visiter_id=" . $data['visiter_id'] . "&visiter_name=" . $data['visiter_name'] . "&avatar=" . $data['avatar'] . "&business_id=" . $data['business_id'] . "&groupid=" . $data['groupid'] . "&product=" . $data['product'] . "&special=".$data['special'];

        $common = new Common();

        $newstr = $common->encrypt($str, 'E', 'wolive');

        $a = urlencode($newstr);

        $this->redirect(request()->root().'/mobile/index?code=' . $a);

    }


    /**
     *
     * 手机端首页.
     *
     * @return mixed
     */
    public function index()
    {

        $url = domain;
        $arr = $this->request->get();

        $common = new Common();

        $data = $common->encrypt($arr['code'], 'D', 'wolive');

        if (!$data) {
            $this->redirect(request()->root().'/index/index/errors');
        }

        parse_str($data, $arr2);
        $special = isset($arr2['special']) ? $arr2['special']:null;

        if (!isset($arr2['visiter_id']) || !isset($arr2['visiter_name']) || !isset($arr2['product']) || !isset($arr2['groupid']) || !isset($arr2['business_id']) || !isset($arr2['avatar'])) {
            $this->redirect(request()->root().'/index/index/errors');
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

        if ($visiter_id) {

            if (!isset($_COOKIE['product_id'])) {
                // 没有product_id
                if ($arr2['product']) {
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

                $pid = $_COOKIE['product_id'];


                if ($arr2['product']) {
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
                // 没有product_id
                if ($arr2['product']) {
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

                $pid = $_COOKIE['product_id'];

                if ($arr2['product']) {
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
        }


        $channel = bin2hex($visiter_id . '/' . $business_id);
        $visiter_name = htmlspecialchars($arr2['visiter_name']);
        if (isset($_SERVER['HTTP_REFERER'])) {
            $from_url = $_SERVER['HTTP_REFERER'];
        } else {
            $from_url = '';
        }
        $avatar = htmlspecialchars($arr2['avatar']);
        $groupid = htmlspecialchars($arr2['groupid']);

        if ($visiter_name == '') {
            $visiter_name = '游客' . $visiter_id;
        }
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
        $this->assign("atype", $business['audio_state']);
        $this->assign('groupid', $groupid);
        $this->assign('app_key', $app_key);
        $this->assign('whost', $arr['host']);
        $this->assign('value', $value);
        $this->assign('wport', wport);
		$this->assign('ad', $ad);
        $this->assign('ad_url', $ad_url);
        $this->assign('port', $port);
        $this->assign('url', $url);
        $this->assign('visiter', $visiter_name);
        $this->assign('business_id', $business_id);
        $this->assign('from_url', $from_url);
        $this->assign('channel', $channel);
        $this->assign('visiter_id', $visiter_id);
        $this->assign('avatar', $avatar);
        $this->assign('special',$special);
        return $this->fetch();
    }

}
