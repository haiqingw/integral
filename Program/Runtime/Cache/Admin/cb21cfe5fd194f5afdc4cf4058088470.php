<?php if (!defined('THINK_PATH')) exit();?><style type="text/css">
    .dialogBackground {
        background-color: #000033;
    }
</style>
<form id="pagerForm" method="post" action="<?php echo U('Accountbalance/drawlists');?>">
    <input type="hidden" name="plat" value="<?php echo ($plat); ?>" />
    <input type="hidden" name="startDate" value="<?php echo ($startDate); ?>" />
    <input type="hidden" name="endDate" value="<?php echo ($endDate); ?>" />
    <input type="hidden" name="keywords" value="<?php echo ($keywords); ?>" />
    <input type="hidden" name="pageNum" value="<?php echo ($page); ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>" />
    <input type="hidden" name="cla" value="<?php echo ($cla); ?>" />
</form>
<!-- /查找直属人结束 -->
<div class="pageHeader">
    <form onsubmit="return dwzSearch(this, 'dialog');" action="<?php echo U('Accountbalance/drawlists');?>" method="post" id="BusinessList">
        <div class="searchBar">
            <table class="searchContent">
                <tr>
                    <td align="left">支付时间：</td>
                    <td>
                        <input type="text" name="startDate" class="date" size="15" readonly="true" datefmt="yyyy-MM-dd"
                            value="<?php echo ($startDate); ?>" placeholder="开始时间" style="text-align: center;" /> ~
                        <input type="text" name="endDate" class="date" readonly="true" size="15" datefmt="yyyy-MM-dd"
                            value="<?php echo ($endDate); ?>" placeholder="结束时间" style="text-align: center;" />
                    </td>
                    <td align="left">交易类型</td>
                    <td>
                        <select class="combox" name="cla">
                            <option value="">请选择</option>
                            <?php if(is_array($classlist)): foreach($classlist as $key=>$val): ?><option value="<?php echo ($val["englishname"]); ?>" <?php if($val['englishname'] == $cla): ?>selected="selected"<?php endif; ?>><?php echo ($val["classname"]); ?></option><?php endforeach; endif; ?>
                        </select>
                    </td>
                    <td>
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

<div class="pageContent">
    <div class="panelBar">

    </div>
    <table class="table" width="100%" layoutH="141">
        <thead>
            <tr>
                <th width="5%" align="center">序号</th>
                <th width="6%" align="center">订单号</th>
                <th width="6%" align="center">商户姓名</th>
                <th width="6%" align="center">商户电话</th>
                <th width="6%" align="center">提现金额</th>
                <th width="12%" align="center">提现费用</th>
                <th width="6%" align="center">代付金额</th>
                <th width="6%" align="center">代付手续费</th>
                <th width="6%" align="center">代付状态</th>
                <th width="6%" align="center">交易类型</th>
                <th width="8%" align="center">提现时间</th>
            </tr>
        </thead>
        <?php if($totalCount > 0): ?><tbody>
                <?php if(is_array($reArray)): $i = 0; $__LIST__ = $reArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr target="cid" rel="<?php echo ($v["id"]); ?>,<?php echo ($v["bid"]); ?>">
                        <!-- <td><input name="busReview" id="busReview" value="<?php echo ($v["id"]); ?>"
                        type="checkbox"></td> -->
                        <td><?php echo ($i); ?></td>
                        <td><?php echo ($v["ordernum"]); ?></td>
                        <td><?php echo ($v["busname"]); ?></td>
                        <td><?php echo ($v["phone"]); ?></td>
                        <td><span style="color: #CC0000; font-weight: bold;"><?php echo ($v['changeAmount']); ?></span></td>
                        <td><?php echo ($v["cfremark"]); ?></td>
                        <td><span style="color: #CC0000; font-weight: bold;"><?php echo ($v['money']); ?></span></td>
                        <td><span style="color: #CC0000; font-weight: bold;"><?php echo ($v['fee']); ?></span></td>
                        <td>
                            <?php if($v['reviewStatus'] == 4): ?><span style="color: #FF6600; font-weight: bold;">未通过</span><?php endif; ?>
                            <?php if($v['reviewStatus'] == 2): ?><span style="color: #660033; font-weight: bold;">未审核</span><?php endif; ?>
                        </td>
                        <td><span style="color:#000000;font-weight:bold;"><?php echo ($v["payType"]); ?></span></td>
                        <td><?php echo ($v["createTime"]); ?></td>
                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
            <?php else: ?>
            <tbody>
                <tr>
                    <td align="center" colspan="14" style="color: red;">抱歉，没有找到符合的记录！</td>
                </tr>
            </tbody><?php endif; ?>
    </table>
    <div class="panelBar tongji">
        <div class="pages tongji_1">
            <span>用户总数：共<?php echo ($totalCount); ?></span>
        </div>
    </div>
    <style>
        .layui-layer-content {
            margin-top: -11px;
        }

        .layui-layer-content img {
            float: left;
            margin-top: 2px;
        }

        .layui-layer.layui-anim.layui-layer-tips,
        .layui-layer-content {
            height: auto !important;
        }
    </style>
    <style>
        .tongji {
            padding-left: 40%;
        }

        .tongji .tongji_1 {
            padding: 0 0 0 20px;
            text-align: center;
        }

        .tongji .tongji_1 span {
            color: red;
        }
    </style>
    <div class="panelBar">
        <div class="pages">
            <!-- <span>显示</span> -->
            <!-- <select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage: this.value})">
                <option value="20" <?php if($numPerPage == 20): ?>selected="selected"<?php endif; ?>>20
                </option>
                <option value="50" <?php if($numPerPage == 50): ?>selected="selected"<?php endif; ?>>50
                </option>
                <option value="100" <?php if($numPerPage == 100): ?>selected="selected"<?php endif; ?>>100
                </option>
                <option value="200" <?php if($numPerPage == 200): ?>selected="selected"<?php endif; ?>>200
                </option>
            </select> -->
            <span>共<?php echo ($totalCount); ?>条</span>
        </div>
        <div class="pagination" targetType="dialog" totalCount="<?php echo ($totalCount); ?>" numPerPage="<?php echo ($numPerPage); ?>" pageNumShown="10"
            currentPage="<?php echo ($page); ?>"></div>
    </div>
</div>