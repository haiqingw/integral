<?php if (!defined('THINK_PATH')) exit();?><div class="pageHeader" style="border:1px #B8D0D6 solid">
    <form id="pagerForm" onsubmit="return divSearch(this, 'jbsxBoxSYSTERM');" action="<?php echo U('POSstatistics/terminallist');?>"
        method="post">
        <input type="hidden" name="keywords" value="<?php echo ($keywords); ?>" />
        <input type="hidden" name="pageNum" value="<?php echo ($page); ?>" /> 
        <input type="hidden" name="startDate" value="<?php echo ($startDate); ?>" /> 
        <input type="hidden" name="endDate" value="<?php echo ($endDate); ?>" /> 
        <input type="hidden" name="cid" value="<?php echo ($cid); ?>" /> 
        <input type="hidden" name="useStatus" value="<?php echo ($useStatus); ?>" /> 
        <input type="hidden" name="allotStatus" value="<?php echo ($allotStatus); ?>" /> 
        <input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>" /> 
        <input type="hidden" name="asc" value="<?php echo ($asc); ?>" /> 
        <input type="hidden" name="orderField" value="<?php echo ($orderField); ?>" /> 
        <input type="hidden" name="obj_name" value="<?php echo ($obj_name); ?>" /> 
        <input type="hidden" name="obj_id" value="<?php echo ($obj_id); ?>" /> 
        <input type="hidden" name="pr" id="pr" value="<?php echo ($productID); ?>" />
        <input type="hidden" name="plat" value="<?php echo ($plat); ?>" />
        <input type="hidden" name="proid" value="<?php echo ($proid); ?>" />
        <div class="searchBar">
            <table class="searchContent">
                <tr>
                    <!-- <td align="left">入库产品：</td>
                    <td align="right"><select class="combox" name="cid" id="cid">
                            <option value="">请选择</option>
                            <?php if(is_array($product)): $i = 0; $__LIST__ = $product;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </select></td> -->
                    <td><label>调拨状态：</label><select name="allotStatus" id="allotStatus" class="combox">
                            <option value="">请选择</option>
                            <option value="1"<?php if($allotStatus == 1): ?>selected="selected"<?php endif; ?>>未调拨
                            </option>
                            <option value="2"<?php if($allotStatus == 2): ?>selected="selected"<?php endif; ?>>已调拨
                            </option>
                    </select></td>
                    <td><label>使用状态：</label><select name="useStatus" id="useStatus" class="combox">
                            <option value="">请选择</option>
                            <option value="1"<?php if($useStatus == 1): ?>selected="selected"<?php endif; ?>>未使用
                            </option>
                            <option value="2"<?php if($useStatus == 2): ?>selected="selected"<?php endif; ?>>已使用
                            </option>
                    </select></td>
                    <td align="right">所属服务商：<input type="text" value="<?php echo ($obj_name); ?>" id="belongName" name="obj.name" lookupPk="name" autocomplete="off" placeholder="点击选择" onclick="openSearchBack('#searchBack')" style="width: 100px; text-align: center; cursor: pointer" readonly /> <input name="obj.id" id="belongID" value="<?php echo ($obj_id); ?>" type="hidden" /> <a href="<?php echo U('MachineManage/searchBack');?>" lookupGroup="obj" id="searchBack" width="600" height="400" rel="modify" title="所属服务商" style="display: none;"></a> <a href="javascript:clearBelong()" style="color: skyblue">clear</a>
                    </td>

                    <td align="right">关键词：<input type="text" value="<?php echo ($keywords); ?>" id="keywords" name="keywords" autocomplete="off" placeholder="终端号,批次号" style="width: 160px;" />
                    </td>
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
<div class="pageContent" style="border-left:1px #B8D0D6 solid;border-right:1px #B8D0D6 solid;width:100%;">

    <table class="table" width="99%" layoutH="166" rel="jbsxBoxSYSTERM">
        <thead>
            <tr>
                <th align="center">序号</th>
                <th align="center">终端号</th>
                <th align="center">返现模板</th>
                <th align="center">批次号</th>
                <th align="center">调拨状态</th>
                <th align="center">所属服务商</th>
                <th align="center">调拨时间</th>
                <th align="center">使用状态</th>
                <th align="center">使用者</th>
                <th align="center">使用时间</th>
                <th align="center">激活状态</th>
                <th align="center">入库时间</th>
                <th align="center">拨码位置</th>
                <th align="center">拨码人</th>
                <th align="center">机具状态</th>
            </tr>
        </thead>
        <?php if($totalCount > 0): ?><tbody>
                <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr target="bid" rel="<?php echo ($v["bid"]); ?>">
                        <td align="center"><?php echo ($i+$serial); ?></td>
                        <td align="center"><?php echo ($v["terminalNo"]); ?></td>
                        <td align="center">模板ID:<?php echo ($v["cbrID"]); ?></td>
                        <td align="center"><?php echo ($v["batchNo"]); ?></td>
                        <td align="center"><?php if($v['allotStatus'] == 1): ?>未调拨<?php else: ?>已调拨<?php endif; ?></td>
                        <td align="center"><?php if($v['allotStatus'] == 1): ?>未调拨<?php else: echo ($v["belongName"]); endif; ?></td>
                        <td align="center"><?php if($v['allotStatus'] == 1): ?>未调拨<?php else: echo (dateformat($v["allotTime"],2)); endif; ?></td>
                        <td align="center"><?php if($v['useStatus'] == 1): ?>未使用<?php else: ?>已使用<?php endif; ?></td>
                        <td align="center"><?php if($v['useStatus'] == 1): ?>未使用<?php else: echo ($v["useName"]); endif; ?></td>
                        <td align="center"><?php if($v['useStatus'] == 1): ?>未使用<?php else: echo (dateformat($v["useTime"],2)); endif; ?></td>
                        <td style="font-weight: bold">
                            <?php if($v['isActive'] == 3): ?><span style="color:#4B0082;"><?php endif; ?>
                            <?php if($v['isActive'] == 1): ?><span style="color:#DC143C;"><?php endif; ?>
                            <?php if($v['isActive'] == 2): ?><span style="color:#006600;"><?php endif; ?>
                            <?php echo ($v["isAct"]); ?></span></td>
                        <td align="center"><?php echo (dateformat($v["createTime"],2)); ?></td>
                        <td align="center"><?php if($v['allot'] == 2): ?><spanv style="color:#006600;font-weight:bold;"><?php echo ($v["allotes"]); ?></span>（<a href="<?php echo U('MachineManage/dialdetail');?>?id=<?php echo ($v["id"]); ?>&productID=<?php echo ($v["productID"]); ?>" target="dialog" rel="MachineManage/dialdetail" title="拨码记录" width="1024" height='768' mask="true"><span style="color: #993300; font-weight: bold;">拨码记录</span></a>）<?php else: ?> <span style="color: #006699; font-weight: bold;"><?php echo ($v["allotes"]); ?></span><?php endif; ?></td>
                        <td align="center"><?php if($v['allot'] == 2): ?><span style="color: #006600; font-weight: bold;"><?php echo ($v["codeMan"]); ?></span> <?php else: ?> <span style="color: #006699; font-weight: bold;"><?php echo ($v["codeMan"]); ?></span><?php endif; ?></td>
                        <td align="center"><?php echo ($v["mstatus"]); ?></td>
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
            <select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage:this.value}, 'jbsxBoxSYSTERM')">
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="200">200</option>
            </select>
            <span>条，共<?php echo ($totalCount); ?>条</span>
        </div>
        <!-- <div class="pagination" rel="jbsxBox" totalCount="200" numPerPage="20" pageNumShown="5" currentPage="1"></div> -->
        <div class="pagination" rel="jbsxBoxSYSTERM" totalCount="<?php echo ($totalCount); ?>" numPerPage="<?php echo ($numPerPage); ?>" pageNumShown="10"
            currentPage="<?php echo ($page); ?>"></div>

    </div>
</div>
<script>
	//验证是否为数字
	function isNumber(value) {
		var patrn = /^(-)?\d+(\.\d+)?$/;
		if (patrn.exec(value) == null || value == "") {
			return false
		} else {
			return true
		}
	}
    function openSearchBack(id) {
		$(id).trigger('click');
	}
	function clearBelong() {
		$('#belongID,#belongName').val("");
	}
</script>