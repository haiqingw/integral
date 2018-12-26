<?php if (!defined('THINK_PATH')) exit();?><style>
.date {
	text-align: center;
}
</style>
<form id="pagerForm" method="post" action="<?php echo U('index');?>"
	onsubmit="return navTabSearch(this);">
	<input type="hidden" name="keywords" value="<?php echo ($keywords); ?>" /> <input
		type="hidden" name="pageNum" value="<?php echo ($page); ?>" /> <input type="hidden"
		name="startDate" value="<?php echo ($startDate); ?>" /> <input type="hidden"
		name="endDate" value="<?php echo ($endDate); ?>" /> <input type="hidden"
		name="sClass" value="<?php echo ($sClass); ?>" /> <input type="hidden"
		name="bindStatus" value="<?php echo ($bindStatus); ?>" /><input type="hidden"
		name="numPerPage" value="<?php echo ($numPerPage); ?>" /> <input type="hidden"
		name="asc" value="<?php echo ($asc); ?>" /> <input type="hidden" name="orderField"
		value="<?php echo ($orderField); ?>" />
</form>
<div class="pageHeader">
	<form onsubmit="return navTabSearch(this);" action="<?php echo U('index');?>"
		method="post" id="BlacklistIndex">
		<div class="searchBar">
			<table class="searchContent">
				<tr>
					<td align="left"></td>
					<td><select name="sClass" id="sClass" class="combox">
							<option value="">请选择</option>
							<option value="S"<?php if($sClass == 'S'): ?>selected="selected"<?php endif; ?>>试刷日期

							</option>
							<option value="F"<?php if($sClass == 'F'): ?>selected="selected"<?php endif; ?>>添加日期
							</option>
							<option value="B"<?php if($sClass == 'B'): ?>selected="selected"<?php endif; ?>>绑定时间
							</option>

					</select>：<input type="text" name="startDate" style="width: 80px;"
						class="date" readonly="true" datefmt="yyyy-MM-dd"
						value="<?php echo ($startDate); ?>" /> ~ <input type="text" name="endDate"
						style="width: 80px;" class="date" readonly="true"
						datefmt="yyyy-MM-dd" value="<?php echo ($endDate); ?>" /></td>
					<td align="right">关键词：<input type="text" value="<?php echo ($keywords); ?>"
						id="keywords" name="keywords" autocomplete="off"
						placeholder="终端号,商户名称,所属代理商" style="width: 160px;" />
					</td>
					<td><label>变更状态：</label><select name="bindStatus"
						id="bindStatus" class="combox">
							<option value="">请选择</option>
							<option value="1"<?php if($bindStatus == 1): ?>selected="selected"<?php endif; ?>>已试刷
							</option>
							<option value="2"<?php if($bindStatus == 2): ?>selected="selected"<?php endif; ?>>未试刷
							</option>
					</select></td>
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
<script>
	//new $.dynSelect({inputID:'keywords',dataUrl:"<?php echo U('Users/getBusName');?>"});
</script>
<script>
	//关闭弹出框
	if ("<?php echo ($state); ?>" == 'success') {
		$.pdialog.close('checkuser');
	}
	function exportExcelsum() {
		//        alertMsg.warn('该功能暂不可用');
		//        return false;
		//window.location.href = "<?php echo U('Excel/outUsers');?>?startDate=<?php echo ($startDate); ?>&endDate=<?php echo ($endDate); ?>=" + "&busName=" + $('input[name="busName"]').val();
	}
	//按字段排序
	function Asc(o) {
		var orderfield = $(o).attr('orderField');
		if ($(o).attr('class').substring(0, 4).replace(/\s/g, "") == 'desc') {
			$(o).attr({
				'orderField' : orderfield + ' asc'
			});
		} else {
			$(o).attr({
				'orderField' : orderfield + ' desc'
			});
		}
	}
</script>
<div class="pageContent">
	<div class="panelBar">
        <ul class="toolBar">
            <li class="line">line</li>
            <li><a class="edit" href="<?php echo U('modify');?>?id={id}" target="dialog" rel="jjtb" mask=true title="机具调拨" width="700" height='550'><span>机具调拨</span></a></li>
            <li class="line">line</li>
        </ul>
    </div>
	<table class="table" width="100%" layoutH="114">
		<thead>
			<tr>
				<th align="center">序号</th>
				<th align="center">商户名称</th>
				<th align="center">商户编号</th>
				<th align="center">终端号</th>
				<th align="center">所属代理商名称</th>
				<th align="center">所属代理商会员号</th>
				<th align="center">绑定时间</th>
				<th align="center">状态</th>
				<th align="center">试刷时间</th>
				<th align="center">添加时间</th>
				<?php if($check == 1): ?><th align="center" width="6%"><span
					style="color: #000033; font-weight: bold;">平台</span></th><?php endif; ?>

			</tr>
		</thead>
		<?php if($totalCount > 0): ?><tbody>
			<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr target="id" rel="<?php echo ($v["id"]); ?>">
				<td align="center"><?php echo ($i); ?></td>
				<td align="center"><?php echo ($v["merchantName"]); ?></td>
				<td align="center"><?php echo ($v["merchantNo"]); ?></td>
				<td align="center"><?php echo ($v["terminalNo"]); ?></td>
				<td align="center"><?php echo ($v["agentName"]); ?></td>
				<td align="center"><?php echo ($v["agentNo"]); ?></td>
				<td align="center"><?php echo ($v["bindTime"]); ?></td>
				<td align="center"><?php if($v['bindStatus'] == 1): ?>已试刷<?php else: ?>未试刷<?php endif; ?></td>
				<td align="center"><?php echo ($v["updateTime"]); ?></td>
				<td align="center"><?php echo (dateformat($v["addtime"],2)); ?></td>
				<?php if($check == 1): ?><td align="center"><span
					style="color: #993300; font-weight: bold;"><?php echo ($v["user"]); ?></span></td><?php endif; ?>
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
			<span>显示</span> <select class="combox" name="numPerPage"
				onchange="navTabPageBreak({numPerPage: this.value})">
				<option value="20"<?php if($numPerPage == 20): ?>selected="selected"<?php endif; ?>>20
				</option>
				<option value="50"<?php if($numPerPage == 50): ?>selected="selected"<?php endif; ?>>50
				</option>
				<option value="100"<?php if($numPerPage == 100): ?>selected="selected"<?php endif; ?>>100
				</option>
				<option value="200"<?php if($numPerPage == 200): ?>selected="selected"<?php endif; ?>>200
				</option>
			</select> <span>条，共<?php echo ($totalCount); ?>条</span>
		</div>
		<div class="pagination" targetType="navTab" totalCount="<?php echo ($totalCount); ?>"
			numPerPage="<?php echo ($numPerPage); ?>" pageNumShown="10" currentPage="<?php echo ($page); ?>"></div>
	</div>
</div>