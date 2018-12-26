<?php if (!defined('THINK_PATH')) exit();?><div class="pageHeader" style="border:1px #B8D0D6 solid">
    <form id="pagerForm" onsubmit="return divSearch(this, 'jbsxBoxBUSYSLISTCD');" action="<?php echo U('Business/lists');?>"
        method="post">
        <input type="hidden" name="pageNum" value="<?php echo ($page); ?>" />
        <input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>" />
        <input type="hidden" name="plat" value="<?php echo ($plat); ?>" />
        <input type="hidden" name="keywords" value="<?php echo ($params['keywords']); ?>" />
        <input type="hidden" name="startDate" value="<?php echo ($startDate); ?>" />
        <input type="hidden" name="endDate" value="<?php echo ($endDate); ?>" />
        <input type="hidden" name="status" value="<?php echo ($params['status']); ?>">
        <input type="hidden" name="level" value="<?php echo ($level); ?>">
        <input type="hidden" name="al" value="<?php echo ($al); ?>">
        <input type="hidden" name="parent" value="<?php echo ($params['parent']); ?>">
        <div class="searchBar">
            <table class="searchContent">
                <tr>
                    <td align="left"></td>
                    <td>添加时间：
                        <input type="text" name="startDate" style="width: 80px;" class="date" readonly="true" datefmt="yyyy-MM-dd"
                            value="<?php echo ($startDate); ?>" />
                        ~
                        <input type="text" name="endDate" style="width: 80px;" class="date" readonly="true" datefmt="yyyy-MM-dd"
                            value="<?php echo ($endDate); ?>" />
                    </td>
                    <td align="left">商户状态：</td>
                    <td>
                        <select name="status" class="combox">
                            <option value="">请选择</option>
                            <option value="1" <?php if($params['status'] == '1'): ?>selected<?php endif; ?>>正常
                            </option>
                            <option value="2" <?php if($params['status'] == '2'): ?>selected<?php endif; ?>>冻结
                            </option>
                            <option value="3" <?php if($params['status'] == '3'): ?>selected<?php endif; ?>>已删除
                            </option>
                        </select>
                    </td>
                    <td align="left">商户级别：</td>
                    <td align="right">关键词：
                        <input type="text" value="<?php echo ($params['keywords']); ?>" id="keywords" name="keywords" autocomplete="off"
                            placeholder="商户名称,联系电话" style="width: 160px;" />
                    <td align="right">上级：
                        <input type="text" value="<?php echo ($params['parent']); ?>" id="parent" name="parent" autocomplete="off"
                            placeholder="商户名称,联系电话" style="width: 160px;" />
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
<div class="pageContent" style="border-left:1px #B8D0D6 solid;border-right:1px #B8D0D6 solid;width:100%;">
    <div class="panelBar">
        <ul class="toolBar">
            <li class="line">line</li>
            <li>
                <a class="add" href="<?php echo U('Business/add');?>?level=<?php echo ($level); ?>" target="dialog" width="600" height="500" mask="true" rel="add"
                    title="商户添加">
                    <span>新增商户</span>
                </a>
            </li>
            <li class="line">line</li>
            <li>
                <a class="edit" href="<?php echo U('Business/modify');?>?bid={bid}" target="dialog" width="600" height="500" mask="true"
                    rel="modify" title="商户修改">
                    <span>修改基本信息</span>
                </a>
            </li>
            <li class="line">line</li>
            <li>
                <a class="edit" href="<?php echo U('Business/checkuser');?>?bid={bid}" target="dialog" mask="true" rel="checkuser"
                    width="320" height='220'>
                    <span>操作（修改商户状态）</span>
                </a>
            </li>
            <li class="line">line</li>
            <li>
                <a class="edit" href="<?php echo U('Business/modifyve');?>?bid={bid}" target="dialog" mask="true" rel="checkuser"
                    width="600" height="300">
                    <span>修改实名认证</span>
                </a>
            </li>
            <li class="line">line</li>
            <li>
                <a class="edit" href="<?php echo U('Business/shipAddress');?>?bid={bid}" target="dialog" mask="true" rel="checkuser"
                    width="900" height='560'>
                    <span>查看收货地址</span>
                </a>
            </li>
            <li class="line">line</li>
            <li>
                <a class="edit" href="<?php echo U('Business/modityLevel');?>?bid={bid}" target="dialog" mask="true" rel="modityLevel"
                    width="400" height='220'>
                    <span>修改用户级别</span>
                </a>
            </li>
            <li class="line">line</li>
            <li>
                <a class="edit" href="<?php echo U('Business/updateIsDraw');?>?bid={bid}" target="dialog" mask="true" rel="modityLevel"
                    width="320" height='220'>
                    <span>修改商户提现状态</span>
                </a>
            </li>
            <li class="line">line</li>
            <li>
                <a class="edit" href="<?php echo U('Business/replaces');?>?bid={bid}" target="dialog" mask="true" rel="modityLevel"
                    width="600" height="500">
                    <span>更换商户上级</span>
                </a>
            </li>
            <li class="line">line</li>
        </ul>
    </div>
    <table class="table" width="100%" layoutH="310" rel="jbsxBoxBUSYSLISTCD">
        <thead>
            <tr>
                <th align="center" width="3%">序号</th>
                <th align="center" width="4%">头像</th>
                <th align="center" width="7%">商户名称</th>
                <th align="center" width="7%">商户级别</th>
                <th width="10%" align="center">实名认证</th>
                <th align="center" width="6%">联系电话</th>
                <th align="center" width="6%">银行卡</th>
                <th align="center" width="8%">注册时间</th>
                <th align="center" width="4%">商户状态</th>
                <th align="center" width="4%">提现状态</th>
                <th align="center" width="7%">上级</th>
                <th align="center" width="7%">上级级别</th>
                <th align="center" width="7%">邀请码</th>
                <th align="center" width="10%">操作</th>
            </tr>
        </thead>
        <?php if($totalCount > 0): ?><tbody>
                <?php if(is_array($resArray)): $i = 0; $__LIST__ = $resArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr target="bid" rel="<?php echo ($v["id"]); ?>">
                        <td><?php echo ($i); ?></td>
                        <td data-id="busname<?php echo ($v["id"]); ?>">
                            <?php if($v['ImagePath'] != ''): ?><img src="<?php echo ($v["ImagePath"]); ?>"  style="line-height:23px;border-radius: 50%;width:20px;height:20px;" />
                                <?php else: ?>
                                <img src="/Public/images/default_head.png"  style="width:20px;height:20px;" /><?php endif; ?>
                        </td>
                        <td data-id="busname<?php echo ($v["id"]); ?>"><?php echo ($v["busname"]); ?></td>
                        <td><?php echo ($v["level"]); ?></td>
                        <td>
                            <!-- 实名认证状态 -->
                            <?php if($v['verified']['status'] == 1): ?><span style="color: #930114; font-weight: bold; line-height: 22px;"><?php echo ($v['verified']['msg']); ?></span>
                                <?php else: ?>
                                <span style="color: #6600CC; font-weight: bold;"><?php echo ($v['verified']['msg']); ?>
                                    <a href="#" class="bankCard" onclick="tipThis(this, '<?php echo ($v['verified']['name']); ?>', '<?php echo ($v['verified']['idCard']); ?>',2 , 2)">
                                        <span style="color: #666600; font-weight: bold; line-height: 22px;">（查看）</span>
                                    </a>
                                </span><?php endif; ?>
                        </td>
                        <td data-id="phone<?php echo ($v["id"]); ?>"><?php echo ($v["phone"]); ?></td>
                        <td>
                            <?php if($v['bindCard']['status'] == 0): ?><span style="color: #930114; font-weight: bold; line-height: 22px;">未绑定</span>
                                <?php else: ?>
                                <span style="color: #6600CC; font-weight: bold; line-height: 22px;">已绑定
                                    <a href="#" class="bankCard" onclick="tipThis(this, '持卡人：<?php echo ($v['bindCard']['name']); ?>', '银行卡号：<?php echo ($v['bindCard']['cardNum']); ?>' ,'总行名称：<?php echo ($v['bindCard']['bank']); ?>', 1)">
                                        <span style="color: #666600; font-weight: bold;">（查看）</span>
                                    </a>
                                </span><?php endif; ?>
                        </td>
                        <td><?php echo ($v["regisTime"]); ?></td>
                        <td>
                            <?php if($v['status'] == 1): ?><span style="color: #006600; font-weight: bold;"><?php echo ($v["ds"]); ?></span><?php endif; ?>
                            <?php if($v['status'] == 2): ?><span style="color: #990000; font-weight: bold;"><?php echo ($v["ds"]); ?></span><?php endif; ?>
                            <?php if($v['status'] == 3): ?><span style="color: #000000; font-weight: bold;"><?php echo ($v["ds"]); ?></span><?php endif; ?>
                        </td>
                        <td>
                            <?php if($v['isDraw'] == 1): ?><span style="color: #006600; font-weight: bold;"><?php echo ($v["isDraws"]); ?></span><?php endif; ?>
                            <?php if($v['isDraw'] == 2): ?><span style="color: #990000; font-weight: bold;"><?php echo ($v["isDraws"]); ?></span><?php endif; ?>
                        </td>
                        <td><span style="color: #000000; font-weight: bold;"><?php echo ($v["sj"]); ?></span></td>
                        <td><span style="color: #DB7093; font-weight: bold;"><?php echo ($v["sjlevel"]); ?></span></td>
                        <td><?php echo ($v["code"]); ?></td>
                        <td>
                            <a href="<?php echo U('Business/verified');?>?bid=<?php echo ($v["id"]); ?>" target="dialog" width="600" height="300"
                                mask="true" rel="modify" title="实名认证">
                                <span style="color: #f60; font-weight: bold;">实名认证</span>
                            </a>
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
            <select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage:this.value}, 'jbsxBoxBUSYSLISTCD')">
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="200">200</option>
            </select>
            <span>条，共<?php echo ($totalCount); ?>条</span>
        </div>
        <!-- <div class="pagination" rel="jbsxBox" totalCount="200" numPerPage="20" pageNumShown="5" currentPage="1"></div> -->
        <div class="pagination" rel="jbsxBoxBUSYSLISTCD" totalCount="<?php echo ($totalCount); ?>" numPerPage="<?php echo ($numPerPage); ?>"
            pageNumShown="10" currentPage="<?php echo ($page); ?>"></div>

    </div>
</div>
<script type="text/javascript">
    /**
     *   查看信息
     */
    function tipThis(obj, n, i, b, t) {
        var $str = '（' + n + '）（' + i + '）';
        if (t == 1) {
            $str = '（' + n + '）（' + i + '）（' + b + '）';
        }
        layer.tips($str, $(obj), {
            tips: [2, 'green'],
            area: ['380px', '23px'],
            offset: "", //右下角弹出
            time: 3000
        });
    }
    /**
     * 提现卡 信息
     */
    function drawDankDetail(obj, id) {
        $.ajax({
            url: '<?php echo U("Business/ajaxBankDetail");?>',
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

    function changeClerk(id, type) {
        alertMsg.confirm("是否修改业务员状态", {
            okCall: function () {
                ajaxTodo("<?php echo U('changeClerk');?>?id=" + id + "&type=" + type);
            }
        });
    }
</script>