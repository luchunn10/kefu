<?php 
namespace app\layer\controller;

use think\Controller;
use app\extra\push\Pusher;
use app\index\model\User;


/**
 * 
 */
class Index extends Controller
{   

     /**
    * 唯一随机数方法
    * [rand description]
    * @param  [type] $len [description]
    * @return [type]      [description]
    */
    public function rand($len)
    {
        $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
        $string=substr(time(),-3);
        for(;$len>=1;$len--)
        {
            $position=rand()%strlen($chars);
            $position2=rand()%strlen($string);
            $string=substr_replace($string,substr($chars,$position,1),$position2,0);
        }
        return $string;
    }

  
 
	/**
	 * [index description]
	 * @return [type] [description]
	 */
	public function index(){
		$request = $this->request->get();
        $sarr = parse_url(ahost);
        if($sarr['scheme'] == 'https'){
            $state = true;
        }else{
            $state =false;
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

        $app_key = app_key;
        $whost = whost;
        $arr = parse_url($whost);
        if($arr['scheme'] == 'ws'){

            $port ='wsPort';
            $value ='false';
        }else{

            $value ='true';
            $port ='wssPort';
        }
         
        $business_id =htmlspecialchars($request['business_id']);
        $url=domain;
        $groupid=htmlspecialchars($request['groupid']);
        $visiter_id=htmlspecialchars($request['visiter_id']);
    
        $avatar=htmlspecialchars($request['avatar']);

        if(!$visiter_id){
            if(isset($_COOKIE['visiter_id'])){
            	$visiter_id =$_COOKIE['visiter_id'];
            }else{
            	$visiter_id =$this->rand(2);
            	setcookie("visiter_id",$visiter_id,time()+3600*12);
            }        	
        }

        $service =User::table('wolive_queue')->where(['visiter_id'=>$visiter_id,'business_id'=>$business_id])->find();

        if(isset($_SERVER['HTTP_REFERER'])){
           $from_url=$_SERVER['HTTP_REFERER'];
        }else{
           $from_url='';
        }

        $visiter_name =htmlspecialchars($request['visiter_name']);
        
        if($visiter_name == ''){
            $visiter_name='游客'.$visiter_id;
        }


        $business =User::table('wolive_business')->where('id',$business_id)->find();

        $channel=bin2hex($visiter_id.'/'.$business_id);
           
        $this->assign("video",$business['video_state']);
        $this->assign("audio",$business['audio_state']);

        $this->assign('app_key', $app_key);
        $this->assign('whost', $arr['host']);
        $this->assign('value', $value);
        $this->assign('wport', wport);;
        $this->assign('port',$port);
        $this->assign('url',$url);
        $this->assign('groupid',$groupid);
        $this->assign('visiter',$visiter_name);
        $this->assign('business_id',$business_id);
        $this->assign('from_url',$from_url);
        $this->assign('channel',$channel);
        $this->assign('visiter_id',$visiter_id);
        $this->assign('avatar',$avatar);
		
		return  $this->fetch();
	}	
}