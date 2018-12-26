<?php

/*****************************/
define("ACTIVITY_PIC_PATH", "/Public/LrUploads/activity/");
define("MANAGE_PIC_URL", "/Public/LrUploads/managelogo/"); //管理后台
define("PROMOTE_PIC_URL", "/Public/LrUploads/promotelogo/"); //推广
define("REGIS_IMS_PIC_URL", "/Public/LrUploads/regis/");
define("INTRO_IMS_PIC_PATH", "/Public/LrUploads/introduce/");
define("EXCLV_IMS_PIC_PATH", "/Public/LrUploads/exclv/");
define("MSG_PIC_PATH", "/Public/LrUploads/message/");
define("USER_HELP_CLASS＿PIC_PATH", "/Public/LrUploads/UserHelpClass/"); //用户帮助分类图片存储目
/*****************************/
define("TTP_DB_NAME", "integral");
define("PREFIX", "p_");
define("UT_T", "usertable"); //用户表
/*---------------内容管理-----------------------*/
/*---------------内容管理----------------------*/
/*<内容管理管理列表>*/
/*</内容管理管理列表>*/
/*<操作日志管理列表>*/
define("BRC_T", "record_change"); //商户余额变更操作记录
/*</操作日志管理列表>*/
/*<商户提现关联表>*/
define("DRAW_TAX", 0.01);
define("DRAW_FEE", 1);
/*</商户提现关联表>*/
/*-------------------------------------*/
/*<商户管理关联表>*/
define("P_CRE_AU", "creit_auth"); //平台商户积分兑换权限管理表
define("P_INTR", "integral"); //商户积分管理表
define("P_IN_COR", "integral_cord"); //增加积分记录表
define("P_ACTIV", "activate_temporary"); //激活记录临时表
define("P_INT_SET", "integral_set"); //商户返积分设置管理表
define("U_T", "user"); //商户表
define("P_ORDER", "order"); //订单管理
define("P_BLMCS", "bus_level_manage"); //商户等级分类管理表
define("P_BCMCS", "bus_cashback_manage_class");//商户返现类型管理表
/*</商户管理关联表>*/
define("BALANCE_KEY", "accadseomcnQWEWTSDDSFHJNCB1233254345646asaadfdfdfdf"); //平台充值记录表
define("DEFAULT_BATCH_LIMIT", 50);
/**
 * TODO 基础分页的相同代码封装，使前台的代码更少
 * @param $count 要分页的总记录数
 * @param int $pagesize 每页查询条数
 * @return \Think\Page
 */
function getpage($count, $pagesize = 10)
{
	// $p = new Think\Page();
	$p = new \Think\Page($count, $pagesize);
	$p->setConfig('header', '<li class="rows">共<b>%TOTAL_ROW%</b>条记录&nbsp;第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
	$p->setConfig('prev', '上一页');
	$p->setConfig('next', '下一页');
	$p->setConfig('last', '末页');
	$p->setConfig('first', '首页');
	$p->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
	$p->lastSuffix = false;//最后一页不显示为总页数
	return $p;
}

/**
 * 角色操作日志
 * @date: 2017年5月19日 上午9:54:50
 * @author: HaiQing.Wu <398994668@qq.com>
 * @param: $GLOBALS
 * @return:
 */
function SystemLog($user, $uid, $controller, $action, $msg, $bid)
{
	if (is_array($bid)) {
		$bid = implode(",", $bid);
	}
	$rolename = getManageRoleName($uid);
	$data = array(
		"userName" => $user,
		"action" => $action, "operatorID" => $uid,
		"className" => $controller,
		"roleName" => $rolename, "bid" => $bid,
		"optionTime" => date("Y-m-d H:i:s D"),
		"result" => json_encode(array(
			"userName" => $user,
			"role" => $rolename,
			"action" => $action,
			"IP" => get_client_ip(), "msg" => $msg
		))
	);
	aRec(SYSL_T, $data);
}
/**
 * 获取系统用户角色
 * @date: 2017年5月8日 下午3:24:44
 * @author: HaiQing.Wu <398994668@qq.com>
 * @param: variable
 * @return:
 */
function getManageRoleName($uid)
{
	$roleID = getFieldValue(UT_T, "usertable_Role_id", "usertable_ID", $uid);
	if ($roleID) {
		$res = getFieldValue(ROLE_T, "role_name", "role_id", $roleID);
		if ($res) {
			return $res;
		}
		return false;
	}
	return false;
}
/**
 * 获取字段值
 * @date: 2017年5月8日 下午3:00:38
 * @author: HaiQing.Wu <398994668@qq.com>
 * @param: variable
 * @return:
 */
function getFieldValue($table, $field, $key, $value)
{
	$where = array($key => $value);
	$res = M($table)->where($where)->getField($field);
	if ($res) {
		return $res;
	}
	return false;
}

