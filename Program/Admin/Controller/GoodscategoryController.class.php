<?php
namespace Admin\Controller;

use Think\Controller;
/** 
 * @Author: HaiQing.Wu 
 * @Date: 2018-12-19 11:03:31 
 * @Desc: 商品分类 
 */
class GoodscategoryController extends CommonController
{
    /** 
     * @Author: HaiQing.Wu 
     * @Date: 2018-12-19 11:04:23 
     * @Desc:  
     */
    public function index()
    {

        $arrays = M("auth")->field("auth_id,auth_name,auth_pid,auth_level")->select();

        $tree = $this->lists_to_tree($arrays, 0, "auth_id", "auth_pid");
        foreach ($tree as $key => $val) {
            echo str_repeat("--", $val['auth_level']) . $val['auth_name'];
        }
        dump($tree);
        // $this->display();
    }
    /** 
     * @Author: HaiQing.Wu 
     * @Date: 2018-12-19 11:43:46 
     * @Desc: 
     * @params:  
     */
    public function lists_to_tree($arrays, $root = 0, $pk = "id", $pid = "pid", $clild = 'son')
    {
        $tree = array();
        if (is_array($arrays)) {
            //创建基于主键的数组引用
            $refer = array();
            foreach ($arrays as $key => $data) {
                $refer[$data[$pk]] = &$arrays[$key];
            }
            foreach ($arrays as $key => $data) {
                //判断是否存在 parent
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    $tree[] = &$arrays[$key];
                } else {
                    if (isset($refer[$parentId])) {
                        $parent = &$refer[$parentId];
                        $parent[$clild][] = &$arrays[$key];
                    }
                }
            }
        }
        return $tree;
    }
}
    