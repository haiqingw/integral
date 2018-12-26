<?php 
namespace Home\Controller;
use Think\Controller;
class CommonController extends BaseController{
	public function _initialize(){
		if(preg_match("/(baidu)/i",$_SERVER['HTTP_REFERER'])){
			@header("http/1.1 404 not found");
			@header("status: 404 not found");
			echo '404 Not Found';
			exit();
		}
		$AccountName = session('mid');
		if(empty($AccountName)){
			$this->redirect("Index/index");
			exit();
		}
		$this->mid = session('mid');
	}
}
?>