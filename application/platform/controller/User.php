<?php
/**
 * Created by PhpStorm.
 * User: Andy
 * Date: 2019/1/28
 * Time: 18:26
 */
namespace app\platform\controller;

use app\platform\model\Admin;
use think\exception\HttpException;
use think\Loader;

class User extends Base
{
    protected $noNeedLogin = [];

    public function index()
    {
        $keyword = $this->request->param('keyword',null);
        $query = Admin::where(['is_delete' => 0]);
        if ($keyword = trim($keyword)) {
            $query->where('username','like',"%".$keyword."%")->whereOr(function ($query) use ($keyword){
                $query->where('is_delete',0)->where('mobile','like',"%".$keyword."%");
            });
        }
        $this->assign('keyword',$keyword);
        $data = $query->paginate();
        $idlist = array_column($data->toArray()['data'],'id');
        if (empty($idlist)) {
            $counts = null;
            $page = null;
        } else {
            $counts = Admin::withCount(['business'=>function($query){
                $query->where('is_delete',0);
            }])->select($idlist);
            $page = $data->render();
        }

        $this->assign('counts',$counts);
        $this->assign('page', $page);
        $this->assign('data', $data);
        return $this->fetch('index');
    }

    public function me()
    {
        $admin = $this->admin;
        $this->assign('me',$admin);
        return $this->fetch();
    }

    public function register()
    {

    }

    public function edit()
    {

        if ($this->request->isPost()) {
            $post = $this->request->param();
            $validate = Loader::validate('Admin');
            $sence = isset($post['id']) ? 'edit': 'insert';

            if (isset($post['no_expire_time'])) {
                $post['expire_time'] = 0;
                unset($post['no_expire_time']);
            } else {
                $post['expire_time'] = strtotime($post['expire_time'] . ' 23:59:59');
            }
            if (!isset($post['permission'])) {
                $post['permission'] = json_encode([]);
            } else {
                $post['permission'] = json_encode($post['permission']);
            }
            if(!$validate->scene($sence)->check($post)){
                return ['code'=>1,'msg'=>$validate->getError()];
            }

            if (!isset($post['id']) ) {
                $account_count = (Admin::where(['is_delete' => 0])->count()) - 1;
                $account_max =-1;
                $account_over_max = $account_max != -1 && $account_count >= $account_max;
                if ($account_over_max) {
                    return [
                        'code' => 1,
                        'msg' => '保存失败，子账户创建数量上限',
                    ];
                }

                $res = $this->auth->register($post['username'], $post['password'], $post['mobile'], $post);
                $false = $this->auth->getError();
            } else {
                unset($post['password']);
                $res = Admin::where('id',$post['id'])->update($post);
                $false = '操作失败，请重试';
            }
            if ($res !== false) {
                return ['code'=>0,'msg'=>'操作成功'];
            } else {
                return ['code'=>1,'msg'=>$false];
            }

        } else {
            $admin = Admin::get([
                'id' => $this->request->param('id'),
                'is_delete' => 0,
            ]);
            $account_count = (Admin::where(['is_delete' => 0])->count()) - 1;
            $account_max = -1;
            if (!$admin) {
                $admin = null;
                $account_over_max = $account_max != -1 && $account_count >= $account_max;
            } else {
                $account_over_max = false;
            }
            if (is_array(json_decode($admin['permission'],true))) {
                $is_copyright = in_array('copyright',json_decode($admin['permission'],true));
            } else {
                $is_copyright = false;
            }

            $data = [
                'model' => $admin,
                'account_count' => $account_count,
                'account_max' => $account_max,
                'account_over_max' => $account_over_max,
                'is_copyright' => $is_copyright
            ];
            $this->assign($data);
            return $this->fetch();
        }
    }

    public function delete()
    {
        $id = $this->request->param('id');

        $admin = Admin::get([
            'id' => $id,
            'is_delete' => 0,
        ]);
        if (!$admin) {
            return [
                'code' => 1,
                'msg' => '用户不存在，请刷新页面后重试',
            ];
        }

        $admin->is_delete = 1;
        if ($admin->save()) {
            return [
                'code' => 0,
                'msg' => '删除用户成功',
            ];
        }

        return [
            'code' => 1,
            'msg' => '删除用户失败',
        ];
    }

    public function modifyPassword()
    {
        $id = $this->request->param('id');

        $admin = Admin::get([
            'id' => $id,
            'is_delete' => 0,
        ]);
        if (!$admin) {
            return [
                'code' => 1,
                'msg' => '用户不存在，请刷新页面后重试',
            ];
        }
        $post =  $this->request->post();
        $username = $post['username'];
        $paswword = $post['password'];
        $validate = Loader::validate('Admin');
        $sence = 'changeusrpwd';
        if(!$validate->scene($sence)->check($post)){
            return ['code'=>1,'msg'=>$validate->getError()];
        }
        $this->auth->setUser($admin);
        if ($admin['username'] == trim($username)) {
            if (strlen($paswword) == 0) {
                return [
                    'code' => 1,
                    'msg' => '密码不能为空',
                ];
            }

            if ($this->auth->changepwd($paswword,'',true)) {
                return [
                    'code' => 0,
                    'msg' => '修改密码成功',
                ];
            } else {
                return [
                    'code' => 1,
                    'msg' => '修改密码失败',
                ];
            }
        } else {
            if ($this->auth->changeusername($username,$paswword)) {
                return [
                    'code' => 0,
                    'msg' => '修改成功',
                ];
            } else {
                return [
                    'code' => 1,
                    'msg' => $this->auth->getError(),
                ];
            }
        }
    }
}