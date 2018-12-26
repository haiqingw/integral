<?php
/**
 * Created by PhpStorm.
 * User: 宁
 * Date: 2015/9/2
 * Time: 15:29
 * 区域显示控制器
 */
namespace Admin\Controller;

use Think\Controller;

class ModelController extends CommonController
{
    //显示区域列表视图
    public function arealist()
    {
        if (!IS_AJAX) {
            return false;
        }else{
            //总条数
			$Systemmodule = D("Systemmodule");//实例化系统模块Model
            $totalCount=cRec('Model');
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
            $orderby = 'model_Sortno asc,model_Createtime desc';  //排序条件
            $info = sRec('Model','',$orderby,$page,$limit,$fields); //查询
			foreach ($info as $key => $value) {
                $info[$key]["model_SystemName"] =$Systemmodule->getsIdSelectName($value["model_SystemAgentsID"]);
            }
            $this->assign("info", $info);
            $this->assign("xuhao", 1);
            $this->display();
        }
    }

    //保存排序
    public function sortno()
    {
        $cidarr = I('post.cidarr');
        $valarr = I('post.valarr');
        $s = 0;
        for ($i=0; $i <count($cidarr) ; $i++) {
            $datas['model_Sortno'] = $valarr[$i];
            $where['model_ID'] = $cidarr[$i];
            $result = uRec('Model',$datas,$where);
            if ($result) {
                $s++;
            }
        }

        if($s){
            $re["statusCode"] = 200;
            $re["message"] = "操作成功!";
            $re["navTabId"] = "navTab";
            $re["forwardUrl"] = "";
            $re["callbackType"] = "forward";
            $this->ajaxReturn($re);
        }else{
            $re["statusCode"] = 300;
            $re["message"] = "操作失败，请稍后重试!";
            $re["navTabId"] = "navTab";
            $re["forwardUrl"] = "";
            $re["callbackType"] = "forward";
            $this->ajaxReturn($re);
        }
    }

    //显示区域添加视图
    public function areaadd()
    {
        if (!IS_AJAX) {
            return false;
        } else {
			//系统模块获取开始
            $Systemmodule = D("Systemmodule");//实例化系统模块Model
            $this->assign("moduleInfo", $Systemmodule->getsAuthInfo());
            //系统模块获取结束
            $this->display();
        }
    }

    //显示区域添加方法
    public function areaaddfunction()
    {
        if (!IS_AJAX) {
            return false;
        } else {
            $Area = D("Model");//实例化区域显示表
            //过滤模块名
            $lrmodel = htmlspecialchars(strip_tags(trim(I("areaName"))));
            $Astr = strreplace($lrmodel);
            $Astr = explode(",", $Astr);
            $areaName = FilterUniqueValues($Astr);//使用逗号将多个角色名称分隔成数组
			 $moduleName = trim(I("moduleName"));//系统模块名称
            //过滤模块名
            $LogString = "";
            for ($i = 0; $i < count($areaName); $i++) {
                if ($Area->where(array("model_Name" => $areaName[$i]))->count() <= 0) {
                    $d[$i]["model_Createip"] = get_client_ip();//获取客户端ip
                    $d[$i]["model_Createtime"] = date("Y-m-d H:i:s");//获取当前时间
                    $d[$i]["model_Name"] = trim($areaName[$i]);//显示区域名称
					$d[$i]["model_SystemAgentsID"] = $moduleName;//系统模块名称
                    $d[$i]["model_Remark"] = trim(I("textarea1"));//备注信息
                }
                $LogString .= $areaName[$i].',';
            }
            if ($Area->addAll($d)){
                //日志记录开始
                aLog("用户名为：".session("UserName")."于 ".date("Y-m-d H:i:s")." IP为： ".get_client_ip()."添加的区域名称为：".$LogString."状态：添加成功",true);
                //日志记录结束
                $re["statusCode"] = 200;
                $re["message"] = "保存成功";
                $re["navTabId"] = "navTab";
                $re["forwardUrl"] = U('Model/arealist');
                $re["callbackType"] = "forward";
                $this->ajaxReturn($re);
            } else {
                //日志记录开始
                aLog("用户名为：".session("UserName")."于 ".date("Y-m-d H:i:s")." IP为： ".get_client_ip()."添加的区域名称为：".$LogString."状态：添加失败",false);
                //日志记录结束
                $re["statusCode"] = 300;
                $re["message"] = "保存失败";
                $this->ajaxReturn($re);
            }
        }
    }

    //更改视图
    public function areamodify()
    {
        if (!IS_AJAX) {
            return false;
        } else {
            $Area = D("Model");//实例化区域显示表
            $areaid = I("areaid", "", "intval");//区域id
            $info = $Area->field("model_ID,model_Name,model_Remark,model_SystemAgentsID")->where(array("model_ID" => $areaid))->find();
			//系统模块获取开始
            $Systemmodule = D("Systemmodule");//实例化系统模块Model
            $this->assign("moduleInfo", $Systemmodule->getsAuthInfo());
            //系统模块获取结束
            $this->assign("info", $info);
            $this->display();
        }
    }

    //更改方法
    public function areamodifyfunction()
    {
        if (!IS_AJAX) {
            return false;
        } else {
            $Area = D("Model");//实例化区域显示表
            //过滤模块名
            $lrmodel = htmlspecialchars(strip_tags(trim(I("areaName"))));
            $Astr = strreplace($lrmodel);
            $Astr = explode(",", $Astr);
            $areaName = FilterUniqueValues($Astr);//使用逗号将多个角色名称分隔成数组
            //查询是否存在
            $wheres['model_ID'] = array('neq',I("areaid","","intval"));
            $wheres['model_Name'] = $areaName[0];
            $modelcons = cRec('Model',$wheres);
            //查询是否存在
            //过滤模块名
            if($modelcons > 0){
                //日志记录开始
                aLog("用户名为：".session("UserName")."于 ".date("Y-m-d H:i:s")." IP为： ".get_client_ip()."模块名称为：".$lrUserName.",模块已存在。状态：更改失败",false);
                //日志记录结束
                $re["statusCode"] = 300;
                $re["message"] = "模块已存在";
                $this->ajaxReturn($re);
            } else if ($Area->where(array("model_ID" => I("areaid", "", "intval")))->setField(array("model_SystemAgentsID" => trim(I("moduleName")),"model_Name" => trim(I("areaName")), "model_Remark" => trim(I("textarea1"))))) {
                //日志记录开始
                aLog("用户名为：".session("UserName")."于 ".date("Y-m-d H:i:s")." IP为： ".get_client_ip()."更改区域名称成功",true);
                //日志记录结束
                $re["statusCode"] = 200;
                $re["message"] = "保存成功";
                $re["navTabId"] = "navTab";
                $re["forwardUrl"] = U('Model/arealist');
                $re["callbackType"] = "forward";
                $this->ajaxReturn($re);
            } else {
                //日志记录开始
                aLog("用户名为：".session("UserName")."于 ".date("Y-m-d H:i:s")." IP为： ".get_client_ip()."更改区域名称失败",false);
                //日志记录结束
                $re["statusCode"] = 300;
                $re["message"] = "保存失败";
                $this->ajaxReturn($re);
            }
        }
    }

    //删除方法
    public function areadel($areaid)
    {
        if (!IS_AJAX) {
            return false;
        } else {
            $Area = D("Model");//实例化区域显示表
            $Auth = D("Auth");//实例化权限表
            // $areaid = I("areaid", "", "intval");//区域id
            //先检查该区域是否被占用，如果被占用将不可删除
            if ($Auth->where(array("auth_area_id" => $areaid))->count() > 0) {
                //日志记录开始
                aLog("用户名为：".session("UserName")."于 ".date("Y-m-d H:i:s")." IP为： ".get_client_ip()."删除区域名称失败。原因：该显示区域已被占用",false);
                //日志记录结束
                $re["statusCode"] = 300;
                $re["message"] = "删除失败:<br />原因：该显示区域已被占用";
                $this->ajaxReturn($re);
            } else {
                if ($Area->where(array("model_ID"=>$areaid))->delete()) {
                    //日志记录开始
                    aLog("用户名为：".session("UserName")."于 ".date("Y-m-d H:i:s")." IP为： ".get_client_ip()."删除区域名称成功",true);
                    //日志记录结束
                    $re["statusCode"] = 200;
                    $re["message"] = "删除成功";
                    $re["navTabId"] = "navTab";
                    $re["forwardUrl"] = U('Model/arealist');
                    $re["callbackType"] = "forward";
                    $this->ajaxReturn($re);
                } else {
                    //日志记录开始
                    aLog("用户名为：".session("UserName")."于 ".date("Y-m-d H:i:s")." IP为： ".get_client_ip()."删除区域名称失败",false);
                    //日志记录结束
                    $re["statusCode"] = 300;
                    $re["message"] = "删除失败";
                    $this->ajaxReturn($re);
                }
            }
        }
    }
}