<?php

/**
 * User: gf
 * Date: 2015/11/2
 * Time: 10:48
 * 登陆控制器
 */

namespace Admin\Controller;

use Think\Controller;

class LoginController extends Controller
{

	//验证码
	public function verify()
	{
		$Verify = new \Think\Verify();
		@ob_end_clean();
		$Verify->fontSize = 14;
		$Verify->length = 4;
		$Verify->useNoise = false;
		$Verify->imageW = 95;
		$Verify->imageH = 26;
		//设置验证字符为纯数字
		$Verify->codeSet = "0123456789";
		$Verify->entry();
	}

	//登录视图
	public function index()
	{
		if (!IS_GET) {
			return false;
		} else {
			$this->display();
		}
	}
	
	//查询角色信息方法(角色ID组)
	protected function getRolerow()
	{
        //查询角色信息开始
		$whrow['role_type'] = 1;
		$roleInfo = M("role")->field("role_id")->where($whrow)->order("role_id DESC")->select();
		$roleidrow = '';
		foreach ($roleInfo as $key => $value) {
			$roleidrow[] = $value['role_id'];
		}
		$roleret = implode(',', $roleidrow);
		return $roleret;
        //查询角色信息结束
	}

	//登陆验证的方法
	public function logincheck()
	{
		// if (!IS_AJAX) {
		// 	return false;
		// } else {
		$LrUserName = trim(I("lrUserName"));    //用户名左右空格
		$LrUserPwd = trim(I("lrUserPwd"));      //密码去掉左右空格
		$LrUserCode = trim(I("lrCode")); //验证码去掉左右空格

		$UserName = htmlspecialchars(strip_tags($LrUserName));  //过滤获得用户名称
		$UserPwd = htmlspecialchars(strip_tags($LrUserPwd));     //过滤获得密码信息
		$UserCode = htmlspecialchars(strip_tags($LrUserCode));  //过滤获得验证码信息

		if (!empty($UserName) && !empty($UserPwd)) {
			if (check_verify($UserCode, $id = '')) {
				$rolerow = $this->getRolerow();  //获得管理员角色ID组
				$UInfo = fRec('Usertable', array("usertable_Name" => $UserName, 'usertable_Role_id' => array("in", $rolerow)), "usertable_ID,usertable_Name,usertable_Pwd,usertable_Role_id,usertable_status");
				if ($UInfo) {
					if ($UInfo['usertable_status'] == 3) {
						$re['Code'] = 300;
						$re['Msg'] = "该用户已被冻结,请联系相关管理员";
					} else if ($UInfo['usertable_status'] == 2) {
						$re['Code'] = 300;
						$re['Msg'] = "该用户已被冻结,请联系相关管理员";
					} else {
							//调用公共控制器方法返回数据库中密码
						if ($UInfo['usertable_ID']) {//判断数据库存是否有相应的记录
							if ($UInfo['usertable_Pwd'] == md5($UserPwd)) {//判断输入密码，与数据库密码是否一至
								session('uid', $UInfo['usertable_ID']);
								session('UserName', $UInfo['usertable_Name']);
								session('UserRoleID', $UInfo['usertable_Role_id']);
								session('UserLoginStatus', 1);
								if ($UInfo['usertable_ID'] == 1) {
									session("target_view", "system_index");
								} else {
									session("target_view", "plat_index");
								}
									//登录更新
								$ips = get_client_ip();
									//$ipInfos = GetIpLookup($ips); //IP位置
								if (empty($ipInfos)) {
									$locations = '局域网';
								} else {
									$locations = $ipInfos['province'] . $ipInfos['city'];
								}
								$times = time();
								$datas['usertable_logtime'] = $times;
								$datas['usertable_logarea'] = $locations;
								$wheres['usertable_ID'] = $UInfo['usertable_ID'];
								uRec('Usertable', $datas, $wheres);
									//登录更新
								aLog("账号【" . $UserName . "】帐号验证通过！", true);
								$re["Code"] = 200;
								$re["hrefUrl"] = U("Index/index");
								$re["Msg"] = "登录成功！玩命跳转中...";
							} else {
								aLog("账号【" . $UserName . "】密码验证失败！", true);
								$re["Code"] = 300;
								$re["Msg"] = "密码验证失败！";
							}
						} else {
							aLog("账号【" . $UserName . "】密码验证失败，安全表内无相关数据！", true);
							$re["Code"] = 300;
							$re["Msg"] = "密码验证失败！网络问题或者系统问题请联系相关客服";
						}
					}
				} else {
						//$re = $this->checkSalesLogin($UserName, $UserPwd);
					$re['Code'] = 300;
					$re['Msg'] = "用户名或者密码错误,请重新输入";
				}
			} else {
				$re['Code'] = 300;
				$re['Msg'] = "验证码错误,请重新输入";
			}
		} else {
			$re['Code'] = 300;
			$re['Msg'] = "用户名和密码不能为空,请重新输入";
		}
		$this->ajaxReturn($re);
		// }
	}

	/**
	 * 销售登陆验证
	 * @param type $userName
	 */
	protected function checkSalesLogin($userName, $password)
	{

		$resArray = fRec('sales', array("saletel" => $userName));
//		echo M()->_sql();
//		dump($resArray);
//		exit();
		if ($resArray) {
			if ($resArray['salestatus'] != 0) {
				$re['Code'] = 300;
				$re['Msg'] = "该用户已冻结,换个账号登陆";
			} else {
				if ($resArray['salepwd'] == md5($password)) {
					session('uid', $resArray['saleid']);
					session('UserName', $resArray['salename']);
					session('UserRoleID', $resArray['salerole']);
					session('UserLoginStatus', 2);

					//登录更新
					$ips = get_client_ip();
					$ipInfos = GetIpLookup($ips); //IP位置
					if (empty($ipInfos)) {
						$locations = '局域网';
					} else {
						$locations = $ipInfos['province'] . $ipInfos['city'];
					}
					$times = time();
					$datas['logtime'] = $times;
					$datas['logarea'] = $locations;
					$wheres['saleid'] = $resArray['saleid'];
					uRec('sales', $datas, $wheres);
					//登录更新
					aLog("账号【" . $userName . "】帐号验证通过！", true);
					$re["Code"] = 200;
					$re["hrefUrl"] = U("Index/index");
					$re["Msg"] = "登录成功！玩命跳转中...";
				}
			}
		} else {
			$re['Code'] = 300;
			$re['Msg'] = "用户名或者密码错误,请重新输入";
		}
		return $re;
	}

// `saleid` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
//  `saleaid` int(11) NOT NULL COMMENT '代理商iD',
//  `salename` varchar(20) NOT NULL COMMENT '销售姓名',
//  `saletel` varchar(13) NOT NULL COMMENT '联系电话（手机号）',
//  `salepwd` char(32) DEFAULT NULL COMMENT '登陆密码',
//  `salestatus` tinyint(1) NOT NULL COMMENT '状态 0 正常 ， 1离职 ，2 删除',
//  `saletime` varchar(15) NOT NULL COMMENT '添加时间',
	//退出系统方法
	public function logout()
	{
		session('uid', null);
		session('UserName', null);
		session('UserRoleID', null);
	}

	//跳转方法
	public function redirect()
	{
		$AccountName = session('UserName');
		if (empty($AccountName)) {
			echo 1;
		}
	}

	//间接跳转地址
	public function indexes()
	{
		$this->display();
	}

}
