<?php

/**
 * Created by PhpStorm.
 * User: 宁
 * Date: 2015/9/2
 * Time: 14:07
 * 权限控制器
 */
namespace Admin\Controller;

use Think\Controller;


class AuthController extends CommonController
{
    //权限管理视图
    public function authlist()
    {
        if (!IS_AJAX) {
            return false;
        } else {
            $Model = D("Model");//实例化显示区域Model
            $params = I('post.');
            $page = !empty($params['pageNum']) ? $params['pageNum'] : 1;
            $limit = !empty($params['numPerPage']) ? $params['numPerPage'] : 25;
            $model = empty($params['model']) ? 0 : $params['model'];
            $keywords = empty($params['keywords']) ? "" : $params['keywords'];
            $where = [];
            if ($model) {
                $where['auth_area_id'] = $model;
            }
            if ($keywords) {
                $where['_string'] = "auth_name like '%" . $keywords . "%' or auth_c like '%" . $keywords . "%' or auth_a like '%" . $keywords . "%'";
            }
            $fields = "auth_id,auth_name,auth_pid,auth_c,auth_a,auth_path,auth_level,auth_area_id,auth_createtime,auth_createip,auth_sortno";  //查询字段
            //查询父级权限信息开始
            $pAuthinfo = $this->get_arrays(0, $fields, $where);
            //查询显示区域开始
            $result = M("model")->field("model_ID,model_Name")->order("model_ID DESC")->select();
            //查询父级权限信息结束
            foreach ($pAuthinfo as $key => $value) {
                $pAuthinfo[$key]["ShowAreaName"] = $Model->getsAreaName($value["auth_area_id"]);
            }
            $totalCount = count($pAuthinfo);
            $pAuthinfo = $this->page_array($limit, $page, $pAuthinfo);
            $this->assign("info", $result);
            $this->assign("model", $model);
            $this->assign("pAuthinfo", $pAuthinfo);//分配信息到模板
            $this->assign("xuhao", 1);
            $this->assignAll([
                "numPerPage" => $limit, "page" => $page, "keywords" => $keywords,
                'totalCount' => $totalCount
            ]);
            $this->display();
        }
    }
    /**
     * 数组分页函数
     * $limit  每页多少条数据
     * $page  当前第几页
     * $array  查询出来的所有数组
     * order 0 - 不变   1- 反序
     */
    public function page_array($limit, $page, $array, $order)
    {
        global $countpage; #定全局变量
        if (!empty($limit) || !empty($page) || !empty($array)) {
            $page = (empty($page)) ? '1' : $page; #判断当前页面是否为空 如果为空就表示为第一页面 
            $start = ($page - 1) * $limit; #计算每次分页的开始位置
            if ($order == 1) {
                $array = array_reverse($array);
            }
            $totals = count($array);
            $countpage = ceil($totals / $limit); #计算总页面数
            $pagedata = array();
            $pagedata = array_slice($array, $start, $limit);
            return $pagedata; #返回查询数据
        }
        return false;
    }
    public function get_arrays($vs, $field, $where)
    {
        $vses = $vs ? $vs : 0;
        $a = sRec('Auth', array_merge(array('auth_pid' => $vses), $where), "auth_sortno asc,auth_createtime desc", 1, 50000, $field);//分配信息到模板
        for ($i = 0; $i < count($a); $i++) {
            $rowarr[] = $a[$i];
            $b = sRec('Auth', array_merge(array('auth_pid' => $a[$i]['auth_id']), $where), "auth_sortno asc,auth_createtime desc", 1, 50000, $field);
            if (count($b)) {
                for ($j = 0; $j < count($b); $j++) {
                    $rowarr[] = $b[$j];
                    $c = sRec('Auth', array_merge(array('auth_pid' => $b[$j]['auth_id']), $where), "auth_sortno asc,auth_createtime desc", 1, 50000, $field);
                    if (count($c)) {
                        for ($h = 0; $h < count($c); $h++) {
                            $rowarr[] = $c[$h];
                        }
                    }
                }
            }
        }
        return $rowarr;
    }

    //保存排序
    public function sortno()
    {
        $cidarr = I('post.cidarr');
        $valarr = I('post.valarr');
        $s = 0;
        for ($i = 0; $i < count($cidarr); $i++) {
            $datas['auth_sortno'] = $valarr[$i];
            $where['auth_id'] = $cidarr[$i];
            $result = uRec('Auth', $datas, $where);
            if ($result) {
                $s++;
            }
        }
        if ($s) {
            $re["statusCode"] = 200;
            $re["message"] = "操作成功!";
            $re["navTabId"] = "navTab";
            $re["forwardUrl"] = "";
            $re["callbackType"] = "forward";
            $this->ajaxReturn($re);
        } else {
            $re["statusCode"] = 300;
            $re["message"] = "操作失败，请稍后重试!";
            $re["navTabId"] = "navTab";
            $re["forwardUrl"] = "";
            $re["callbackType"] = "forward";
            $this->ajaxReturn($re);
        }
    }

    //获取显示区域和所属父级
    public function checkmodels()
    {
        $Area = D("Model");//实例化区域显示表
        //查询显示区域开始
        $info = $Area->field("model_ID,model_Name,model_SystemAgentsID")->order("model_ID DESC")->select();
        //查询父级信息开始
        $rowarr = array();
        $field = "auth_id,auth_name,auth_level";
        $a = sRec('Auth', array('auth_pid' => 0), "auth_sortno asc,auth_createtime desc", 1, 50000, $field);//分配信息到模板
        for ($i = 0; $i < count($a); $i++) {
            $rowarr[] = $a[$i];
            $b = sRec('Auth', array('auth_pid' => $a[$i]['auth_id']), "auth_sortno asc,auth_createtime desc", 1, 50000, $field);
            if (count($b)) {
                for ($j = 0; $j < count($b); $j++) {
                    $rowarr[] = $b[$j];
                }
            }
        }
        //查询父级信息结束
        $result['infos'] = $info;
        $result['pauthinfo'] = $rowarr;
        return $result;
    }


    //权限添加视图
    public function authadd()
    {
        if (!IS_AJAX) {
            return false;
        } else {
            $Systemmodule = D("Systemmodule");//实例化系统模块Model
            $result = $this->checkmodels();
            $infos = $result["infos"];//显示区域信息
            foreach ($infos as $key => $vaule) {
                $SystemModuleName = $Systemmodule->getsIdSelectName($vaule["model_SystemAgentsID"]);
                $infos[$key]["SystemModuleName"] = empty($SystemModuleName) ? "未知" : $SystemModuleName;
            }
            $pathInfo = $result['pauthinfo'];
            $this->assign('info', $infos);
            $this->assign('pauthinfo', $pathInfo);
            $this->display();
        }
    }

    //权限添加方法
    public function authaddfunction()
    {
        if (!IS_AJAX) {
            return false;
        } else {
            $authss = D("Auth");
            $d["auth_area_id"] = I("areaShowID");//显示区域
            $d["auth_name"] = trim(I("auth_Name"));//权限名称
            $d["auth_pid"] = I("auth_pid");//父级ID
            $d["auth_c"] = trim(I("auth_controllerName"));//控制器名称
            $d["auth_a"] = trim(I("auth_functionName"));//操作方法名称
            $d["auth_createip"] = get_client_ip();//获取客户端IP
            $d["auth_createtime"] = date("Y-m-d H:i:s");//获取当前时间
            if (D("Auth")->where(array("auth_name" => trim(I("auth_Name"))))->count() > 0) {
                //日志记录开始
                aLog("用户名为：" . session("UserName") . "于 " . date("Y-m-d H:i:s") . " IP为： " . get_client_ip() . "添加权限名称为 " . $d["auth_name"] . "保存失败,原因是权限名称已经存在", false);
                //日志记录结束
                $re["statusCode"] = 300;
                $re["message"] = "保存失败,<br />原因：权限名称已经存在";
                $this->ajaxReturn($re);
            } else {
                if (D("Auth")->addAuth($d)) {
                    //日志记录开始
                    aLog("用户名为：" . session("UserName") . "于 " . date("Y-m-d H:i:s") . " IP为： " . get_client_ip() . "添加权限名称为 " . $d["auth_name"] . "保存成功", true);
                    //日志记录结束
                    $re["statusCode"] = 200;
                    $re["message"] = "保存成功";
                    $re["navTabId"] = "navTab";
                    $re["forwardUrl"] = U('Auth/authlist');
                    $re["callbackType"] = "forward";
                    $this->ajaxReturn($re);
                } else {
                    //日志记录开始
                    aLog("用户名为：" . session("UserName") . "于 " . date("Y-m-d H:i:s") . " IP为： " . get_client_ip() . "添加权限名称为 " . $d["auth_name"] . "保存失败", false);
                    //日志记录结束
                    $re["statusCode"] = 300;
                    $re["message"] = "保存失败";
                    $this->ajaxReturn($re);
                }
            }

        }
    }

    //批量删除操作
    public function authdel($decldel)
    {
        if (!IS_AJAX) {
            return false;
        } else {
            $decldelarr = explode(",", $decldel);
            $Auth = D("Auth");//实例化权限表

            $is_del = $Auth->delete($decldel);
            $j = $is_del;

            if ($j) {
                //记录日志
                aLog("用户名为：" . session("UserName") . "于 " . date("Y-m-d H:i:s") . " IP为：" . get_client_ip() . "删除了 " . $j . "条记录，操作成功！", true);
                //返回操作
                $re["statusCode"] = 200;
                $re["message"] = "操作成功";
                $re["navTabId"] = "navTab";
                $re["callbackType"] = "forward";
                $this->ajaxReturn($re);
                //返回操作
            } else {
                //记录日志
                aLog("用户名为：" . session("UserName") . "于 " . date("Y-m-d H:i:s") . " IP为：" . get_client_ip() . "删除记录，操作失败！", false);
                //返回操作
                $re["statusCode"] = 300;
                $re["message"] = "操作失败";
                $this->ajaxReturn($re);
                //返回操作
            }
        }
    }


    //权限更改视图
    public function authmodify()
    {
        if (!IS_AJAX) {
            return false;
        } else {
            $Systemmodule = D("Systemmodule");//实例化系统模块Model
            $Auth = D("Auth");//实例化权限表
            $authInfo = $Auth->field("auth_id,auth_name,auth_area_id,auth_pid,auth_c,auth_a")->where(array("auth_id" => I("authid", "", "intval")))->find();
            $this->assign("authInfo", $authInfo);
            //查询更改信息结束
            $result = $this->checkmodels();
            $infos = $result['infos'];
            $pauthinfo = $result["pauthinfo"];
            foreach ($infos as $key => $vaule) {
                $SystemModuleName = $Systemmodule->getsIdSelectName($vaule["model_SystemAgentsID"]);
                $infos[$key]["SystemModuleName"] = empty($SystemModuleName) ? "未知" : $SystemModuleName;
            }
            $this->assign('info', $infos);
            $this->assign('pauthinfo', $pauthinfo);
            $this->display();
        }
    }
    //权限更改方法
    public function authmodifyfunction()
    {
        if (!IS_AJAX) {
            return false;
        } else {
            $Auth = D("Auth");//实例化权限表
            $authID = I("authID", "", "intval");//权限ID
            $areaShowID = I("areaShowID", "", "");//显示区域
            $auth_Name = trim(I("auth_Name"));//权限名称
            $auth_pid = I("auth_pid");//父级ID
            $auth_controllerName = trim(I("auth_controllerName"));//控制器名称
            $auth_functionName = trim(I("auth_functionName"));//方法
            //查询父级的全路径
            $pauth_path = $Auth->field("auth_path,auth_level")->where(array("auth_id" => $auth_pid))->find();
            $authPath = empty($pauth_path["auth_path"]) ? "" : $pauth_path["auth_path"] . '-';
            if ($pauth_path["auth_level"] == 0 && $pauth_path["auth_path"] != "") {
                $authLevel = $pauth_path["auth_level"] + 1;
            } elseif ($pauth_path["auth_path"] == "") {
                $authLevel = 0;
            } else {
                $authLevel = $pauth_path["auth_level"] + 1;
            }
            //更改信息开始
            $dt = array(
                "auth_id" => $authID,//权限ID
                "auth_area_id" => $areaShowID,//显示区域ID
                "auth_name" => $auth_Name,//权限名称
                "auth_pid" => $auth_pid,//父级ID
                "auth_c" => $auth_controllerName,//控制器名称
                "auth_a" => $auth_functionName,//操作方法
                //全路径
                "auth_path" => $authPath . $authID,
                "auth_level" => $authLevel
            );
            if ($Auth->save($dt)) {
                //日志记录开始
                aLog("用户名为：" . session("UserName") . "于 " . date("Y-m-d H:i:s") . " IP为： " . get_client_ip() . "更改权限名称成功", true);
                //日志记录结束
                $re["statusCode"] = 200;
                $re["message"] = "更改成功";
                $re["navTabId"] = "navTab";
                $re["forwardUrl"] = U('Auth/authlist');
                $re["callbackType"] = "forward";
                $this->ajaxReturn($re);
            } else {
                //日志记录开始
                aLog("用户名为：" . session("UserName") . "于 " . date("Y-m-d H:i:s") . " IP为： " . get_client_ip() . "更改权限名称失败", false);
                //日志记录结束
                $re["statusCode"] = 300;
                $re["message"] = "更改失败";
                $this->ajaxReturn($re);
            }
            //更改信息结束

        }
    }

}