<?php if (!defined('THINK_PATH')) exit();?><div class="pageHeader" style="border:1px #B8D0D6 solid">
    <form id="pagerForm" onsubmit="return divSearch(this, 'jbsxBoxSYSZXLB');" action="<?php echo U('POSstatistics/translists');?>"
        method="post">
        <input type="hidden" name="keywords" value="<?php echo ($keywords); ?>" />
        <input type="hidden" name="pageNum" value="<?php echo ($page); ?>" />
        <input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>" />
        <input type="hidden" name="sdate" value="<?php echo ($sdate); ?>" />
        <input type="hidden" name="edate" value="<?php echo ($edate); ?>" />
        <input type="hidden" name="plat" value="<?php echo ($plat); ?>" />
        <input type="hidden" name="productTypes" value="<?php echo ($productTypes); ?>" />
        <input type="hidden" name="process" value="<?php echo ($process); ?>" />
        <div class="searchBar">
            <table class="searchContent">
                <tr>
                    <td align="left">交易日期：
                        <input type="text" name="sdate" style="width: 80px;" class="date" readonly="true" datefmt="yyyy-MM-dd"
                            value="<?php echo ($sdate); ?>" /> ~ <input type="text" name="edate" style="width: 80px;" class="date"
                            readonly="true" datefmt="yyyy-MM-dd" value="<?php echo ($edate); ?>" /></td>
                    <td align="right">关键词：<input type="text" value="<?php echo ($keywords); ?>" id="keywords" name="keywords"
                            autocomplete="off" placeholder="终端号,交易流水号,商户名称,交易金额" style="width: 160px;" />
                    </td>
                    <td align="left">处理状态：</td>
                    <td><select name="process" class="combox">
                            <option value="">全部</option>
                            <option value="1" <?php if($process == '1'): ?>selected<?php endif; ?>>未处理
                            </option>
                            <option value="2" <?php if($process == '2'): ?>selected<?php endif; ?>>已处理
                            </option>
                        </select></td>
                    <td align="left">
                        <div class="buttonActive">
                            <div class="buttonContent">
                                <button type="submit">检索</button>
                            </div>
                        </div>
                    </td>
                    <td align="right">
                        <span style="color: #000080; font-weight: bold;float:right;"><?php echo ($title); ?></span>
                    </td>
                    <td align="right">
                        <span style="color: #990033; font-weight: bold;float:right;">(<?php echo ($productName); ?>)</span>
                    </td>
                </tr>
            </table>
        </div>
    </form>
</div>
<style>
    /* 统计 */
    .RtotalMain ul {
        overflow: hidden;
    }

    .RtotalMain ul li {
        float: left;
        font-family: "黑体";
        margin-left: 20px;
    }

    .RtotalMain ul li span {
        font-size: 15px;
        font-weight: bold;
    }

    .RtotalMain ul li span em {
        padding: 0 5px;
        color: #f33;
        font-size: 18px;
        font-weight: bold;
        font-style: normal;
    }
</style>
<div class="pageContent" style="border-left:1px #B8D0D6 solid;border-right:1px #B8D0D6 solid;width:100%;">
    <div class="panelBar">
        <?php if($totalTrade != ''): ?><div class="RtotalMain">
                <ul>
                    <?php if(is_array($totalTrade)): $i = 0; $__LIST__ = $totalTrade;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><li>
                            <span>
                                <?php echo ($val["processSt"]); ?>
                                共：<em>&yen;&nbsp;<?php echo ($val["tp_sum"]); ?></em>（元）
                            </span>
                        </li><?php endforeach; endif; else: echo "" ;endif; ?>
                </ul>
            </div><?php endif; ?>
    </div>
    <table class="table" width="99%" layoutH="186" rel="jbsxBoxSYSZXLB">
        <thead>
            <tr>
                <th align="center">序号</th>
                <th align="center">订单号</th>
                <th align="center">商户号</th>
                <th align="center">终端号</th>
                <th align="center">交易日期</th>
                <th align="center">交易金额</th>
                <th align="center">手续费</th>
                <th align="center">到账金额</th>
                <th align="center">交易类型</th>
                <th align="center">交易状态</th>
                <th align="center">卡号</th>
                <th align="center">卡类型</th>
                <th align="center">绑定时间</th>
                <th align="center">返现状态</th>
                <th align="center">备注</th>
            </tr>
        </thead>
        <?php if($totalCount > 0): ?><tbody>
                <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr target="bid" rel="<?php echo ($v["bid"]); ?>">
                        <td align="center"><?php echo ($i); ?></td>
                        <td align="center"><?php echo ($v["tradeOrderNo"]); ?></td>
                        <td align="center"><?php echo ($v["merchantNo"]); ?></td>
                        <td align="center"><?php echo ($v["terminalNo"]); ?></td>
                        <td align="center"><?php echo (dateformat($v["tradeTime"],2)); ?></td>
                        <td align="center"><?php echo ($v["tradeAmt"]); ?></td>
                        <td align="center"><?php echo ($v["poundage"]); ?></td>
                        <td align="center"><?php echo ($v["arrivalAmt"]); ?></td>
                        <td align="center"><?php echo ($v["tradeStatus"]); ?></td>
                        <td align="center"><?php echo ($v["tradeType"]); ?></td>
                        <td align="center"><?php echo ($v["cardNo"]); ?></td>
                        <td align="center"><?php echo ($v["cardType"]); ?></td>
                        <td align="center"><?php echo (dateformat($v["bindTime"],2)); ?></td>
                        <td align="center">
                            <?php if($v["processStatus"] == 1): ?><font color="#E54646"><b>未处理</b></font>
                                <?php else: ?>
                                <font color="#0DAAE5"><b>已处理</b></font><?php endif; ?>
                        </td>
                        <td align="center"><?php echo ($v["remark"]); ?>
                        </td>
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
            <select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage:this.value}, 'jbsxBoxSYSZXLB')">
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="200">200</option>
            </select>
            <span>条，共<?php echo ($totalCount); ?>条</span>
        </div>
        <!-- <div class="pagination" rel="jbsxBox" totalCount="200" numPerPage="20" pageNumShown="5" currentPage="1"></div> -->
        <div class="pagination" rel="jbsxBoxSYSZXLB" totalCount="<?php echo ($totalCount); ?>" numPerPage="<?php echo ($numPerPage); ?>" pageNumShown="10"
            currentPage="<?php echo ($page); ?>"></div>

    </div>
</div>