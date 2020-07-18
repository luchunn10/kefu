<?php
namespace app\manager\controller;

use app\admin\model\Admins;
use app\platform\model\Business;
use app\platform\model\Service;
use think\File;
use think\Paginator;


Class Index extends Base
{
	Public function index(){
    
      $ftime =date('Y-m-d',time());
      $frtime =strtotime($ftime);
      $time =time();
      $t = strtotime(date('Y-m-d'));
      $login = $_SESSION['Msg'];

     if(!isset($_POST['key'])){

      $res =Admins::table('wolive_business')->distinct(true)->field('id')->paginate();

      $data=[];

      foreach ($res as $vg) {
          $v =$vg['id'];
          $cdata=[];
          $web =Admins::table('wolive_business')->where('id',$v)->where('is_delete',0)->find();

          if(!$web){
              continue;
          }
          $cdata['is_recycle'] = $web['is_recycle'];
          $cdata['is_delete'] = $web['is_delete'];
          // 正在谈话人数
          $talknum =Admins::table('wolive_queue')->where(['business_id'=>$v])->where('state','normal')->where("service_id",'<>',0)->count();
          // 在线客服人数
          $services=Admins::table("wolive_service")->where(['business_id'=>$v,'state'=>'online'])->count();
          // 正在排队人数
          $waitnum =Admins::table("wolive_queue")->where(['business_id'=>$v])->where("service_id",0)->count();
          // 接入总人数
          $totalvisit=Admins::table("wolive_chats")->distinct(true)->field('visiter_id')->where('business_id',$v)->count();
          // 今日会话量
          $nowchat = Admins::table("wolive_chats")->where('business_id',$v)->where('timestamp', '>', "{$t}")->where('timestamp', '<=', time())->count();
          // 会话总量
          $totalchat =Admins::table('wolive_chats')->where('business_id',$v)->count();
          // 今日留言量
          $nowmsg = Admins::table('wolive_message')->where('business_id',$v)->where('timestamp', '>', "{$t}")->where('timestamp', '<=', time())->count();
          // 留言总量
          $totalmsg = Admins::table('wolive_message')->where('business_id',$v)->count();
          


          $cdata['web']=$v;
          $cdata['name']=$web['business_name'];
          $cdata['talk']=$talknum;
          $cdata['service']=$services;
          $cdata['wait']=$waitnum;
          $cdata['visited']=$totalvisit;
          $cdata['nowchat']=$nowchat;
          $cdata['totalchat']=$totalchat;
          $cdata['nowmsg']=$nowmsg;
          $cdata['totalmsg']=$totalmsg;
          $data[]=$cdata;
      }

      $page = $res->render();
         $this->assign('key','');

      $this->assign("datas",$data);
      $this->assign('page', $page);

     }else{



       $key =$_POST['key'];
       $res =Admins::table('wolive_business')->distinct(true)->field('id')->where('id','like','%'.$key.'%')->whereOr('business_name','like','%'.$key.'%')->paginate(10);
       $data=[];

      foreach ($res as $vg) {
          $v =$vg['id'];
          $cdata=[];
          // 正在谈话人数
         $talknum =Admins::table('wolive_queue')->where(['business_id'=>$login['business_id']])->where('state','normal')->where("service_id",'<>',0)->count();
          // 在线客服人数
          $services=Admins::table("wolive_service")->where(['business_id'=>$login['business_id'],'state'=>'online'])->count();
          // 正在排队人数
          $waitnum =Admins::table("wolive_queue")->where(['business_id'=>$login['business_id']])->where("service_id",0)->count();
          // 接入总人数
          $totalvisit=Admins::table("wolive_chats")->distinct(true)->field('visiter_id')->where('business_id',$login['business_id'])->count();
          // 今日会话量
          $nowchat = Admins::table("wolive_chats")->where('business_id',$login['business_id'])->where('timestamp','like',"{$time}%")->count();    
          // 会话总量
          $totalchat =Admins::table('wolive_chats')->where('business_id',$v)->count();
          // 今日留言量
          $nowmsg = Admins::table('wolive_message')->where('business_id',$login['business_id'])->where('timestamp','like',"{time}%")->count();
          // 留言总量
          $totalmsg = Admins::table('wolive_message')->where('business_id',$login['business_id'])->count();

          $web =Admins::table('wolive_business')->where('id',$v)->find();

          if($web){
            $cdata['state'] ='open';
          }else{
            $cdata['state']= 'close';
          }

          $cdata['is_recycle'] = $web['is_recycle'];
          $cdata['is_delete'] = $web['is_delete'];

          $cdata['web']=$v;
          $cdata['name']=$web['business_name'];
          $cdata['talk']=$talknum;
          $cdata['service']=$services;
          $cdata['wait']=$waitnum;
          $cdata['visited']=$totalvisit;
          $cdata['nowchat']=$nowchat;
          $cdata['totalchat']=$totalchat;
          $cdata['nowmsg']=$nowmsg;
          $cdata['totalmsg']=$totalmsg;
          $data[]=$cdata;
      }

      $page = $res->render();

      $this->assign('key',$key);
      $this->assign("datas",$data);
      $this->assign('page', $page);

     }
      
    $this->assign('title','首页');

     
	  return $this->fetch();
	}

  public function account()
  {
      return $this->fetch();
  }
  
  public function application()
  {
      return $this->fetch();
  }
  
  public function setting()
  {
      return $this->fetch();
  }
  
  public function clean()
  {
      return $this->fetch();
  }


  public function entry()
  {
      $id = $this->request->param('id');
      $condition = [
          'id' => $id,
          'is_delete' => 0,
      ];
      $app = Business::get($condition);
      if (!$app) {
          $this->redirect('app/index');
          return;
      }

      $service = Service::get(['business_id'=>$id,'level'=>'super_manager']);
      session('Msg',$service->toArray());
      session('Msg.business',$app->toArray());
      session('Manager.referer',$id);
      $this->redirect(url('admin/index/index'));
      return $this->fetch();
  }
	// public function edit()
 //    {
 //        $id = $this->request->param('id');
 //        if ($this->request->isPost()) {
 //            $post = $this->request->param();
 //            if (isset($post['no_expire_time'])) {
 //                $post['expire_time'] = 0;
 //                unset($post['no_expire_time']);
 //            } else {
 //                $post['expire_time'] = strtotime($post['expire_time'] . ' 23:59:59');
 //            }
 //            $business = Business::get(['admin_id'=>$post['admin_id'],'business_name'=>$post['business_name'],'id'=>['<>',$post['id'],'is_delete'=>0]]);
 //            if ($business) {
 //                return ['code'=>1,'msg'=>'客服系统名称已存在'];
 //            }
 //            $res = Business::editBusiness($post);
 //            if ($res !== false) {
 //                return ['code'=>0,'msg'=>'操作成功'];
 //            } else {
 //                return ['code'=>1,'msg'=>'操作失败，请重试'];
 //            }
 //        } else {
 //            $model = Business::get($id);
 //            $this->assign('model',$model);
 //            $this->assign('title','编辑');
 //            return $this->fetch();
 //        }

 //    }



}