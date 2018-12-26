<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" method="post" action="<?php echo U('ModuleManage/index');?>">
	<input type="hidden" name="pageNum" value="<?php echo ($page); ?>" />
	<input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>" />
</form>
<div class="pageContent" id="mainContent">
	<form id="SlideForms" method="post" action="<?php echo U('ModuleManage/index');?>" onsubmit="return navTabSearch(this);"></form>
	<div class="panelBar">
		<ul class="toolBar">
			<li><a class="add" href="<?php echo U('ModuleManage/add');?>" target="dialog" mask="true" width="624" height="500" rel=""><span>添加</span></a></li>
			<li class="line"></li>
			<li><a class="edit" href="<?php echo U('ModuleManage/modify');?>?id={SID}" target="dialog" mask="true" width="624" height="500"
				 rel=""><span>修改</span></a></li>
			<li class="line"></li>
			<li><a class="delete" href="<?php echo U('ModuleManage/Delete');?>?Mid={SID}&dtype=2" target="ajaxTodo" title="确定删除么" rel=""><span>删除</span></a></li>
			<li class="line"></li>

		</ul>
	</div>
	<table width="100%" class="table" layoutH="76">
		<thead>
			<tr>
				<th align="center" width="3%">序号</th>
				<!-- <th align="center" width="11%">排序</th> -->
				<th align="center" width="11%">模块名称</th>
				<th align="center" width="8%">图标</th>
				<th align="center" width="8%">访问地址</th>
				<th align="center" width="8%">页面地址</th>
				<th align="center" width="10%">创建时间</th>
				<th align="center" width="10%">使用状态</th>
				<th align="center" width="8%">操作</th>
			</tr>
		</thead>
		<tbody>
			<?php if(is_array($resArray)): $i = 0; $__LIST__ = $resArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr target="SID" rel="<?php echo ($vo["id"]); ?>">
					<td><?php echo ($i + $offset); ?></td>
					<!-- <td style="position:relative;"><a style="display:block;background-color:#dec8c8;width:50%;height:25px;line-height:25px; margin:0 auto;"
						 onclick="changeOrd($(this), <?php echo ($vo['Mid']); ?>)" target=""><?php echo ($vo["Msort"]); ?></a></td> -->
					<td><?php echo ($vo["name"]); ?></td>
					<td>
						<a style="line-height:25px;" href="<?php echo U('ModuleManage/preview');?>?Mid=<?php echo ($vo["Mid"]); ?>" target="dialog" mask="true"
						 maxable="false" resizable="false" height="611" width="345" title="<?php echo ($vo["SlideTitle"]); ?>"><img src="<?php echo ($vo["picUrl"]); ?>"
							 width="20" height="20"></a>
					</td>
					<td><?php echo ($vo["controllerUrl"]); ?></td>
					<td><?php echo ($vo["htmlUrl"]); ?></td>
					<td><?php echo ($vo["createTime"]); ?></td>
					<td>
						<?php if($vo['status'] == 1): ?><span style="color:#216a1d;font-weight:bold;">正常</span>
							<?php else: ?>
							<span style="color:#d3281c;font-weight:bold;">停用</span><?php endif; ?>
					</td>
					<td>
						<?php if($vo['status'] == 1): ?><a href="<?php echo U('ModuleManage/UseStatus');?>?mid=<?php echo ($vo["Mid"]); ?>&uStatus=<?php echo ($vo["status"]); ?>" target="ajaxTodo" title='点击停用'><span
								 style="color:#f73c25;line-height:22px; font-weight:bold;">停用</span></a>
							<?php else: ?>
							<a href="<?php echo U('ModuleManage/UseStatus');?>?mid=<?php echo ($vo["id"]); ?>&uStatus=<?php echo ($vo["status"]); ?>" target="ajaxTodo" title='点击启用'><span
								 style="color:#7e8025;line-height:22px; font-weight:bold;">启用</span></a><?php endif; ?>
					</td>
				</tr><?php endforeach; endif; else: echo "" ;endif; ?>

		</tbody>
	</table>

	<div class="panelBar">
		<div class="pages">
			<span>显示</span>
			<select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage: this.value})">
				<option value="40" <?php if($numPerPage == 40): ?>selected="selected"<?php endif; ?>>40</option>
				<option value="80" <?php if($numPerPage == 80): ?>selected="selected"<?php endif; ?>>80</option>
				<option value="120" <?php if($numPerPage == 120): ?>selected="selected"<?php endif; ?>>120</option>
				<option value="200" <?php if($numPerPage == 200): ?>selected="selected"<?php endif; ?>>200</option>
			</select>
			<span>条，共<?php echo ($totalCount); ?>条</span>
		</div>

		<div class="pagination" targetType="navTab" totalCount="<?php echo ($totalCount); ?>" numPerPage="<?php echo ($numPerPage); ?>" pageNumShown="10"
		 currentPage="<?php echo ($page); ?>"></div>
	</div>
</div>
<script type="text/javascript">
	function changeOrd(obj, id) {
		var val = obj.text();
		var c = obj.parent("div");
		obj.parent("div").html(
			"<input type='text' style='text-align:center;width:50px;' onFocus=this.select()  onblur=changeOrdConfirm($(this)," +
			id + ") value='" + val + "' />");

		c.children("input").focus();
	}

	function changeOrdConfirm(obj, id) {
		var ord = obj.val();
		$.ajax({
			url: "<?php echo U('ModuleManage/Sort');?>",
			type: "POST",
			async: false,
			data: {
				mid: id,
				sortNum: ord
			},
			success: function (data) {
				$('#SlideForms').submit();
			}
		});
	}
</script>