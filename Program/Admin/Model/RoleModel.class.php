<?php
/**
 * Created by PhpStorm.
 * User: 宁
 * Date: 2015/9/1
 * Time: 15:26
 * 角色Model
 */
namespace Admin\Model;

use Think\Model;

class RoleModel extends Model
{
    //权限分配设置
    //$auth是一堆数组信息,给当前角色的权限ID信息
    public function saveAuth($auth,$role_id)
    {
        $auth_ids = implode(",", $auth);
        //查询auth表中的信息
        $info = M("Auth")->field("auth_c,auth_a")->select($auth_ids);
        $auth_ac = "";
        for ($v = 0; $v < count($info); $v++) {
            if(!empty($info[$v]["auth_c"]) && !empty($info[$v]["auth_a"])){
                $auth_ac .=$info[$v]["auth_c"].'-'.$info[$v]["auth_a"].',';
            }
        }
        $auth_ac = rtrim($auth_ac,",");
        $d = array(
            "role_id" => $role_id,
            "role_auth_ids" => $auth_ids,
            "role_auth_ac" => $auth_ac
        );
        return $this->save($d);
    }
}