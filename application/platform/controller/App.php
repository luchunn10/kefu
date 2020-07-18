<?php
/**
 * Created by PhpStorm.
 * User: Andy
 * Date: 2019/1/30
 * Time: 14:54
 */
namespace app\platform\controller;


use app\Common;
use app\platform\model\Admin;
use app\platform\model\Business;
use app\platform\model\Service;
use think\Cookie;
use think\Loader;

class App extends Base
{

    protected $noNeedLogin = [];

    public function index()
    {
        $keyword = $this->request->param('keyword');
        $where = [
            'admin_id' => $this->admin['id'],
            'is_delete' => 0,
        ];
        !empty($keyword) && $where['business_name'] = ['like',"%".trim($keyword)."%"];
        $count = Business::where($where)->count();

        $where['is_recycle'] = 0;
        $list = Business::where($where)->paginate();
        $page = $list->render();

        $this->assign('keyword',$keyword);
        $this->assign('page',$page);
        $this->assign('list',$list);
        $this->assign('app_count',$count);
        return $this->fetch();
    }

	 public function edit(){
			
			 $id = $this->request->param('id');
        if ($this->request->isPost()) {
            $post = $this->request->param();
            $post['admin_id'] = isset($post['admin_id']) ? $post['admin_id'] : $this->admin['id'];
			$is_ad=$post['is_ad'];

            if (isset($post['no_expire_time'])) {
                $post['expire_time'] = 0;
                unset($post['no_expire_time']);
            } else {
                $post['expire_time'] = strtotime($post['expire_time'] . ' 23:59:59');
            }

            $validate = Loader::validate('App');
            $sence = isset($post['id']) ? 'edit': 'insert';

            if(!$validate->scene($sence)->check($post)){
                return ['code'=>1,'msg'=>$validate->getError()];
            }
            if (is_array(json_decode($this->admin['permission'],true))) {
                $is_copyright = in_array('copyright',json_decode($this->admin['permission'],true));
            } else {
                $is_copyright = false;
            }

            if ($this->admin['id']== 1) {
                $is_copyright = true;
            }

            if ($is_copyright == false ) {
                if ((isset($post['logo']) || isset($post['copyright']))) {
                    return ['code'=>1,'msg'=>'您无此权限'];
                }
                $post['logo'] = $this->option['logo'];
                $post['copyright'] = $this->option['copyright'];
            } else {
                $post['copyright'] = $this->request->post('copyright','',null);
            }			

            if (!isset($post['id']) ) {
                $count = Business::where([
                    'admin_id' => $this->admin['id'],
                    'is_delete' => 0,
                ])->count();
                if ($count && $this->admin['app_max_count'] && $count>= $this->admin['app_max_count']) {
                    return ['code'=>1,'msg'=>'客服系统创建数量超过上限'];
                }
                $business = Business::get(['admin_id'=>$post['admin_id'],'business_name'=>$post['business_name'],'is_delete'=>0]);
                if ($business) {
                    return ['code'=>1,'msg'=>'客服系统名称已存在'];
                }
                $res = Business::addBusiness($post);
            } else {

                $business = Business::get(['admin_id'=>$post['admin_id'],'business_name'=>$post['business_name'],'id'=>['<>',$post['id'],'is_delete'=>0]]);
                if ($business) {
                    return ['code'=>1,'msg'=>'客服系统名称已存在'];
                }
                $res = Business::editBusiness($post);
            }
            if ($res !== false) {
                return ['code'=>0,'msg'=>'操作成功'];
            } else {
                return ['code'=>1,'msg'=>'操作失败，请重试'];
            }
        } else {
            $business = null;
            if ($id) {
                $business = Business::get($id);
                $url = url('edit',['id'=>$id]);
            } else {
                $url = url('edit');
            }
            $count = Business::where([
                'admin_id' => $this->admin['id'],
                'is_delete' => 0,
            ])->count();
            $account_max = $this->admin['app_max_count'];
            $account_over_max = !isset($business) && $account_max != 0 && $count >= $account_max;

            if (is_array(json_decode($this->admin['permission'],true))) {
                $is_copyright = in_array('copyright',json_decode($this->admin['permission'],true));
            } else {
                $is_copyright = false;
            }

            if ($this->admin['id']== 1) {
                $is_copyright = true;
            }

			//$ad_type=$post['user_type'];

            $data = [
                'option' => $this->option,
                'action_url' => $url,
                'model' => $business,
                'count' => $count,
                'account_max' => $account_max,
                'account_over_max' =>  $account_over_max,
                'is_copyright' => $is_copyright			
            ];
            $this->assign($data);
            $this->view->engine->layout(false);
            return $this->fetch();
		}		
	 }

    public function entry()
    {
        $id = $this->request->param('id');
        $condition = [
            'id' => $id,
            'admin_id' => $this->admin['id'],
            'is_delete' => 0,
        ];
        if ($this->admin['id']== 1) {
            unset($condition['admin_id']);
        }
        $app = Business::get($condition);
        if (!$app) {
            $this->redirect('app/index');
            return;
        }

        $service = Service::get(['business_id'=>$id,'level'=>'super_manager']);
        session('Msg',$service->toArray());
        session('Msg.business',$app->toArray());
        session('Platform.referer',$id);
        $common = new Common();
        $service_token = $common->encrypt($service['service_id'],'E','dianqilai_service');
        Cookie::delete('service_token');
        Cookie::set('service_token', $service_token, 7*24*60*60);
        $this->redirect(url('admin/index/index'));
        return $this->fetch();
    }

    public function delete()
    {
        $id = $this->request->param('id');
        $condition = [
            'id' => $id,
            'admin_id' => $this->admin['id'],
            'is_delete' => 0,
        ];
        if ($this->admin['id'] == 1) {
            unset($condition['admin_id']);
        }
        $store = Business::get($condition);
        if ($store) {
            $store->is_delete = 1;
            $store->save();
        }
        return [
            'code' => 0,
            'msg' => '操作成功',
        ];
    }

    public function recycle()
    {
        $keyword = $this->request->param('keyword');
        $where = [
            'admin_id' => $this->admin['id'],
            'is_delete' => 0,
            'is_recycle' => 1,
        ];
        !empty($keyword) && $where['business_name'] = ['like',"%".trim($keyword)."%"];
        $list = Business::where($where)->paginate();

        $page = $list->render();
        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('keyword',$keyword);
        return $this->fetch('recycle');
    }

    public function subapp()
    {
        $keyword = $this->request->param('keyword');
        $query = Business::hasWhere('admin',['username'=>['like',"%".$keyword."%"]])->where('Business.is_delete',0)->where('admin_id','<>',$this->admin['id'])->with('service,admin');
        if ($keyword = trim($keyword)) {
            $query->whereOr(function($query) use ($keyword){
                $query->where('business_name','like',"%".$keyword."%")->where('Business.is_delete',0)->where('admin_id','<>',$this->admin['id']);
            });
        }

        $list = $query->paginate();
        $page = $list->render();
        return $this->fetch('subapp', [
            'list' => $list,
            'keyword' => $keyword,
            'page' => $page,
        ]);

        return $this->fetch();
    }

    public function setRecycle()
    {
        $action = $this->request->param('action');
        $id = $this->request->param('id');

        $action = $action == '1' ? '1':'0';
        $condition = [
            'id' => $id,
            'admin_id' => $this->admin['id'],
        ];

        if ($this->admin['id'] == 1) {
            unset($condition['admin_id']);
        }

        $business = Business::get($condition);
        if ($business) {
            $res = $business->save(['is_recycle'=>$action]);
        }

        if($res){
            return [
                'code' => 0,
                'msg' => '操作成功',
            ];
        }else{
            return [
                'code' => 1,
                'msg' => '操作失败',
            ];
        }
    }

    public function disabled()
    {
        $action = $this->request->param('action');
        $id = $this->request->param('id');

        $action = $action == 'close' ? 'open':'close';
        $condition = [
            'id' => $id,
            'admin_id' => $this->admin['id'],
        ];

        if ($this->admin['id'] == 1) {
            unset($condition['admin_id']);
        }

        $business = Business::get($condition);
        if ($business) {
            $res = $business->save(['state'=>$action]);
        }

        if($res){
            return [
                'code' => 0,
                'msg' => '操作成功',
            ];
        }else{
            return [
                'code' => 1,
                'msg' => '操作失败',
            ];
        }
    }
}