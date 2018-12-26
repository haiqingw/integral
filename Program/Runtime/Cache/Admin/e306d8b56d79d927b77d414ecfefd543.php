<?php if (!defined('THINK_PATH')) exit();?><div class="pageHeader" style="border:1px #B8D0D6 solid">
    <form id="pagerForm" onsubmit="return divSearch(this, 'jbsxBoxDRAWLISTS<?php echo ($keys); ?>');" action="<?php echo ($url); ?>" method="post">
        <input type="hidden" name="wstatus" value="<?php echo ($wstatus); ?>">
        <input type="hidden" name="wsDate" value="<?php echo ($wsDate); ?>" />
        <input type="hidden" name="weDate" value="<?php echo ($weDate); ?>" />
        <input type="hidden" name="wkeywords" value="<?php echo ($keywords); ?>" />
        <input type="hidden" name="pageNum" value="<?php echo ($page); ?>" />
        <input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>" />
        <input type="hidden" name="plat" value="<?php echo ($plat); ?>" />
        <input type="hidden" name="keys" value="<?php echo ($keys); ?>" />
        <div class="searchBar">
            <table class="searchContent">
                <tr>
                    <td>添加时间：
                        <input type="text" name="wsDate" class="date" size="15" readonly="true" datefmt="yyyy-MM-dd"
                            value="<?php echo ($wsDate); ?>" placeholder="开始时间" style="text-align: center;" /> ~
                        <input type="text" name="weDate" class="date" readonly="true" size="15" datefmt="yyyy-MM-dd"
                            value="<?php echo ($weDate); ?>" placeholder="结束时间" style="text-align: center;" />
                    </td>
                    <td>关键字：
                        <input type="text" name="wkeywords" style="width: 160px; text-align: center;" value="<?php echo ($keywords); ?>"
                            placeholder="用户姓名,联系电话" />
                    </td>
                    <td>
                        <label>交易状态：</label>
                        <select name="wstatus" class="combox">
                            <option value="">请选择</option>
                            <option value="1" <?php if($wstatus == 1): ?>selected="selected"<?php endif; ?>>正常
                            </option>
                            <option value="2" <?php if($wstatus == 2): ?>selected="selected"<?php endif; ?>>作废
                            </option>

                        </select>
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
    /* 统计 */
    .RtotalMain ul {
        overflow: hidden;
    }

    .RtotalMain ul:first-of-type {
        margin-top: 2px;
    }

    .RtotalMain ul li {
        float: left;
        font-family: "黑体";
        margin-left: 20px;
        line-height: 20px;
    }

    .RtotalMain ul li span {
        font-size: 14px;
        font-weight: bold;
    }

    .RtotalMain ul li span em {
        padding: 0 5px;
        color: #f33;
        font-size: 14px;
        font-weight: bold;
        font-style: normal;
    }

    span.inviteNumSpan {
        display: inline-block;
        width: 60px;
    }
    .totalMain{
        overflow: hidden;
        height:27px;
        line-height:27px;
    }
    .totalMain div{
        font-weight:bold;
        padding:0 10px;
        display:inline-block;
        border-right:1px solid #ccc;
    }
    .totalMain div:last-of-type{
        border:none;
    }
    .totalMain div span{
        font-size:14px;
        padding-left:5px;
        color:#f33;
        font-weight:bold;
    }
</style>
<div class="pageContent" style="border-left:1px #B8D0D6 solid;border-right:1px #B8D0D6 solid;width:100%;">
    <div class="panelBar">
        <!-- <ul class="toolBar">
            <li class="line">line</li>
            <li>
                <a class="add" href="<?php echo U('Platdrawmanage/audits');?>?id={SID}" target="dialog" width="500" height="450"
                    mask="true" rel="add" title="审核">
                    <span>审核</span>
                </a>
            </li>
            <li class="line">line</li>
            <li>
                <a class="edit" href="<?php echo U('Pubauditmanage/updateStatus');?>?id={SID}" target="ajaxTodo" title="该提现订单是否要作废，作废信息不能再做任何操作，核对信息请确认"
                    rel="updateStatus">
                    <span>作废</span>
                </a>
            </li>
            <li class="line">line</li>
            <li>
                <a class="delete" href="<?php echo U('Pubauditmanage/ordercashrefun');?>?id={SID}" target="ajaxTodo" rel="ordercashrefun"
                    title="该提现订单是否要退款，一经退款不能在做其他操作，核对信息请确认">
                    <span>退款</span>
                </a>
            </li>
            <li class="line">line</li>
        </ul> -->
        <div class="totalMain">
            <?php if(is_array($drawtj)): $i = 0; $__LIST__ = $drawtj;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><div>
                    <?php echo ($val["rstat"]); ?>：<span><?php echo ($val["sum"]); ?></span>
                </div><?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
    </div>
    <table class="table" width="100%" layoutH="310" rel="jbsxBoxDRAWLISTS<?php echo ($keys); ?>">
        <thead>
            <tr>
                <th width="4%" align="center">序号</th>
                <th width="8%" align="center">商户姓名</th>
                <th width="7%" align="center">商户电话</th>
                <th width="6%" align="center">积分</th>
                <th width="6%" align="center">积分状态</th>
                <th width="9%" align="center">更新时间</th>
                <th width="9%" align="center">添加时间</th>
            </tr>
        </thead>
        <?php if($totalCount > 0): ?><tbody>
                <?php if(is_array($resArray)): $i = 0; $__LIST__ = $resArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr target="cid" rel="<?php echo ($v["id"]); ?>">
                        <td><?php echo ($i); ?></td>
                        <td><?php echo ($v["busname"]); ?></td>
                        <td><?php echo ($v["phone"]); ?></td>
                        <td>
                            <span style="color: #CC0000; font-weight: bold;">
                                <?php echo ($v['integral']); ?></span>
                        </td>
                        <td>
                            <?php if($v['status'] == 1): ?><span style="color: #006633; font-weight: bold;"><?php echo ($v["stst"]); ?></span><?php endif; ?>
                            <?php if($v['status'] == 2): ?><span style="color: #660033; font-weight: bold;"><?php echo ($v["stst"]); ?></span><?php endif; ?>
                        </td>
                        <td><?php echo ($v["updateTime"]); ?></td>
                        <td><?php echo ($v["createTime"]); ?></td>
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
            <select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage:this.value}, 'jbsxBoxDRAWLISTS<?php echo ($keys); ?>')">
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="200">200</option>
            </select>
            <span>条，共<?php echo ($totalCount); ?>条</span>
        </div>
        <!-- <div class="pagination" rel="jbsxBox" totalCount="200" numPerPage="20" pageNumShown="5" currentPage="1"></div> -->
        <div class="pagination" rel="jbsxBoxDRAWLISTS<?php echo ($keys); ?>" totalCount="<?php echo ($totalCount); ?>" numPerPage="<?php echo ($numPerPage); ?>"
            pageNumShown="10" currentPage="<?php echo ($page); ?>"></div>

    </div>
</div>
<script type="text/javascript">
    /**
     * 提现卡 信息
     */
    function drawDankDetail(obj, id) {
        $.ajax({
            url: '<?php echo U("ChangeBalance/ajaxBankDetail");?>',
            type: "POST",
            dataType: 'Json',
            data: {
                "bids": id
            },
            success: function (ret) {
                if (ret.status == 1) {
                    var $d = ret.data;
                    layer.tips($d.name + '　---　' + $d.card_number + '　---　' +
                        $d.bank_name, $(obj), {
                            tips: [2, 'green'],
                            area: ['380px', '23px'],
                            offset: "", //右下角弹出
                            time: 3000
                        });
                } else {
                    alertMsg.warn(ret.msg);
                }
            }
        });
    }

    function tranDetail(o) {
        var url = "<?php echo U('ChangeBalance/detail');?>";
        $.pdialog.open(url, "dialog", '交易明细', {
            mask: true,
            width: 1400,
            height: 400,
            drawable: false,
            maxable: false,
            minable: false,
            resizable: false
        });
    }
</script>