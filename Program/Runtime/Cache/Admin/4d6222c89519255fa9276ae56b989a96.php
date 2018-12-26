<?php if (!defined('THINK_PATH')) exit();?><div class="pageHeader" style="border:1px #B8D0D6 solid">
    <form id="pagerForm" onsubmit="return divSearch(this, 'jbsxBoxCOMMONDITYLIST');" action="<?php echo U('Commodity/lists');?>"
        method="post">
        <input type="hidden" name="pageNum" value="<?php echo ($page); ?>" />
        <input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>" />
        <input type="hidden" name="plat" value="<?php echo ($plat); ?>" />
        <input type="hidden" name="keywords" value="<?php echo ($params['keywords']); ?>" />
        <input type="hidden" name="startDate" value="<?php echo ($startDate); ?>" />
        <input type="hidden" name="endDate" value="<?php echo ($endDate); ?>" />
        <input type="hidden" name="status" value="<?php echo ($status); ?>">
        <div class="searchBar">
            <table class="searchContent">
                <tr>
                    <td align="left">商品状态：</td>
                    <td>
                        <select name="status" class="combox">
                            <option value="">请选择</option>
                            <option value="1" <?php if($status == '1'): ?>selected<?php endif; ?>>未上架
                            </option>
                            <option value="2" <?php if($status == '2'): ?>selected<?php endif; ?>>已上架
                            </option>
                        </select>
                    </td>
                    <td align="right">关键词：
                        <input type="text" value="<?php echo ($keywords); ?>" id="keywords" name="keywords" autocomplete="off"
                            placeholder="商品名称" style="width: 160px;" />
                    </td>
                    <td align="left">
                        <div class="buttonActive">
                            <div class="buttonContent">
                                <button type="submit">检索</button>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </form>
</div>
<style>
    .spansa span {
        border-right: 1px #6d9dd7 solid;
        padding: 0 8px;
        color: #000000;
        font-weight: bold;
    }

    .spansa span:last-of-type {
        border: none;
    }
</style>
<div class="pageContent" style="border-left:1px #B8D0D6 solid;border-right:1px #B8D0D6 solid;width:100%;">
    <div class="panelBar">


    </div>
    <table class="table" width="100%" layoutH="310" rel="jbsxBoxCOMMONDITYLIST">
        <thead>
            <tr>
                <th align="center" width="3%">序号</th>
                <th align="center" width="7%">名称</th>
                <th align="center" width="7%">标题</th>
                <th align="center" width="6%">详情</th>
                <th align="center" width="6%">类别</th>
                <th align="center" width="6%">原价</th>
                <th align="center" width="6%">现价</th>
                <th align="center" width="6%">押金</th>
                <th align="center" width="6%">费率</th>
                <th align="center" width="6%">库存</th>
                <th align="center" width="5%">已售</th>
                <th align="center" width="6%">图片展示</th>
                <th align="center" width="6%">视频展示</th>
                <th align="center" width="4%">商品状态</th>
                <th align="center" width="7%">推荐状态</th>
                <th align="center" width="7%">添加时间</th>
            </tr>
        </thead>
        <?php if($totalCount > 0): ?><tbody>
                <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr target="cid" rel="<?php echo ($v["id"]); ?>">
                        <td><?php echo ($i); ?></td>
                        <td><?php echo ($v["commodityName"]); ?></td>
                        <td><?php echo (msubstr($v["commodityTitle"],0,10)); ?>...</td>
                        <td><?php echo (msubstr(fhtml($v["commodityDetail"]),0,10)); ?>...</td>
                        <td><?php echo ($v["categoryName"]); ?></td>
                        <td><?php echo ($v["originalPrice"]); ?></td>
                        <td><?php echo ($v["nowPrice"]); ?></td>
                        <td><?php echo ($v["deposit"]); ?></td>
                        <td><?php echo ($v["rate"]); ?></td>
                        <td><?php echo ($v["stock"]); ?></td>
                        <td><?php echo ($v["sold"]); ?></td>
                        <td>
                            <a href="<?php echo U('ViewBox');?>?ids=<?php echo ($v["imgPath"]); ?>" target="dialog" mask="true" rel="ViewBox" width="300"
                                height="600">图片展示</a>
                        </td>
                        <td>
                            <?php if(empty($v['videoPath'])): ?>暂无
                                <?php else: echo ($v["videoPath"]); endif; ?>
                        </td>
                        <td>
                            <?php if($v["status"] == 1): ?><font>未上架</font>
                                <?php elseif($v["status"] == 2): ?>
                                <font>已上架</font><?php endif; ?>
                            </switch>
                        </td>
                        <td>
                            <?php if($v["type"] == 1): ?><font>正常</font>
                                <?php elseif($v["type"] == 2): ?>
                                <font>推荐</font>
                                <?php elseif($v["type"] == 3): ?>
                                <font>爆款</font><?php endif; ?>
                        </td>
                        <td><?php echo (dateformat($v["addtime"])); ?></td>
                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
            <?php else: ?>
            <tbody>
                <tr>
                    <td align="center" colspan="13" style="color: red;">抱歉，没有找到符合的记录！</td>
                </tr>
            </tbody><?php endif; ?>
    </table>
    <div class="panelBar">
        <div class="pages">
            <span>显示</span>
            <select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage:this.value}, 'jbsxBoxCOMMONDITYLIST')">
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="200">200</option>
            </select>
            <span>条，共<?php echo ($totalCount); ?>条</span>
        </div>
        <!-- <div class="pagination" rel="jbsxBox" totalCount="200" numPerPage="20" pageNumShown="5" currentPage="1"></div> -->
        <div class="pagination" rel="jbsxBoxCOMMONDITYLIST" totalCount="<?php echo ($totalCount); ?>" numPerPage="<?php echo ($numPerPage); ?>"
            pageNumShown="10" currentPage="<?php echo ($page); ?>"></div>

    </div>
</div>



 <!-- <a href="<?php echo U('Commodity/lists');?>?plat=<?php echo ($val["plat"]); ?>" target="ajax" rel="jbsxBoxCOMMONDITYLIST"><?php echo ($val["company"]); ?></a> -->