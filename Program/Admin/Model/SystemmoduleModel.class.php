<?php
/**
 * Created by PhpStorm.
 * User: Ning.an
 * Date: 2016/10/13
 * Time: 18:49
 * Personal Website: anchina.net
 * Email:ai_yuem@aliyun.com
 * 系统模块Model
 */
namespace Admin\Model;

use Think\Model;

class SystemmoduleModel extends Model
{
    //添加方法
    public function adds($arr)
    {
        return $this->add($arr);
    }

    //统计信息总数方法
    public function totalCount()
    {
        return $this->count(); //统计信息总数
    }

    //查询信息
    public function PageList($offset, $limit)
    {
        $fields = array(
            "smID",
            "smName",
            "smCreateTime",
            "smRemarks"
        );
        $info = $this->field($fields)->limit($offset, $limit)->order("smID DESC")->select();
        return $info;
    }

    //根据ID查询信息方法
    public function getsIdSelect($smID)
    {
        $fields = array(
            "smName",
            "smRemarks"
        );
        $where = array(
            "smID" => intval($smID)
        );
        $info = $this->field($fields)->where($where)->find();
        return $info;
    }
    //根据ID查询信息方法
    public function getsIdSelectName($smID)
    {
        $fields = array(
            "smName"
        );
        $where = array(
            "smID" => intval($smID)
        );
        $info = $this->field($fields)->where($where)->find();
        return $info["smName"];
    }

    //编辑信息方法
    public function update($arr)
    {
        return $this->save($arr);
    }

    //检查系统模块是否被使用方法
    public function Counts($smId)
    {
        $Model = D("Model");//实例化权限Model
        $where = array(
            "model_SystemAgentsID" => intval($smId)
        );
        $info = $Model->where($where)->count();
        return $info;
    }

    //删除系统模块信息方法
    public function delway($smID)
    {
        return $this->delete($smID);
    }

    //查询权限模块信息方法
    public function getsAuthInfo()
    {
        $fields = array(
            "smID",
            "smName"
        );
        $info = $this->field($fields)->order("smID DESC")->select();
        return $info;
    }
}