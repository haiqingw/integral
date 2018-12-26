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


class ClassController extends Controller
{
    /**分类列表 */
    public function index()
    {
        $params = I('post.');
        $page = isset($params['pageNum']) ? $params['pageNum'] : 1;
        $numPerPage = isset($params['numPerPage']) ? $params['numPerPage'] : 20;
        $keywords = I('keywords');
        $where = [];
        $offset = ($page - 1) * $numPerPage;
        if (!empty($keywords)) {
            $where['name'] = ['like', '%' . $keywords . '%'];
        }
        $data = M('class')->where($where)->limit($offset, $numPerPage)->select();
        $totalCount = M('class')->where($where)->count();
        for ($i = 0; $i < count($data); $i++) {
            $p = M('class')->where(['id' => $data[$i]['pId']])->find();
            $data[$i]['parentname'] = $p['name'];
        }
        $this->assign([
            'data' => $data,
            'totalCount' => $totalCount,
            'numPerPage' => $numPerPage,
            'page' => $page
        ]);
        $this->display();
    }
    /***
     * 分类添加
     */
    public function add()
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
        $this->assignAll([
            'classes' => $classes,
        ]);
        $this->display();
    }

    /**
     * 分类添加方法
     *
     * @return void
     */
    public function addFunction()
    {
        $params = I('post.');
        if ($params['name']) {
            $find = M('class')->where(['name' => $params['name']])->find();
            if ($find) {
                $re["statusCode"] = 300;
                $re["message"] = '此分类名称已存在，请换个名称';
                $this->ajaxReturn($re);
                exit();
            }
        }
        if ($_FILES['photo'] != null && $_FILES['photo'] != '') {
            $path = '/Public/uploads/class/';
            $resUpload = D('Func')->DoUpload($path, $_FILES['photo']);
            if (!$resUpload) {
                $re["statusCode"] = 300;
                $re["message"] = '图片上传失败';
                $this->ajaxReturn($re);
                exit();
            }
        }
        $data['name'] = $params['name'];
        $data['sort'] = $params['sort'];
        $data['pId'] = $params['pId'];
        if ($data['pId'] == 0) {
            $data['floorId'] = 1;
        } else {
            $parent = M('class')->where(['id' => $data['pId']])->find();
            $data['floorId'] = $parent['floorId'] + 1;
            if ($parent['pId'] != 0) {
                $data['gId'] = $parent['gId'];
            } else {
                $data['gId'] = $parent['id'];
            }
        }
        if ($resUpload) {
            $data['photo'] = $path . $resUpload['savepath'] . $resUpload['savename'];
        }
        $model = M("class");
        $insert = $model->add($data);
        if ($insert) {
            D('Func')->addLog($data['name'] . '保存成功', 'true');
            $re["statusCode"] = 200;
            $re["message"] = '保存成功';
            $re["navTabId"] = "navTab";
            $re["forwardUrl"] = U('Grade/index');
            $re["callbackType"] = "forward";
        } else {
            D('Func')->addLog($data['name'] . '保存失败', 'true');
            $re["statusCode"] = 300;
            $re["message"] = '保存失败';
        }
        $this->ajaxReturn($re);
    }

    /**
     * 二级分类修改
     *
     * @return void
     */
    public function update()
    {
        $model = M("class");
        $id = I("id", "", "intval");
        $data = $model->where(array("id" => $id))->find();
        $this->assignAll([
            "data" => $data,
            'grades' => $grades
        ]);
        $this->display();
    }

    public function updateFunction()
    {
        $params = I('post.');
        if ($params['name']) {
            $where['id'] = ['neq', $params['id']];
            $find = M('class')->where(['name' => $params['name']])->Where($where)->find();
            if ($find) {
                $re["statusCode"] = 300;
                $re["message"] = '此分类名称已存在，请换个名称';
                $this->ajaxReturn($re);
                exit();
            }
        }
        if ($_FILES['photo'] != null && $_FILES['photo'] != '' && $_FILES['photo']['error'] == 0) {
            $path = '/Public/uploads/class/';
            $resUpload = D('Func')->DoUpload($path, $_FILES['photo']);
            if (!$resUpload) {
                $re["statusCode"] = 300;
                $re["message"] = '图片上传失败';
                $this->ajaxReturn($re);
                exit();
            }
        }
        $old = M('class')->where(['id' => $params['id']])->find();
        $data['name'] = $params['name'];
        $data['sort'] = $params['sort'];
        if ($resUpload) {
            $data['photo'] = $path . $resUpload['savepath'] . $resUpload['savename'];
        }
        $update = M("class")->where(['id' => $params['id']])->data($data)->save();
        if ($update) {
            if ($resUpload) {
                D('Func')->unlink($old['photo']);
            }
            D('Func')->addLog($data['name'] . '修改成功', 'true');
            $re["statusCode"] = 200;
            $re["message"] = '修改成功';
            $re["navTabId"] = "navTab";
            $re["forwardUrl"] = U('Class/index');
            $re["callbackType"] = "forward";
        } else {
            D('Func')->addLog($data['name'] . '修改失败', 'true');
            $re["statusCode"] = 300;
            $re["message"] = '保存失败';
        }
        $this->ajaxReturn($re);
    }

    public function del()
    {
        if (I('decldel') == null) {
            return false;
        } else {
            $where = ['id' => I('decldel')];
            $one = M('class')->where($where)->find();
            $res = M('class')->where($where)->delete();
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

}