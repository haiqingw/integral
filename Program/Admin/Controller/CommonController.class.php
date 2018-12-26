<?php

/**
 * 公用控制器
 */
namespace Admin\Controller;

use Think\Controller;

class CommonController extends Controller
{
	protected $uid;
	protected $UserLoginStatus;
	protected $errorMsg = "";
	protected $responseStatus = 1;
	//自动验证的方法
	public function _initialize()
	{
		//file_put_contents("SERVER",var_export($_SERVER,true));
		if (preg_match("/(baidu)/i", $_SERVER['HTTP_REFERER'])) {
			@header("http/1.1 404 not found");
			@header("status: 404 not found");
			echo '404 Not Found';
			exit();
		}
		$AccountName = session('uid');
		if (empty($AccountName)) {
			if (I('UPT') != 1) {
				// $this->redirect("Login/index");
				echo "<script>window.location.href='" . U('Login/index') . "'</script>";
				exit();
			}
		}
		$this->uid = session('uid');
		$this->UserLoginStatus = session("UserLoginStatus");
		//验证控制器及方法使用权限
		//获取当前的控制器和方法名称
		$New_ca = CONTROLLER_NAME . '-' . ACTION_NAME;
		//用户的角色ID
		if (session("UserLoginStatus") == 1) {
			$UserRole_ID = fRec('Usertable', array(
				"usertable_ID" => $AccountName
			), 'usertable_Role_id');
			$where['role_id'] = $UserRole_ID["usertable_Role_id"];
		}
		if (session("UserLoginStatus") == 2) {
			$salesRole_ID = gFec(S_T, array(
				"saleid" => $AccountName
			), "salerole");
			$where['role_id'] = $salesRole_ID;
		}
		//查询角色ID 所对应的权限信息
		$RoleInfo = fRec('Role', $where, 'role_auth_ac');
		$authac = explode(',', $RoleInfo["role_auth_ac"]);
		$authacarr = array_push($authac, 'Index-index', 'Main/index');
		//权限追加
		//所有权限
		$RoleInfoes = sRec('Auth', '', "auth_sortno asc,auth_createtime desc", 1, 50000, 'auth_c,auth_a');
		foreach ($RoleInfoes as $value) {
			if ($value['auth_c']) {
				$bs = $value['auth_c'] . '-' . $value['auth_a'];
				$aminarr[] = $bs;
			}
		}
		//判断权限是否可以访问
		if (in_array($New_ca, $aminarr) && !in_array($New_ca, $authac)) {
			$re["statusCode"] = 300;
			$re["message"] = "权限不足,无法访问!";
			$this->ajaxReturn($re);
		}
	}
	//二维数组去掉重复值 并保留键值
	public function array_unique_fb($array2D)
	{
		foreach ($array2D as $k => $v) {
			$v = join(",", $v); //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
			$temp[$k] = $v;
		}
		$temp = array_unique($temp); //去掉重复的字符串,也就是重复的一维数组
		foreach ($temp as $k => $v) {
			$array = explode(",", $v); //再将拆开的数组重新组装
			$temp2[$k]["plat"] = $array[0];
		}
		foreach ($temp2 as $key => $v) {
			$temp2[$key]['co'] = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
			$re["statusCode"] = 300;
			$re["message"] = "权限不足,无法访问!";
			$this->ajaxReturn($re);
		}
		return $temp2;
	}
	/**
	 * 最高权限登录验证
	 * @date: 2017年11月16日 上午10:25:01
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 */
	protected static function checkSystemLoginUser()
	{
		if (session('uid') == 1) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * 发送手机验证码
	 * @date: 2017年6月5日 下午3:34:23
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param:
	 * @return: string
	 */
	protected static function phoneVerifyCode($mobile, $verify, $sendType, $pid = 3)
	{
		if (!empty($mobile) && !empty($verify) && !empty($sendType)) {
			$object = new \Common\Api\Message();
			$send = $object->SendVerifyCode($mobile, $verify, $sendType, $pid);
			if ($send) {
				return true;
			}
		}
		return false;
	}
	/**
	 * 获取数据表字段
	 * @date: 2018年6月25日 上午11:07:21
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: $table 表名带表前缀
	 * @return: array
	 */
	public function get_db_table_colums($table)
	{
		if (!empty($table)) {
			$sql = "select COLUMN_NAME name from information_schema.COLUMNS where table_name = '" . $table . "'  and table_schema ='" . TTP_DB_NAME . "'";
			$query = M()->query($sql);
			if ($query) {
				return $query;
			}
		}
		return false;
	}
}
