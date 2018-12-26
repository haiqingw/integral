<?php

/**
 * Created by PhpStorm.
 * User: 宁
 * Date: 2015/9/1
 * Time: 15:58
 * 用户控制器
 */
namespace Admin\Controller;

use Think\Controller;

class SystemuserController extends CommonController
{
	//用户列表视图
	public function index()
	{
		$data=M('usertable')->select();
		for($i=0;$i<count($data);$i++){
			$role=M('role')->where(['role_id'=>$data[$i]['usertable_Role_id']])->find();
			$data[$i]['rolename']=$role['role_name'];
		}
		$totalCount=count($data);
		$this->assignAll([
			'data'=>$data,
			'totalCount'=>$totalCount
			]);
		$this->display();
	}

	public function add()
	{
		$roles=M('role')->select();
		$this->assignAll([
			'roles'=>$roles
		]);
		$this->display();
	}

	public function addFunction()
	{
		$params=I('post.');
		$data['usertable_Name']=$params['lrUserName'];
		$data['usertable_Pwd']=$params['lrUserPwd'];
		$data['usertable_Role_id']=$params['roleID'];
		$data['usertable_Phone']=$params['lrUserPhone'];
		$data['usertable_Email']=$params['lrUserEmail'];
		$data['usertable_createtime']=time();
		$data['usertable_createip']=get_client_ip();

		$insert=M('usertable')->data($data)->add();
		if ($insert) {
				//日志记录开始
			aLog("用户名为：" . session("UserName") . "于 " . date("Y-m-d H:i:s") . " IP为： " . get_client_ip() . "添加项目名称为 " . $params['usertable_Name'] . "保存成功", true);
				//日志记录结束
			$re["statusCode"] = 200;
			$re["message"] = '保存成功';
			$re["navTabId"] = "navTab";
			$re["forwardUrl"] = U("Systemuser/index");
			$re["callbackType"] = "forward";
			$this->ajaxReturn($re);
		} else {
				//日志记录开始
			aLog("用户名为：" . session("UserName") . "于 " . date("Y-m-d H:i:s") . " IP为： " . get_client_ip() . "添加权限名称为 " . $params['usertable_Name'] . "保存失败", false);
				//日志记录结束
			$re["statusCode"] = 300;
			$re["message"] = '保存失败';
			$this->ajaxReturn($re);
		}

	}

	public function update()
	{
		$id=I('uid');
		$roles=M('role')->select();
		$info=M('usertable')->where(['usertable_ID'=>$id])->find();
		$this->assignAll([
			'info'=>$info,
			'roles'=>$roles
		]);
		$this->display();
	}

	public function updateFunction(){
		$where=['usertable_ID'=>I('post.uid')];
		$params=I('post.');
		$data['usertable_Name']=$params['lrUserName'];
		$data['usertable_Pwd']=$params['lrUserPwd'];
		$data['usertable_Role_id']=$params['roleID'];
		$data['usertable_Phone']=$params['lrUserPhone'];
		$data['usertable_Email']=$params['lrUserEmail'];

		$insert=M('usertable')->where($where)->data($data)->save();
		if ($insert) {
				//日志记录开始
			aLog("用户名为：" . session("UserName") . "于 " . date("Y-m-d H:i:s") . " IP为： " . get_client_ip() . "添加项目名称为 " . $params['usertable_Name'] . "保存成功", true);
				//日志记录结束
			$re["statusCode"] = 200;
			$re["message"] = '保存成功';
			$re["navTabId"] = "navTab";
			$re["forwardUrl"] = U("Systemuser/index");
			$re["callbackType"] = "forward";
			$this->ajaxReturn($re);
		} else {
				//日志记录开始
			aLog("用户名为：" . session("UserName") . "于 " . date("Y-m-d H:i:s") . " IP为： " . get_client_ip() . "添加权限名称为 " . $params['usertable_Name'] . "保存失败", false);
				//日志记录结束
			$re["statusCode"] = 300;
			$re["message"] = '保存失败';
			$this->ajaxReturn($re);
		}

	}
	
	public function del(){
		$ids =I('decldel');
        if(I('decldel')==null){
            return false;
        }else{
			if(intval($ids)){
				$where = ['usertable_ID'=>$ids];
			}else{
				print_r($ids);
				$where['usertable_ID'] = ['in',(explode(",", $ids))];
			}
			$res = M('usertable')->where($where)->delete();
			if($res){
                //日志记录开始
                aLog("用户名为：" . session("UserName") . "于 " . date("Y-m-d H:i:s") . " IP为： " . get_client_ip() . "添加方案ID为 " . I('decldel') . "删除成功", false);
                //日志记录结束
                $re["statusCode"] = 200;
                $re["message"] = '删除成功';
                $re["navTabId"] = "navTab";
                $re["forwardUrl"] = U('Systemuser/index');
                $re["callbackType"] = "forward";
            }else{
                //日志记录开始
                aLog("用户名为：" . session("UserName") . "于 " . date("Y-m-d H:i:s") . " IP为： " . get_client_ip() . "添加方案ID为 " . I('decldel') . "删除失败", false);
                //日志记录结束
                $re["statusCode"] = 300;
                //$re["message"] = '删除失败';
                $re["message"] ='删除失败';
			}
			$this->ajaxReturn($re);
        }
    }
}