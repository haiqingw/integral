<?php
/**
 * Created by PhpStorm.
 * User: 宁
 * Date: 2015/9/1
 * Time: 14:49
 * 角色控制器
 */
namespace Admin\Controller;

use Think\Controller;

class RoleController extends CommonController
{
    //角色列表
    public function rolelist()
    {
        if (!IS_AJAX) {
            return false;
        } else {
            //总条数
            $totalCount=cRec('Role');
            $this->assign('totalCount',$totalCount);
            $page = I('pageNum');//第几页
            if (empty($page)) {
                $page = 1;
            }
            $limit = I('numPerPage');
            if (empty($limit)) {
                $limit = 20;//每页多少条
            }
            $this->assign("numPerPage", $limit);
            $this->assign("page", $page);
            $offset = ($page - 1) * $limit;

            $fields = '*';  //查询字段
            $orderby = 'role_createtime desc';  //排序条件
            $info = sRec('Role','',$orderby,$page,$limit,$fields); //查询
            $this->assign("info", $info);
            $this->assign("xuhao", 1);
            $this->display();
        }
    }

    //角色添加视图
    public function roleadd()
    {
        if (!IS_AJAX) {
            return false;
        } else {
            $this->display();
        }
    }

    //角色添加方法
    public function roleaddfunction()
    {
        if (!IS_AJAX) {
            return false;
        } else {
            $Role = M("Role");//实例化角色表
            //过滤中文逗号全角逗号
            $Astr = strreplace(trim(I("roleName")));
            $roletp = I('addRoletp');
            $agenttp = I('addAgenttp');
            $Astr = explode(",", $Astr);
            $RoleName = FilterUniqueValues($Astr);//使用逗号将多个角色名称分隔成数组
            $LogInfo = "";
            for ($i = 0; $i < count($RoleName); $i++) {
                //过滤重复角色
                if ($Role->where(array("role_name" => $RoleName[$i]))->count() <= 0) {
                    $d[$i]["role_createip"] = get_client_ip();//获取客户端的IP地址
                    $d[$i]["role_createtime"] = date("Y-m-d H:i:s");//获取添加的时间
                    $d[$i]["role_name"] = $RoleName[$i];//角色名称
                    if ($roletp==1) {
                        $d[$i]["role_type"] = $roletp;
                    } else {
                        $d[$i]["role_type"] = $roletp;
                        $d[$i]["role_agtp"] = $agenttp;
                    }
                }
                $LogInfo .= $RoleName.',';
            }

            if ($Role->addAll($d)) {
                //日志记录开始
                aLog("用户名为：".session("UserName")."于 ".date("Y-m-d H:i:s")." IP为： ".get_client_ip()."添加角色的名称为 ".rtrim($LogInfo,",")."添加成功",false);
                //日志记录结束
                $re["statusCode"] = 200;
                $re["message"] = "保存成功";
                $re["navTabId"] = "Role/rolelist";
                $re["forwardUrl"] = '';
                $re["callbackType"] = "forward";
                $this->ajaxReturn($re);
            } else {
                //日志记录开始
                aLog("用户名为：".session("UserName")."于 ".date("Y-m-d H:i:s")." IP为： ".get_client_ip()."添加角色的名称为 ".rtrim($LogInfo,",")."添加失败",false);
                //日志记录结束
                $re["statusCode"] = 300;
                $re["message"] = "保存失败,<br />请正确输入信息!";
                $this->ajaxReturn($re);
            }
        }
    }

    //更改角色视图
    public function rolemodify()
    {
        if (!IS_AJAX) {
            return false;
        } else {
            $roleid = I("roleid", "", "intval");//角色ID
            $Role = M("Role");//实例化角色表
            $info = $Role->field("role_id,role_name,role_type,role_agtp")->where(array("role_id" => $roleid))->find();
            $this->assign("info", $info);
            $this->display();
        }
    }

    //更改角色方法
    public function rolemodifyfunction()
    {
        if (!IS_AJAX) {
            return false;
        } else {
            $roid = I("role_id", "", "intval");
            $rolename = trim(I("roleName"));
            $roletp = I('modRoletp');
            $agenttp = I('modAgenttp');
            $Role = M("Role");//实例化角色表
            $where['role_id'] = I("role_id", "", "intval");
            //数据
            $dat['role_name'] = $rolename;
            if ($roletp==1) {
                $dat['role_type'] = $roletp;
                $dat['role_agtp'] = 0;
            } else {
                $dat['role_type'] = $roletp;
                $dat['role_agtp'] = $agenttp;
            }
            //数据
            $result = $Role->data($dat)->where($where)->save();
            if ($result) {
                $re["statusCode"] = 200;
                $re["message"] = "保存成功";
                $re["navTabId"] = "Role/rolelist";
                $re["forwardUrl"] = '';
                $re["callbackType"] = "closeCurrent";
                $this->ajaxReturn($re);
            } else {
                $re["statusCode"] = 300;
                $re["message"] = "保存失败";
                $this->ajaxReturn($re);
            }
        }
    }

    //角色删除方法
    public function roledel($roleid)
    {
        if (!IS_AJAX) {
            return false;
        } else {
            $Usertable = M("Usertable");//实例化用户表
            $Role = M("Role");//实例化角色表
            $zCons = $Usertable->where(array("usertable_Role_id" => intval($roleid)))->count();
            if ($zCons > 0) {
                $re["statusCode"] = 300;
                $re["message"] = "删除失败,<br />原因:该角色已被使用";
                $this->ajaxReturn($re);
            } else {
                if ($Role->where(array("role_id" => intval($roleid)))->delete()) {
                    $re["statusCode"] = 200;
                    $re["message"] = "删除成功";
                    $re["navTabId"] = "Role/rolelist";
                    $re["forwardUrl"] = '';
                    $re["callbackType"] = "forward";
                    $this->ajaxReturn($re);
                } else {
                    $re["statusCode"] = 300;
                    $re["message"] = "删除失败";
                    $this->ajaxReturn($re);
                }
            }
        }
    }

    //分配权限视图
    public function distribution()
    {
        if (!IS_AJAX) {
            return false;
        } else {
            $Role = M("Role");//实例化角色表
            $roleid = I("roleid", "", "intval");//角色ID
            $info = $Role->field("role_id,role_name")->where(array("role_id" => $roleid))->find();
            $this->assign("info", $info);
            //查询全部权限信息
            $Auth = M("Auth");//实例化权限表
            $pauth_info = $Auth->field("auth_id,auth_name,auth_pid,auth_c,auth_a,auth_path")->where(array("auth_level" => 0))->order('auth_sortno asc,auth_createtime desc')->select();//权限顶级
            $sauth_info = $Auth->field("auth_id,auth_name,auth_pid,auth_c,auth_a,auth_path")->where(array("auth_level" => 1))->order('auth_sortno asc,auth_createtime desc')->select();//权限次顶级
            $tauth_info = $Auth->field("auth_id,auth_name,auth_pid,auth_c,auth_a,auth_path")->where(array("auth_level" => 2))->order('auth_sortno asc,auth_createtime desc')->select();//权限次次顶级
            $this->assign("pauthinfo", $pauth_info);
            $this->assign("sauthinfo", $sauth_info);
            $this->assign("tauth_info", $tauth_info);
            //查询当前用户的权限信息
            $UserInfo = $Role->field("role_auth_ids")->where(array("role_id" => $roleid))->find();
            $auth_ids_arr = explode(",", $UserInfo["role_auth_ids"]);//数组auth_ids信息
            $this->assign("auth_ids_arr", $auth_ids_arr);
            $this->display();
        }
    }
    //权限分配方法
    public function distributionfunction(){
        if(!IS_AJAX){
            return false;
        }else{
            if(D("Role")->saveAuth(I("lr_authName"),I("role_id"))){
                $re["statusCode"] = 200;
                $re["message"] = "保存成功";
                $re["navTabId"] = "navTab";
                $re["forwardUrl"] = U('Role/rolelist');
                $re["callbackType"] = "closeCurrent";
                $this->ajaxReturn($re);
            } else {
                $re["statusCode"] = 300;
                $re["message"] = "保存失败";
                $this->ajaxReturn($re);
            }
        }
    }
}