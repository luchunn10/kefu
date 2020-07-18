<?php
/**
 * Created by PhpStorm.
 * User: Andy
 * Date: 2019/1/28
 * Time: 18:44
 */
namespace app\platform\controller;

use app\platform\model\Option;

class Setting extends Base
{
    protected $noNeedLogin = [];

    public function index()
    {
        if ($this->request->isPost()) {
				

            Option::setList([
                [
                    'title' => 'title',
                    'value' => $this->request->post('title'),
                ],
                [
                    'title' => 'logo',
                    'value' => $this->request->post('logo'),
                ],
                [
                    'title' => 'copyright',
                    'value' => $this->request->post('copyright','',null),
                ],
                [
                    'title' => 'max_login_error',
                    'value' => intval($this->request->post('max_login_error')),
                ],
                [
                    'title' => 'passport_bg',
                    'value' => $this->request->post('passport_bg'),
                ],
                [
                    'title' => 'open_register',
                    'value' => $this->request->post('open_register'),
                ],
                [
                    'title' => 'mp_verify',
                    'value' => $this->request->post('mp_verify'),
                ],
                [
                    'title' => 'ind_sms',
                    'value' => $this->request->post('ind_sms/a'),
                ],
            ], 0, 'admin');
            return [
                'code' => 0,
                'msg' => '保存成功',
            ];
        } else {
            $option = Option::getList('title,logo,copyright,max_login_error,passport_bg,open_register,mp_verify,ind_sms', 0, 'admin');
            $this->assign('option',$option);
            return $this->fetch('index');
        }
    }

}