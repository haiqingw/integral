<?php
/**
 * Created by PhpStorm.
 * User: Ning.an
 * Date: 2016/10/18
 * Time: 18:31
 * Personal Website: anchina.net
 * Email:ai_yuem@aliyun.com
 * 显示区域modle
 */
namespace Admin\Model;

use Think\Model;

class ModelModel extends Model
{
    //查询显示区域名称
    public function getsAreaName($ModleID)
    {
        $fields = array(
            "model_Name"
        );
        $where = array(
            "model_ID" => intval($ModleID)
        );
        $info = $this->field($fields)->where($where)->find();
        return $info["model_Name"];
    }

    //根据系统模块ID查询显示区域
    public function getsShowArea($AgentsId)
    {
        $where = array(
            "model_SystemAgentsID" => $AgentsId
        );
        $fields = array(
            "model_ID",
            "model_Name"
        );
        $info = $this->field($fields)->where($where)->order("model_Sortno ASC")->select();
        return $info;
    }
}