<?php 
namespace app\manager\controller;

use app\admin\model\Admins;
use app\platform\model\Business;


Class Set extends Base
{
   public function change(){
   	   $id = $this->request->param('id');
   	   $action = $this->request->param('action');
       $action = $action == '1' ? '0':'1';
       $condition = [
           'id' => $id,
       ];
       $business = Business::get($condition);
       if ($business) {
           $res = $business->save(['is_recycle'=>$action]);
       }

   	  if($res){
   	   	  return true;
   	   }else{
   	   	 return false;
   	   }
   }

   public function delete(){
       $id = $this->request->param('id');
       $condition = [
           'id' => $id,
           'is_delete' => 0,
       ];
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
 
	
}
