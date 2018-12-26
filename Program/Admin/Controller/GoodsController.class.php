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


class GoodsController extends Controller
{
    /**商品列表 */
    public function index()
    {
        $params = I('post.');
        $page = empty($params['pageNum']) ? 1 : $params['pageNum'];
        $numPerPage = empty($params['numPerPage']) ? 20 : $params['numPerPage'];
        $state = empty($params['state']) ? "" : $params['state'];
        $classes = $this->getclasses();
        $classFloor1 = M('class')->where(['floorId' => 1])->select();
        $keywords = empty($params['keywords']) ? "" : $params['keywords'];
        $class1 = empty($params['class1']) ? "" : $params['class1'];
        $class2 = empty($params['class2']) ? "" : $params['class2'];
        $classId = empty($params['classId']) ? "" : $params['classId'];
        $where = array();
        $class3 = '';
        if ($keywords) {
            $where['name'] = ['like', '%' . $keywords . '%'];
        }
        if ($classId) {
            $where['classId'] = $classId;
            $class3 = M('class')->field('id,name')->where(['id' => $classId])->find();
        }
        if ($class1) {
            $class1 = M('class')->field('id,name')->where(['id' => $class1])->find();
        }
        if ($class2) {
            $class2 = M('class')->field('id,name')->where(['id' => $class2])->find();
        }
        if ($state) {
            $where['state'] = $state;
        }
        $offset = ($page - 1) * $numPerPage;
        $goods = M('goods')->where($where)->limit($offset, $numPerPage)->select();
        for ($i = 0; $i < count($goods); $i++) {
            $class = M('class')->where(['id' => $goods[$i]['classId']])->find();
            $goods[$i]['classname'] = $class['name'];
        }
        $totalCount = M('goods')->where($where)->count();
        $this->assignAll([
            'goods' => $goods,
            'keywords' => $keywords,
            'totalCount' => $totalCount,
            'page' => $page,
            'state' => $state,
            'numPerPage' => $numPerPage,
            'classes' => $classes,
            'classId' => $classId,
            'classFloor1' => $classFloor1,
            'class1' => $class1,
            'class2' => $class2,
            'class3' => $class3
        ]);
        $this->display();
    }
    /***
     * 添加商品
     */
    public function add()
    {
        $classes = $this->getclasses();
        $this->assignAll([
            'classes' => $classes
        ]);
        $this->display();
    }
    /**
     * 添加商品操作
     *
     * @return void
     */
    public function addFunction()
    {
        $params = I('post.');
        if ($params['name']) {
            $find = M('Goods')->where(['name' => $params['name']])->find();
            if ($find) {
                $re["statusCode"] = 300;
                $re["message"] = '此商品名称已存在，请换个名称';
                $this->ajaxReturn($re);
                exit();
            }
        }
        if ($_FILES['facePhoto'] != null && $_FILES['facePhoto'] != '') {
            $path = '/Public/uploads/goods/';
            $resUpload = D('Func')->DoUpload($path, $_FILES['facePhoto']);
            if (!$resUpload) {
                $re["statusCode"] = 300;
                $re["message"] = '图片上传失败';
                $this->ajaxReturn($re);
                exit();
            }
        }
        $data['name'] = $params['name'];
        $data['classId'] = $params['classId'];
        $data['detail'] = $params['detail'];
        $data['state'] = $params['state'];
        $data['buyPrice'] = $params['buyPrice'];
        $data['nowPrice'] = $params['nowPrice'];
        $data['jifen'] = $params['jifen'];
        $data['createTime'] = time();
        $data['createIp'] = get_client_ip();
        if ($resUpload) {
            $data['facePhoto'] = $path . $resUpload['savepath'] . $resUpload['savename'];
        }
        $model = M("goods");
        $insert = $model->add($data);
        if ($insert) {
            D('Func')->addLog($data['name'] . '保存成功', 'true');
            $re["statusCode"] = 200;
            $re["message"] = '保存成功';
            $re["navTabId"] = "navTab";
            $re["forwardUrl"] = U('Goods/update');
            $re["callbackType"] = "forward";
        } else {
            D('Func')->addLog($data['name'] . '保存失败', 'false');
            $re["statusCode"] = 300;
            $re["message"] = '保存失败';
        }
        $this->ajaxReturn($re);
    }

    /**
     * 商品修改
     *
     * @return void
     */
    public function update()
    {
        $classes = $this->getclasses();
        $model = M("goods");
        $id = I("id", "", "intval");
        $data = $model->where(array("id" => $id))->find();
        $this->assignAll([
            "data" => $data,
            'classes' => $classes
        ]);
        $this->display();
    }
    /**
     * 修改商品操作
     *
     * @return void
     */
    public function updateFunction()
    {
        $params = I('post.');
        if ($params['name']) {
            $where['id'] = ['neq', $params['id']];
            $find = M('goods')->where(['name' => $params['name']])->Where($where)->find();
            if ($find) {
                $re["statusCode"] = 300;
                $re["message"] = '此商品名称已存在，请换个名称';
                $this->ajaxReturn($re);
                exit();
            }
        }
        if ($_FILES['facePhoto'] != null && $_FILES['facePhoto'] != '' && $_FILES['fecePhoto']['size'] != 0) {
            $path = '/Public/uploads/goods/';
            $resUpload = D('Func')->DoUpload($path, $_FILES['fecePhoto']);
            if (!$resUpload) {
                $re["statusCode"] = 300;
                $re["message"] = '图片上传失败';
                $this->ajaxReturn($re);
                exit();
            }
        }
        $old = M('goods')->where(['id' => $params['id']])->find();
        $data['name'] = $params['name'];
        $data['classId'] = $params['classId'];
        $data['detail'] = $params['detail'];
        $data['state'] = $params['state'];
        $data['buyPrice'] = $params['buyPrice'];
        $data['nowPrice'] = $params['nowPrice'];
        $data['jifen'] = $params['jifen'];
        if ($resUpload) {
            $data['facePhoto'] = $path . $resUpload['savepath'] . $resUpload['savename'];
        }
        $update = M("goods")->where(['id' => $params['id']])->data($data)->save();
        if ($update) {
            if ($resUpload) {
                D('Func')->unlink($old['facePhoto']);
            }
            D('Func')->addLog($data['name'] . '修改成功', 'true');
            $re["statusCode"] = 200;
            $re["message"] = '修改成功';
            $re["navTabId"] = "navTab";
            $re["forwardUrl"] = U('Goods/index');
            $re["callbackType"] = "forward";
        } else {
            D('Func')->addLog($data['name'] . '修改失败', 'true');
            $re["statusCode"] = 300;
            $re["message"] = '保存失败';
        }
        $this->ajaxReturn($re);
    }
    /**
     * 商品删除
     *
     * @return void
     */
    public function del()
    {
        if (I('decldel') == null) {
            return false;
        } else {
            $where = ['id' => I('decldel')];
            $one = M('goods')->where($where)->find();
            $res = M('goods')->where($where)->delete();
            if ($res) {
                D('Func')->unlink($one['photo']);
                //日志记录开始
                aLog("用户名为：" . session("UserName") . "于 " . date("Y-m-d H:i:s") . " IP为： " . get_client_ip() . "添加方案ID为 " . I('decldel') . "删除成功", false);
                //日志记录结束
                $re["statusCode"] = 200;
                $re["message"] = '删除成功';
                $re["navTabId"] = "navTab";
                $re["forwardUrl"] = U('Class/index');
                $re["callbackType"] = "forward";
            } else {
                //日志记录开始
                aLog("用户名为：" . session("UserName") . "于 " . date("Y-m-d H:i:s") . " IP为： " . get_client_ip() . "添加方案ID为 " . I('decldel') . "删除失败", false);
                //日志记录结束
                $re["statusCode"] = 300;
                $re["message"] = $res->getlastSql();
            }
            $this->ajaxReturn($re);
        }
    }
    /**
     * 获取分类数组
     *
     * @return void
     */
    public function getclasses()
    {
        /**顶级分类 */
        $classes = M('class')->field('id,name,pId,floorId,gId')->where(['pId' => 0])->select();
        for ($i = 0; $i < count($classes); $i++) {
            /**二级分类 */
            $arrs = M('class')->field('id,name,pId,floorId,gId')->where(['gId' => $classes[$i]['id'], 'floorId' => 2])->select();
            for ($a = 0; $a < count($arrs); $a++) {
                $classes[$i]['child'][] = M('class')->field('id,name,pId,floorId,gId')->where(['id' => $arrs[$a]['id']])->find();
                /**三级分类 */
                $find = M('class')->field('id,name,pId,floorId,gId')->where(['pId' => $arrs[$a]['id'], 'floorId' => 3])->select();
                if (!empty($find) && count($find) >= 1) {
                    for ($b = 0; $b < count($find); $b++) {
                        $classes[$i]['child'][] = M('class')->field('id,name,pId,floorId,gId')->where(['id' => $find[$b]['id']])->find();
                        /**四级分类 */
                        $bfind = M('class')->field('id,name,pId,floorId,gId')->where(['pId' => $find[$b]['id'], 'floorId' => 4])->select();
                        if (!empty($bfind) && count($bfind) >= 1) {
                            for ($c = 0; $c < count($bfind); $c++) {
                                $classes[$i]['child'][] = M('class')->field('id,name,pId,floorId,gId')->where(['id' => $bfind[$c]['id']])->find();
                                /**五级分类 */
                                $cfind = M('class')->field('id,name,pId,floorId,gId')->where(['pId' => $bfind[$c]['id'], 'floorId' => 5])->select();
                                if (!empty($cfind) && count($cfind) >= 1) {
                                    for ($d = 0; $d < count($cfind); $d++) {
                                        $classes[$i]['child'][] = M('class')->field('id,name,pId,floorId,gId')->where(['id' => $cfind[$d]['id']])->find();
                                        //无限极
                                    }
                                } else {
                                    continue;
                                }
                            }
                        } else {
                            continue;
                        }
                    }
                } else {
                    continue;
                }
            }
        }
        return $classes;
    }
    /**
     * 修改上架下架状态
     *
     * @return void
     */
    public function changestate()
    {
        $id = I('id');
        $val = I('val');
        if ($id != null && $val != null) {
            $re = M('Goods')->where(['id' => $id])->data(['state' => $val])->save();
            $this->ajaxReturn($re);
        } else {
            return false;
        }
    }

    public function changeFloor()
    {
        $params = I('post.');
        if (!M('class')->where(['pId' => $params['classId']])->find()) {
            return false;
        } else {
            $data['options'] = M('class')->where(['pId' => $params['classId']])->select();
            $this->ajaxReturn($data);
        }
    }
    /**
     * 商品入库
     *
     * @return void
     */
    public function addgoods()
    {
        $goodsId = I('id');
        $good = M('Goods')->where(['id' => $goodsId])->find();
        $this->assignAll([
            'goodsId' => $goodsId,
            'good' => $good
        ]);
        $this->display();
    }
    /**
     * 商品入库操作
     *
     * @return void
     */
    public function addgoodsFunc()
    {
        $params = I('post.');
        $data['goodsId'] = $params['goodsId'];
        $data['num'] = $params['num'];
        $data['addPrice'] = $params['addPrice'];
        $data['addTime'] = time();
        $data['addBy'] = session("UserName");
        $res = M('goods_add')->add($data);
        $goodnum = M('goods')->where(['id' => $params['goodsId']])->find();
        $number = $goodnum['hasNum'] + $params['num'];
        $good = M('goods')->where(['id' => $params['goodsId']])->data(['hasNum' => $number])->save();

        if ($res) {
            //日志记录开始
            aLog("用户名为：" . session("UserName") . "于 " . date("Y-m-d H:i:s") . " IP为： " . get_client_ip() . "添加方案ID为 " . $params['goodsId'] . "入库成功", false);
                //日志记录结束
            $re["statusCode"] = 200;
            $re["message"] = '入库成功';
            $re["navTabId"] = "navTab";
            $re["forwardUrl"] = U('Good/index');
            $re["callbackType"] = "forward";
        } else {
            //日志记录开始
            aLog("用户名为：" . session("UserName") . "于 " . date("Y-m-d H:i:s") . " IP为： " . get_client_ip() . "添加方案ID为 " . $params['goodsId'] . "入库失败", false);
            //日志记录结束
            $re["statusCode"] = 300;
            $re["message"] = '入库失败';
        }
        $this->ajaxReturn($re);
    }
    /**
     * 商品入库列表
     *
     * @return void
     */
    public function addlist()
    {
        $params = I('post.');
        $keywords = isset($params['keywords']) ? $params['keywords'] : "";
        $datestart = isset($params['datestart']) ? $params['datestart'] : "";
        $dateend = isset($params['dateend']) ? $params['dateend'] : "";
        $classId = isset($params['classId']) ? $params['classId'] : "";
        $page = isset($params['pageNum']) ? $params['pageNum'] : 1;
        $numPerPage = isset($params['numPerPage']) ? $params['numPerPage'] : 20;
        $offset = ($page - 1) * $numPerPage;
        $classFloor1 = M('class')->field('id,name,pId,floorId')->where(['floorId' => 1])->select();
        $class1 = empty($params['class1']) ? "" : $params['class1'];
        $class2 = empty($params['class2']) ? "" : $params['class2'];
        $classId = empty($params['classId']) ? "" : $params['classId'];
        $where = [];
        $class3 = '';
        if (!empty($keywords)) {
            $where['p_goods.name'] = ['like', '%' . $keywords . '%'];
        }
        if (!empty($datestart)) {
            $where['addTime'] = ['gt', strtotime($datestart)];
        }
        if (!empty($dateend)) {
            $where['addTime'] = ['lt', strtotime($dateend)];
        }
        if (!empty($datestart) && !empty($dateend)) {
            $where['addTime'] = ['between', [strtotime($datestart), strtotime($dateend)]];
        }
        if (!empty($classId)) {
            $where['classId'] = $classId;
            $class3 = M('class')->field('id,name')->where(['id' => $classId])->find();
        }
        if (!empty($class1)) {
            $class1 = M('class')->field('id,name')->where(['id' => $class1])->find();
        }
        if (!empty($class2)) {
            $class2 = M('class')->field('id,name')->where(['id' => $class2])->find();
        }
        $addlist = M('goods_add')->join('left join p_goods on p_goods_add.goodsId=p_goods.id')->where($where)->limit($offset, $numPerPage)->select();
        $totalCount = M('goods_add')->join('left join p_goods on p_goods_add.goodsId=p_goods.id')->where($where)->count();
        for ($i = 0; $i < count($addlist); $i++) {
            $one = M('goods')->where(['id' => $addlist[$i]['goodsId']])->find();
            $addlist[$i]['goodsname'] = $one['name'];
        }
        $this->assignAll([
            'addlist' => $addlist,
            'datestart' => $datestart,
            'dateend' => $dateend,
            'keywords' => $keywords,
            'classId' => $classId,
            'page' => $page,
            'numPerPage' => $numPerPage,
            'totalCount' => $totalCount,
            'classFloor1' => $classFloor1,
            'class1' => $class1,
            'class2' => $class2,
            'class3' => $class3
        ]);
        $this->display();
    }
    /**
     * 删除入库记录
     *
     * @return void
     */
    public function deladdlist()
    {
        $ids = I('decldel');
        if ($ids == null || $ids == '') {
            $re["statusCode"] = 300;
            $re["message"] = '请先选择';
            $re["statusCode"] = 300;
            $this->ajaxReturn($re);
            exit();
        }
        $num = 0;
        if (!is_numeric($ids)) {
            $ids = explode(',', $ids);
            foreach ($ids as $id) {
                $r = M('goods_add')->where(['id' => $id])->delete();
                if ($r) {
                    $num++;
                }
            }
            if ($num > 0) {
                $re["statusCode"] = 200;
                $re["message"] = '成功删除' . $num . '条数据';
                $re["navTabId"] = "navTab";
                $re["forwardUrl"] = U('Goods/addlist');
                $re["callbackType"] = "forward";
            } else {
                $re["statusCode"] = 300;
                $re["message"] = '删除失败';
            }
        } else {
            $r = M('goods_add')->where(['id' => $ids])->delete();
            if ($r) {
                $re["statusCode"] = 200;
                $re["message"] = '成功删除数据';
                $re["navTabId"] = "navTab";
                $re["forwardUrl"] = U('Goods/addlist');
                $re["callbackType"] = "forward";
            } else {
                $re["statusCode"] = 300;
                $re["message"] = '删除失败';
            }
        }
        $this->ajaxReturn($re);
    }

}
