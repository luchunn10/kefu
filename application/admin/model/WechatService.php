<?php
/**
 * Created by PhpStorm.
 * User: Andy
 * Date: 2019/2/18
 * Time: 10:35
 */
namespace app\admin\model;

use EasyWeChat\Foundation\Application;

class WechatService
{

    public static function get($bid = null)
    {
        $business_id = !empty($bid) ? $bid : $_SESSION['Msg']['business_id'];
        $wechat = WechatPlatform::get(['business_id'=>$business_id]);
        $option = [
            'app_id' => $wechat['app_id'],
            'secret' => $wechat['app_secret'],
        ];
        return new Application($option);
    }

    public static function getUserinfo($openId)
    {
        $a = self::get()->user;
        $user = $a->get($openId);
        return $user;
    }

}