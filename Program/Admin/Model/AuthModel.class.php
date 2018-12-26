<?php
/**
 * Created by PhpStorm.
 * User: 宁
 * Date: 2015/9/2
 * Time: 11:54
 * 权限Model
 */
namespace Admin\Model;

use Think\Model;

class AuthModel extends Model
{
    //添加权限方法
    public function addAuth($auth){
        //$auth里边存在5个信息，还缺少两个关键信息：auth_path,auth_level
        //insert 生成一个新纪录
        //update 把path  和level 更新进去
        $new_id = $this->add($auth);//添加到数据库表中返回新纪录的主键id值
        if($auth["auth_pid"]==0){
            $auth_path = $new_id;
        }else{
            //查询指定父级的全路径,条件：$auth["auth_pid"]
            $pinfo = $this->find($auth["auth_pid"]);
            $p_path = $pinfo["auth_path"];//父级全路径
            $auth_path = $p_path.'-'.$new_id;
        }
        //auth_level数目：全路径里面的中恒线的个数
        //把全路径变为数组，计算数组的个数和减去-1，就是level的信息
        $auth_level = count(explode("-",$auth_path))-1;
        $lrdata = array(
            "auth_id"   =>  $new_id,
            "auth_path" => $auth_path,
            "auth_level" => $auth_level
        );
        return $this->save($lrdata);

    }
}