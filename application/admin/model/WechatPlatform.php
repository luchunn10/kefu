<?php
/**
 * Created by PhpStorm.
 * User: Andy
 * Date: 2019/2/18
 * Time: 9:58
 */
namespace app\admin\model;

use think\Model;

class WechatPlatform extends Model
{
    protected $field = true;

    protected $table = 'wolive_wechat_platform';

    public static function edit($post)
    {
        $model = self::get(['business_id'=>$post['business_id']]);
        if (empty($model)) {
            $res = self::create($post,true);
        } else {
            $model->app_id = $post['app_id'];
            $model->app_secret = $post['app_secret'];
            $model->msg_tpl = $post['msg_tpl'];
            $model->customer_tpl = $post['customer_tpl'];
            $model->visitor_tpl = $post['visitor_tpl'];
            $model->desc = $post['desc'];
            $res = $model->save();
        }
        return $res;
    }
}