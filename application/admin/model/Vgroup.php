<?php
/**
 * Created by PhpStorm.
 * User: Andy
 * Date: 2019/4/10
 * Time: 11:21
 */
namespace app\admin\model;

use think\Model;

class Vgroup extends Model
{
    protected $table = 'wolive_vgroup';
    protected $autoWriteTimestamp = false;

    public function setCreateTimeAttr()
    {

    }

    public function getCreateTimeAttr()
    {

    }
}