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


class GoodsphotoController extends Controller
{
    /**
     * 商品图片列表
     *
     * @return void
     */
    public function index()
    {
        $goodsId = I('goodsId');
        $good = M('goods')->where(['id' => $goodsId])->find();
        $guigeId = I('guigeId') != null ? I('guigeId') : 0;
        $guige = M('goods_guige')->where(['id' => $guigeId])->find();
        $photos = M('goods_photo')->where(['goodsId' => $goodsId, 'guigeId' => $guigeId])->select();
        $guiges = M('goods_guige')->where(['goodsId' => $goodsId])->select();
        $this->assignAll([
            'photos' => $photos,
            'goodsId' => $goodsId,
            'guiges' => $guiges,
            'guigeId' => $guigeId,
            'good' => $good,
            'guige'=>$guige
        ]);
        $this->display();
    }

    public function add()
    {
        $data['goodsId'] = I('post.goodsId');
        $data['guigeId'] = I('post.guigeId');
        $resnum = 0;
        $path = '/Public/uploads/goodsphoto/';
        if ($_FILES['photo1'] != null && $_FILES['photo1'] != '') {
            $photo1 = $_FILES['photo1'];
            $resUpload1 = D('Func')->DoUpload($path, $photo1);
            if ($resUpload1) {
                $data['photo'] = $path . $resUpload1['savepath'] . $resUpload1['savename'];
                $model = M("goods_photo")->add($data);
                $resnum++;
            }
        }
        if ($_FILES['photo2'] != null && $_FILES['photo2'] != '') {
            $photo2 = $_FILES['photo2'];
            $resUpload2 = D('Func')->DoUpload($path, $photo2);
            if ($resUpload2) {
                $data['photo'] = $path . $resUpload2['savepath'] . $resUpload2['savename'];
                $model = M("goods_photo")->add($data);
                $resnum++;
            }
        }
        if ($_FILES['photo3'] != null && $_FILES['photo3'] != '') {
            $photo3 = $_FILES['photo3'];
            $resUpload3 = D('Func')->DoUpload($path, $photo3);
            if ($resUpload3) {
                $data['photo'] = $path . $resUpload3['savepath'] . $resUpload3['savename'];
                $model = M("goods_photo")->add($data);
                $resnum++;
            }
        }
        if ($_FILES['photo4'] != null && $_FILES['photo4'] != '') {
            $photo4 = $_FILES['photo4'];
            $resUpload4 = D('Func')->DoUpload($path, $photo4);
            if ($resUpload4) {
                $data['photo'] = $path . $resUpload4['savepath'] . $resUpload4['savename'];
                $model = M("goods_photo")->add($data);
                $resnum++;
            }
        }
        if ($resnum > 0) {
            $re["statusCode"] = 200;
            $re["message"] = '保存成功' . $resnum . '个文件';
            $re["navTabId"] = "navTab";
            $re["forwardUrl"] = U('Goodsphoto/index') . "?goodsId=" . $goodsId;
            $re["callbackType"] = "forward";
        } else {
            $re["statusCode"] = 300;
            $re["message"] = '保存失败';
        }
        $this->ajaxReturn($re);
    }

    public function del()
    {
        $id = I('id');
        if ($id) {
            $one = M('goods_photo')->where(['id' => $id])->find();
            $del = M('goods_photo')->where(['id' => $id])->delete();
            if ($del) {
                M('Func')->unlink($one['photo']);
                $re["statusCode"] = 200;
                $re["message"] = '删除成功';
                $re["navTabId"] = "navTab";
                $re["forwardUrl"] = U('Goodsphoto/index') . "?goodsId=" . $one['goodsId'];
                $re["callbackType"] = "forward";
            } else {
                $re["statusCode"] = 300;
                $re["message"] = '保存失败';
            }
            $this->ajaxReturn($re);
        } else {
            return false;
        }
    }


}