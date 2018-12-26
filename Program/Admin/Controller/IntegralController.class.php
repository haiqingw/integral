<?php
namespace Admin\Controller;

use Think\Controller;

class IntegralController extends CommonController
{
    /** 
     * @Author: HaiQing.Wu 
     * @Date: 2018-12-19 16:17:49 
     * @Desc: 记录 
     */
    public function cords()
    {
        $params = I();
        $where = array();
        $plat = empty($params['plat']) ? "" : $params['plat'];
        $keys = empty($params['keys']) ? "" : $params['keys'];
        $startDate = empty($params['wsDate']) ? date("Y-m-d") : $params['wsDate'];
        $endDate = empty($params['weDate']) ? date("Y-m-d") : $params['weDate'];
        $page = empty($params['pageNum']) ? 1 : $params['pageNum'];
        $limit = empty($params['numPerPage']) ? 20 : $params['numPerPage'];
        $keywords = empty($params['wkeywords']) ? "" : $params['wkeywords'];
        //外部数据
        $dao = M(P_IN_COR . " cre", "p_", DB_INTE_DATA);
        $where = array(
            "plat" => $plat,
            "createTime" => array(
                array("egt", strtotime($startDate)),
                array("lt", strtotime("$endDate +1 day"))
            )
        );
        $totalCount = 0;
        $offset = ($page - 1) * $limit;
        $arrays = $dao->join("left join  (select id ids,busname,phone from p_user ) uu on uu.ids=cre.bid  left join (select id cid,commodityName from p_commodity) comm on comm.cid=cre.proid")->field("*,from_unixtime(createTime,'%b.%d.%Y %H:%i:%s') createTime,case types when 1 then '激活' when 2 then '交易' when 3 then '笔数' when 4 then '招商' end tyname,case isSuccess when 1 then '成功' when 2 then '失败' end isSuc")->where($where)->limit($offset, $limit)->order("createTime DESC")->select();
        if ($arrays) {
            $i = 0;
            while ($i < count($arrays)) {
                $arrays[$i]['phone'] = hidePhone($arrays[$i]['phone']);
                $i++;
            }
            $totalCount = $dao->where($where)->count();
        }
        $this->assignAll([
            "totalCount" => $totalCount, "plat" => $plat,
            "resArray" => $arrays, "numPerPage" => $limit,
            "page" => $page, "wsDate" => $startDate, "keys" => $keys,
            "weDate" => $endDate, "keywords" => $keywords, "url" => U('Integral/' . $keys)
        ]);
        $this->display();
    }
    /** 
     * @Author: HaiQing.Wu 
     * @Date: 2018-12-19 14:51:51 
     * @Desc: 列表 
     */
    public function lists()
    {
        $params = I();
        $where = array();
        $plat = empty($params['plat']) ? "" : $params['plat'];
        $keys = empty($params['keys']) ? "" : $params['keys'];
        $startDate = empty($params['wsDate']) ? date("Y-m-d") : $params['wsDate'];
        $endDate = empty($params['weDate']) ? date("Y-m-d") : $params['weDate'];
        $page = empty($params['pageNum']) ? 1 : $params['pageNum'];
        $limit = empty($params['numPerPage']) ? 20 : $params['numPerPage'];
        $keywords = empty($params['wkeywords']) ? "" : $params['wkeywords'];
        //外部数据
        $dao = M(P_INTR . " inr", "p_", DB_INTE_DATA);
        $where = array(
            "plat" => $plat,
            "createTime" => array(
                array("egt", strtotime($startDate)),
                array("lt", strtotime("$endDate +1 day"))
            )
        );
        $totalCount = 0;
        $offset = ($page - 1) * $limit;
        $arrays = $dao->field("*,from_unixtime(createTime,'%b.%d.%Y %H:%i:%s') createTime,ifnull(from_unixtime(updateTime,'%b.%d.%Y %H:%i:%s'),'暂无更新') updateTime,case status when 1 then '正常' when 2 then '冻结' when 3  then '删除'  end stst")->join("LEFT JOIN (select id ids,busname,phone from p_user ) uu on uu.ids=inr.bid")->where($where)->order("createTime DESC")->limit($offset, $limit)->select();
        if ($arrays) {
            $i = 0;
            while ($i < count($arrays)) {
                $arrays[$i]['integral'] = RSAcode($arrays[$i]['integral'], "DE");
                $arrays[$i]['phone'] = hidePhone($arrays[$i]['phone']);
                $i++;
            }
            $totalCount = $dao->where($where)->count();
        }
        $this->assignAll([
            "totalCount" => $totalCount, "plat" => $plat,
            "resArray" => $arrays, "numPerPage" => $limit,
            "page" => $page, "wsDate" => $startDate, "keys" => $keys,
            "weDate" => $endDate, "keywords" => $keywords, "url" => U('Integral/' . $keys)
        ]);
        $this->display();
    }
    /** 
     * @Author: HaiQing.Wu 
     * @Date: 2018-12-19 14:14:03 
     * @Desc: 页面入口  
     */
    public function index()
    {
        $params = I();
        $keys = empty($params['keys']) ? "" : $params['keys'];
        $where = array("isOpen" => 1);
        $dao = M(P_CRE_AU . " cer", "p_", DB_INTE_DATA);
        $lists = $dao->field("DISTINCT plat,companyName")->join("left join (select * from p_usertable ) ut on cer.plat= 
        ut.usertable_ID")->limit(0, 20)->where($where)->order("plat DESC")->select();
        $data = array();
        $i = 0;
        while ($i < count($lists)) {
            $data[$i]['company'] = $lists[$i]['companyName'];
            $data[$i]['plat'] = $lists[$i]['plat'];
            $i++;
        }
        $url = U("Integral/" . $keys);
        $this->assignAll(['resArray' => $data, "keys" => $keys, "url" => $url]);
        $this->display();
    }
}