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

class AgentsModel extends Model
{
    //更新代理商推荐码
    public function upbusnum()
    {
        $usertable = M('usertable');  //实例化数据表

        $rolerow = $this->getRolerow();  //获得管理员角色ID组

        $whereone['usertable_Role_id'] = array("in",$rolerow);
        $whereone['usertable_status'] = 0;
        $selinfo = $usertable->field('usertable_ID,usertable_Numb')->where($whereone)->select();
        //随机数
        for ($i=0; $i < count($selinfo); $i++) {
            if (empty($selinfo[$i]['usertable_Numb'])) {
				$rands = $this->getrands();  //获取随机数
                /*do(
                    $rands = $this->getrands();  //获取随机数
                    $cons = $usertable->where(array('usertable_Numb'=>$rands))->count();
                ) while ($cons < 1);*/
                $usertable->data(array('usertable_Numb'=>$rands))->where(array('usertable_ID'=>$selinfo[$i]['usertable_ID']))->save();
            }
        }
    }

    protected function getrands()
    {
        //生成随机数
        for($j=0;$j<6;$j++){
            $n = rand(1,9);
            $rands.=$n;
        }
        //生成随机数
        //返回随机数
        return $rands;
    }

    //查询角色信息方法(角色ID组)
    protected function getRolerow()
    {
        //查询角色信息开始
        $whrow['role_type'] = 2;
        $roleInfo = M("role")->field("role_id")->where($whrow)->order("role_id DESC")->select();
        $roleidrow = '';
        foreach ($roleInfo as $key => $value) {
            $roleidrow[] = $value['role_id'];
        }
        $roleret = implode(',',$roleidrow);
        return $roleret;
        //查询角色信息结束
    }
}