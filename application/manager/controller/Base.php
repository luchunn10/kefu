<?php
/**
 * Handler File Class
 *
 * @author liliang <liliang@wolive.cc>
 * @email liliang@wolive.cc
 * @date 2017/06/01
 */

namespace app\manager\controller;

use app\platform\model\Admin;
use app\platform\model\Business;
use think\Controller;
use app\admin\model\Admins;

/**
 * 基础验证是否登录.
 */
class Base extends Controller
{

    /**
     * 验证session.
     *
     * @return void
     */
    public function _initialize()
    {
        $login = $_SESSION['Msg'];
        $res =Admins::table('wolive_business')->where('id',$login['business_id'])->find();
        $group =Admins::table('wolive_group')->where('business_id',$login['business_id'])->select();

        $groupjson =json_encode($group);

        $data = json_encode($login);
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

        $this->assign('referer',session('Platform.referer'));

        $admin = Admin::count();
        if (empty($admin) && $res['admin_id'] == 1) {
            $is_we7 = 1;
        } else {
            $is_we7 = 0;
        }
        $this->assign('we7_referer',session('zjhjdql.referer'));
        $this->assign('is_we7',$is_we7);
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

        $_SESSION['Super'] = $_SESSION['Msg'];
        $admin = Admin::count();

        if(empty($_SESSION['Super']['level']) || $_SESSION['Super']['level'] != 'super_manager'){
            $this->error('您不是超级用户!');
        }
        $res = Business::get($_SESSION['Super']['business_id']);
        if (empty($admin) && $res['admin_id'] == 1) {
            $is_we7 = 1;
        } else {
            $is_we7 = 0;
        }
        if (!$is_we7) {
            $this->error('您不是微擎超级用户!');
        }
        $login = $_SESSION['Msg'];
        $result = Admins::table("wolive_service")->where('service_id', $login['service_id'])->find();
        $this->assign('arr', $result);
        $this->assign('title', "超级管理");
        $this->assign('part', "超级管理");
    }

}
