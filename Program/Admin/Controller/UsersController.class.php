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


class UsersController extends Controller
{
    /** */
    public function index()
    {
        $where = array("isOpen" => 1);
        $dao = M("creit_auth cer", "p_", DB_INTE_DATA);
        $lists = $dao->field("DISTINCT plat,companyName")->join("left join (select * from p_usertable ) ut on cer.plat= 
        ut.usertable_ID")->limit(0, 20)->where($where)->order("plat DESC")->select();
        $data = array();
        $i = 0;
        while ($i < count($lists)) {
            $data[$i]['company'] = $lists[$i]['companyName'];
            $data[$i]['data'] = self::creitLevel($lists[$i]['plat']);
            $i++;
        }
        $this->assignAll(['data' => $data]);
        $this->display();
    }

    protected static function creitLevel($plat)
    {
        $data = array();
        if (!empty($plat)) {
            $dao = M("creit_auth cre", "p_", DB_INTE_DATA);
            $level = $dao->join("LEFT JOIN (select * from p_bus_level_manage where plat = " . $plat . ") bl on cre.level = bl.englishname")->where(array("cre.plat" => $plat, "isOpen" => 1))->select();
            if ($level && is_array($level)) {
                $i = 0;
                while ($i < count($level)) {
                    $data[$i]['plat'] = $level[$i]['plat'];
                    $data[$i]['classname'] = $level[$i]['classname'];
                    $data[$i]['level'] = $level[$i]['englishname'];
                    $data[$i]['isOpen'] = $level[$i]['isOpen'];
                    $i++;
                }
            }
        }
        return $data;
    }

    public function lists()
    {
        $params = I();
        $page = !empty($params['pageNum']) ? $params['pageNum'] : 1;
        $numPerPage = !empty($params['numPerPage']) ? $params['numPerPage'] : 20;
        $keyword = !empty($params['keyword']) ? $params['keyword'] : '';
        $plat = !empty($params['plat']) ? $params['plat'] : '';
        $level = !empty($params['level']) ? $params['level'] : '';
        $users = M("user", "p_", DB_INTE_DATA);
        $where=[];
        if(!empty($params['level'])){
            $where['level'] = $params['level'];
        }
        if(!empty($params['plat'])){
            $where['plat'] = $params['plat'];
        }
        if(!empty($params['keyword'])){
            $where['busname'] = ["like","%".$params['keyword']."%"];
        }
        $ofset=($page - 1) * $numPerPage;
        $data = $users->where($where)->limit($ofset, $numPerPage)->select();
        $parents=[];
        foreach($data as $d){
            $p=$users->where(['id'=>$d['parent']])->find();
            if($p){
                $parents[]=$p['busname'];
            }else{
                $parents[]='';
            }
        }
        $totalCount = $users->where($where)->count();
        $this->assignAll([
            'data' => $data,
            'totalCount' => $totalCount,
            'page' => $page,
            'pageNum' => $page,
            'numPerPage' => $numPerPage,
            'plat'=>$plat,
            'level'=>$level,
            'keyword'=>$keyword,
            'parents'=>$parents
        ]);
        $this->display();
    }

}