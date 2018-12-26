<?php
namespace Admin\Controller;

use Think\Controller;

class IndexController extends CommonController
{
	//视图模型
	public function index()
	{
		if (!IS_GET) {
			return false;
		} else {
			//根据SESSION uid 获取用户信息
			if (session("UserLoginStatus") == 2) {
				$map['saleid'] = session("uid");
				$auth_info = fRec('sales', $map);
				$maps['role_id'] = $auth_info['salerole'];
			}

			if (session("UserLoginStatus") == 1) {
				$map['usertable_ID'] = session('uid');
				$auth_info = fRec('Usertable', $map, 'usertable_ID,usertable_Name,usertable_Role_id,usertable_status');
				//获取权限
				$maps['role_id'] = $auth_info['usertable_Role_id'];
			}

			$auth_in = fRec('role', $maps, 'role_id,role_name,role_auth_ids');
			//查询的字段
			$field = "auth_id,auth_name,auth_pid,auth_c,auth_a,auth_area_id";
			if ($this->UserLoginStatus != 2) {
				$map['usertable_ID'] = session('uid');
				$table = UT_T;
				$fields = 'usertable_ID,usertable_Name,usertable_logtime,usertable_logarea,usertable_Numb Numb,companyName';
			}
			$infoAcc = fRec($table, $map, $fields);
			//查询用户权限功能数组
			$topList = $this->get_arrays('admin', 0, $field, explode(',', $auth_in['role_auth_ids']));
			// echo "<pre>";
			// print_r($topList);
			// echo "</pre>";
			$this->assign("logoinfo", $logo);
			$this->assign('infoAcc', $infoAcc);
			$this->assign("LoginStatus", $this->UserLoginStatus);
			$this->assign('tlist', $topList);
			$this->assign('realyear', date('Y', time()));
			$this->display();
		}
	}
	//权限管理
	public function get_arrays($type, $vs, $field, $array_in)
	{
		$models = array();
		$vses = $vs ? $vs : 0;
		$a = sRec('Auth', array(
			'auth_pid' => $vses
		), "auth_sortno asc,auth_createtime desc", 1, 50000, $field);
		for ($i = 0; $i < count($a); $i++) {
			if (in_array($a[$i]['auth_id'], $array_in)) {
				$abc = array();
				$models[] = $a[$i]['auth_area_id'];
				$b = sRec('Auth', array(
					'auth_pid' => $a[$i]['auth_id']
				), "auth_sortno asc,auth_createtime desc", 1, 50000, $field);
				if (count($b)) {
					for ($j = 0; $j < count($b); $j++) {
						if (in_array($b[$j]['auth_id'], $array_in)) {
							$abc[] = $b[$j];
						}
					}
				}
				$a[$i]['leveltr'] = $abc;
				$rowarr[] = $a[$i];
			}
		}
		//整理组合数组
		$v = array_unique($models); //去重模块类型
		$map['model_ID'] = array('in', $v);
		$z = sRec('Model', $map, "model_Sortno asc,model_Createtime desc", 1, 50000, "model_ID,model_Name");
		for ($i = 0; $i < count($v); $i++) {
			$s[$i]['modelinfo'] = $z[$i];
			for ($j = 0; $j < count($rowarr); $j++) {
				if ($rowarr[$j]['auth_area_id'] == $z[$i]['model_ID']) {
					$s[$i]['modelarray'][] = $rowarr[$j];
				}
			}
		}
		return $s;
	}
	//管理帐号密码修改
	public function uppassword()
	{
		if (!IS_AJAX) {
			return false;
		} else {
			$this->display();
		}
	}
	//管理帐号密码修改（执行方法）
	public function uppasswordfun()
	{
		if (!IS_AJAX) {
			return false;
		} else {
			$UserUid = session('uid'); //商户操作ID
			$UserName = session('UserName'); //商户操作名称
			$oldpwd = md5(I('post.ManagerOldPwd')); //原密码
			$newpwd = md5(I('post.ManagerNewPwd')); //新密码
			//查询管理平台用户表
			$Managerinfo = fRec('usertable', array(
				'usertable_ID' => $UserUid
			), 'usertable_Name,usertable_Pwd');
			//检查密码
			if ($Managerinfo['usertable_Pwd'] === $oldpwd) {
				//更新密码
				$uptp = uRec('usertable', array(
					'usertable_Pwd' => $newpwd
				), array(
					'usertable_ID' => $UserUid
				));
				//更新密码
				if ($uptp) {
					session('uid', null);
					session('UserName', null);
					session('UserRoleID', null);
					aLog("帐号【" . $UserName . "】修改密码，操作成功！", false);
					$re["statusCode"] = 200;
					$re["message"] = "操作成功,请重新登录！";
					$re["navTabId"] = "navTab";
					$re["forwardUrl"] = "";
					$re["callbackType"] = "closeCurrent";
					$this->ajaxReturn($re);
				} else {
					aLog("帐号【" . $UserName . "】修改密码，操作失败！", false);
					$re["statusCode"] = 300;
					$re["message"] = "操作失败";
					$this->ajaxReturn($re);
				}
			} else {
				aLog("帐号【" . $UserName . "】修改密码，操作失败！原因：原密码错误。", false);
				$re["statusCode"] = 300;
				$re["message"] = "原密码错误,操作失败";
				$this->ajaxReturn($re);
			}
		}
	}
	public function behaviorAdmin()
	{
		if (IS_AJAX) {
			$params = I("post.");
			$bid = session("uid");
			$name = session("UserName");
			$data = [
				"controller" => $params['controller'],
				"action" => $params['action'],
				"clicktime" => time(),
				"bid" => $bid, "name" => $name
			];
			aRec("behavior_admin", $data);
		} else {
			return false;
		}
	}
}
