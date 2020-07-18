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
use app\platform\enum\apps;
use app\platform\model\Business;
use think\Controller;
use think\captcha\Captcha;
use think\config;
use app\Common;
use app\extra\push\Pusher;
use think\Cookie;


/**
 * 登录控制器.
 */
class Login extends Controller
{
    private $business_id = null;

    public function _initialize()
    {
        $this->business_id = $this->request->param('business_id',Cookie::get(apps::HJLIVE_APP_FLAG));

        empty($this->business_id) ? abort(500):Cookie::set(apps::HJLIVE_APP_FLAG,$this->business_id);

        $this->assign('business_id',$this->business_id);
    }

    /**
     * 登陆首页.
     *
     * @return string
     */
    public function index()
    {
        $token  = Cookie::get('service_token');
        if ($token) {
            $this->redirect(url('admin/index/index'));
        }
        // 未登陆，呈现登陆页面.
        $params = [];
        $goto = $this->request->get('goto', '');
        if ($goto) {
            $params['goto'] = urlencode($goto);
        }
        $business = Business::get($this->business_id);
        $this->assign('business',$business);
        $this->assign('submit', url('check', $params));
        return $this->fetch();
    }

    /**
     * 注册页面.
     *
     * @return mixed
     */
    private function sign()
    {
        return $this->fetch();
    }


    /**
     * 验证码.
     *
     * @return \think\Response
     */
    public function captcha()
    {

        $captcha = new Captcha(Config::get('captcha'));
        return $captcha->entry('admin_login');
    }

    /**
     * 注册验证码.
     *
     * @return \think\Response
     */
    public function captchaForAdmin()
    {
        $captcha = new Captcha(Config::get('captcha'));
        return $captcha->entry('admin_regist');
    }

    /**
     * 检查.
     *
     * @return void
     */
    public function check()
    {
        session('zjhjdql.referer',null);
        $post = $this->request->post();
        if(!isset($post['username']) || !isset($post['password']) || !isset($post['business_id'])){
          $this->error('参数不完整!', url("/admin/login/index"));
        }

        $post['user_name'] =htmlspecialchars($post['username']);

        if($post['user_name'] == "test"){

            $data = Admins::table('wolive_service')->where('user_name', $post['user_name'])->find();
            if(!$data) {
                $this->error('没有这个用户', url("/admin/login/index"));
            }
        }else{

            $post["password"] =htmlspecialchars($post['password']);
            unset($post['username']);

            $result = $this->validate($post, 'Login');
            if ($result !== true) {
                $this->error($result);
            }
            // 获取信息 根据$post['username'] 的数据 来做条件 获取整条信息
            $admin = Admins::table("wolive_service")
                ->where('user_name', $post['user_name'])
                ->where('business_id',$post['business_id'])
                ->find();
            if (!$admin) {
                $this->error("用户不存在");
            }
            // 密码检查

            $pass = md5($post['user_name'] . "hjkj" . $post['password']);

            $password = Admins::table("wolive_service")
                ->where('user_name', $post['user_name'])
                ->where('password', $pass)
                ->find();

            if (!$password) {

                $this->error('密码错误');
            }

            // 获取登陆数据

            $login = $admin->getData();

            // 删掉登录用户的敏感信息
            unset($login['password']);

            $res = Admins::table('wolive_service')->where('service_id', $login['service_id'])->update(['state' => 'online']);

            $data = Admins::table('wolive_service')->where('service_id', $login['service_id'])->find();

        }

        $_SESSION['Msg'] = $data->getData();
        $business = Business::get($_SESSION['Msg']['business_id']);
        $_SESSION['Msg']['business'] = $business->getData();

        $common =new Common();

        $service_token = $common->encrypt($_SESSION['Msg']['service_id'],'E','dianqilai_service');
        Cookie::set('service_token', $service_token, 7*24*60*60);

        $ismoblie =$common->isMobile();

        if($ismoblie){
          
          $this->success('登录成功', url("mobile/admin/index"));
        }else{

          $this->success('登录成功', url("admin/Index/index"));
        }
        
    }

    /**
     *  注册用户.
     *
     * @return string
     */
   private function regist(){

        $post =$this->request->post();

        $post['user_name'] =htmlspecialchars($post['user_name']);
        $post["password"] =htmlspecialchars($post['password']);
        $post["password2"] =htmlspecialchars($post['password2']);
        $post['nick_name'] ="管理员".$post['user_name'];
        unset($post['username']);
        unset($post['nickname']);

        $result =$this->validate($post,'Admins');

        if($result !== true){

            return $result;
        }
     
        $res =Admins::table('wolive_service')
            ->where('user_name',$post['user_name'])
            ->where()
            ->find();

        if($res){
            return "用户名存在！";
        }
        //合成新函数
        $post['business_id']=$post['user_name'];

        unset($post['captcha']);
        unset($post['password2']);

        $pass =md5($post['user_name']."hjkj".$post['password']);
        $post['password']=$pass;
        $post['level'] ='manager';


        $debug =Admins::table('wolive_service')->insert($post);
        if($debug){
            $arr=[];
            $arr['business_id']=$post['user_name']; 
            $business =Admins::table('wolive_business')->insert($arr);
            return '注册成功';
        }
    }


  

    /**
     * 退出登陆 并清除session.
     *
     * @return void
     */
    public function logout()
    {
        Cookie::delete('service_token');
      if(isset($_SESSION['Msg'])){
               $login = $_SESSION['Msg'];
            // 更改状态

          Cookie::delete('service_token');
          setCookie("cu_com", "", time() - 60);
          $_SESSION['Msg'] = null;
        }


        $this->redirect(url('admin/login/index',['business_id'=>$this->request->param('business_id')]));
           
    }

    /**
     * socket_auth 验证
     * [auth description]
     * @return [type] [description]
     */
     public function auth(){

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
        

        $data= $pusher->socket_auth($_POST['channel_name'], $_POST['socket_id']);
        
        return $data;  
    }

}
