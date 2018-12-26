<?php
namespace Admin\Controller;
use Think\Controller;
class EncodeController extends Controller{
	public function index(){
		$this->display();
	}
	public function encode(){
		if(!IS_AJAX){
			return false;
		}else{
			$params = I('post.');
			switch($params['i']){
				case 1:
					$content = RSAcode($params['content']);
					break;
				case 2:
					$content = RSAcode($params['content'],"DE");
					break;
				case 3:
					$content = base64_encode($params['content']);
					break;
				case 4:
					$content = base64_decode($params['content']);
					break;
				case 5:
					$content = md5($params['content']);
					break;
				case 6:
					$content = json_encode($params['content']);
					break;
				case 7:
					$content = json_decode($params['content']);
					break;
			}
			if(json_encode($content) == 'null'){
				$content = '解密失败';
			}
			echo json_encode(['msg'=>$content]);
		}
	}
}