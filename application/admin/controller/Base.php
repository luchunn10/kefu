<?php
/**
 * Handler File Class
 *
 * @author liliang <liliang@wolive.cc>
 * @email liliang@wolive.cc
 * @date 2017/06/01
 */

namespace app\admin\controller;

use app\Common;
use app\platform\model\Admin;
use app\platform\model\Business;
use app\weixin\model\Weixin;
use think\Controller;
use think\Cookie;
use think\Loader;
use think\Response;
use think\Session;
use app\admin\model\Admins;

/**
 * 基础验证是否登录.
 */
class Base extends Controller
{

    protected $base_root = null;
    /**
     * 验证session.
     *
     * @return void
     */
    public function _initialize()
    {
        $token = Cookie::get('service_token');
        if (!empty($token)) {
            $common = new Common();
            $uid = $common->encrypt($token,'D','dianqilai_service');
            $data = Admins::table("wolive_service")
                ->where('service_id', $uid)
                ->find();
            if ($data) {
                $_SESSION['Msg'] = $data->getData();
                $business = Business::get($_SESSION['Msg']['business_id']);
                $_SESSION['Msg']['business'] = $business->getData();
            }
        }

        if (empty($_SESSION['Msg']) || !isset($_SESSION['Msg'])) {
            $this->redirect('admin/login/index');
        }

        $login = $_SESSION['Msg'];
        $res =Admins::table('wolive_business')->where('id',$login['business_id'])->find();

        if ($res['is_recycle'] || $res['is_delete']) {
            \session('Msg',null);
            $this->error('系统已被回收或封禁');
        }

        if ($res['expire_time'] < time() && $res['expire_time'] != 0) {
            \session('Msg',null);
            $this->error('系统已过期');
        }

        $group =Admins::table('wolive_group')->where('business_id',$login['business_id'])->select();

        $groupjson =json_encode($group);

        $temp = $login;
        unset($temp['copyright']);
        unset($temp['business']['copyright']);
        $data = json_encode($temp);
        $app_key = app_key;

        $arr = parse_url(whost);

        if ($arr['scheme'] == 'ws') {
            $value = 'false';
            $port = 'wsPort';
        } else {
            $value = 'true';
            $port = 'wssPort';
        }
        $basename = request()->root();
        if (pathinfo($basename, PATHINFO_EXTENSION) == 'php') {
            $basename = dirname($basename);
        }
        $this->base_root = $basename;
        $this->assign('baseroot',$this->base_root);
        $service = Weixin::get(['service_id'=>$_SESSION['Msg']['service_id']]);
        $this->assign('referer',session('Platform.referer'));

        $admin = Admin::count();
        if (empty($admin)) {
            $is_we7 = 1;
        } else {
            $is_we7 = 0;
        }
        $this->assign('we7_referer',session('zjhjdql.referer'));
        $this->assign('is_we7',$is_we7);
        $this->assign('is_bind_wechat',$service ?1:0);
        $this->assign('seo',$_SESSION['Msg']['business']);
        $this->assign('app_key', $app_key);
        $this->assign('whost',$arr['host']);
        $this->assign('value', $value);
        $this->assign('wport', wport);
        $this->assign('arr', $login);
        $this->assign('data', $data);
        $this->assign('port', $port);
        $this->assign('group',$groupjson);
        $this->assign('voice',$res['voice_state']);
        $this->assign('voice_address',$res['voice_address']);

    }

}
