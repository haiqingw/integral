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


class GoodsguigeController extends Controller
{
    /***
     * 添加或修改商品规格
     */
    public function add()
    {
        $goodsId=I('goodsId');
        $guiges=M('goods_guige')->where(['goodsId'=>$goodsId])->select();
        $this->assignAll([
            'guiges'=>$guiges,
            'goodsId'=>$goodsId,
            'guigenum'=>count($guiges)
        ]);
        $this->display();
    }
    /**
     * 商品规格修改操作
     *
     * @return void
     */
    public function updateFunction()
    {
        $goodsId = I('post.goodsId');
        $name=I('post.name');
        $buyPrice=I('post.buyPrice');
        $nowPrice=I('post.nowPrice');
        $jifen=I('post.jifen');
        $id=I('post.id');
        $resnum=0;
        for($i=0;$i<count($name);$i++){
            if($name[$i]!=null && $name[$i]!=''){
                $find=M('goods_guige')->where(['id'=>$id[$i]])->find();
                $data['name'] = $name[$i];
                    $data['buyPrice'] = $buyPrice[$i];
                    $data['nowPrice'] = $nowPrice[$i];
                    $data['jifen'] = $jifen[$i];
                    $data['goodsId'] = $goodsId;
                if($find){
                    $res=M('goods_guige')->where(['id'=>$id[$i]])->data($data)->save();
                    if($res){
                        $resnum++;
                        D('Func')->addLog($data['name'].'保存成功','true');
                    }else{
                        D('Func')->addLog($data['name'].'保存失败','false');
                    }
                }else{
                    $res=M('goods_guige')->data($data)->add();
                    if($res){
                        $resnum++;
                        D('Func')->addLog($data['name'].'保存成功','true');
                    }else{
                        D('Func')->addLog($data['name'].'保存失败','false');
                    }
                }
            }
        }
        if($resnum>0){
            $re["statusCode"] = 200;
            $re["message"] = '保存成功';
            $re["navTabId"] = "navTab";
            $re["forwardUrl"] = U('Goodsguige/add');
            $re["callbackType"] = "forward";
        }else{
            $re["statusCode"] = 300;
            $re["message"] = '保存失败';
        }
        $this->ajaxReturn($re);
    }

/**
 * 删除商品规格
 *
 * @return void
 */    
    public function delguige()
    {
        if (I('id') == null) {
            return false;
        } else {
            $id=I('id');
            $where = ['id' => I('id')];
            $res = M('goods_guige')->where($where)->delete();
            if($res){
                $photos = M('goods_photo')->where(['guigeId'=>$id])->select();
                if($photos!=null){
                    foreach($photos as $photo){
                        $guigephotos=M('goods_photo')->where(['guigeId'=>$photo['id']])->delete();
                        M('Func')->unlink($photo['photo']);
                    }
                }
            }
            $this->ajaxReturn($res);
        }
    }


}