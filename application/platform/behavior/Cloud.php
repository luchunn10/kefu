<?php
/**
 * Created by PhpStorm.
 * User: Andy
 * Date: 2019/3/11
 * Time: 16:24
 */
namespace app\platform\behavior;

use think\exception\HttpException;

class Cloud
{
    public function run(&$params)
    {
        $info = \app\common\lib\cloud\Cloud::getHostInfo();
        if (!$info) {
            throw new HttpException(500,$info['msg']);
        }
    }
}