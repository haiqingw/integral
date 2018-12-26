<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" method="post" action="<?php echo U('Adslots/larize');?>">
	<input type="hidden" name="pageNum" value="<?php echo ($page); ?>" />
	<input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>" />
</form>
<div class="pageContent" id="mainContent">
	<form id="SlideForms" method="post" action="<?php echo U('Adslots/larize');?>" onsubmit="return navTabSearch(this);"></form>
	<div class="panelBar">
		<ul class="toolBar">
			<li>
				<a class="add" href="<?php echo U('Adslots/laadd');?>" target="navTab" rel="">
					<span>添加</span>
				</a>
			</li>
			<li class="line"></li>
			<li>
				<a class="edit" href="<?php echo U('Adslots/lamodify');?>?id={SID}" target="navTab" rel="">
					<span>修改</span>
				</a>
			</li>
			<li class="line"></li>
		</ul>
	</div>
	<table width="100%" class="table" layoutH="76">
		<thead>
			<tr>
				<th align="center" width="3%">序号</th>
				<?php if($checkUser == 1): ?><th width="7%" align="center">平台</th><?php endif; ?>
				<th align="center" width="8%">图片</th>
				<th align="center" width="8%">二维码显示X轴</th>
				<th align="center" width="6%">二维码显示Y轴</th>
				<th align="center" width="6%">二维码尺寸</th>
				<th align="center" width="6%">变更使用状态</th>
				<th align="center" width="6%">状态</th>
				<th align="center" width="10%">创建时间</th>
			</tr>
		</thead>
		<tbody>
			<?php if(is_array($resArray)): $i = 0; $__LIST__ = $resArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr target="SID" rel="<?php echo ($vo["id"]); ?>">
					<td><?php echo ($i); ?></td>
					<?php if($checkUser == 1): ?><td style="font-weight: bold;"><?php echo ($vo["platu"]); ?></td><?php endif; ?>
					<td>
						<a style="line-height: 35px;" href="<?php echo U('Adslots/preview');?>?id=<?php echo ($vo["id"]); ?>&type=laz" target="dialog" mask="true" maxable="false"
						    resizable="false" height="1073" width="640" title="<?php echo ($vo["title"]); ?>">
							<img src="<?php echo ($vo["picUrl"]); ?>" width="20" height="20">
						</a>
					</td>
					<td><?php echo ($vo["xAxis"]); ?></td>
					<td><?php echo ($vo["yAxis"]); ?></td>
					<td><?php echo ($vo["qrSize"]); ?></td>
					<td>
						<?php if($vo['useStatus'] == 2): ?><a href="<?php echo U('Adslots/lausestatus');?>?id=<?php echo ($vo["id"]); ?>&type=<?php echo ($vo["useStatus"]); ?>" target="ajaxTodo" rel="updateStatus" title="点击是否展示">
								<span style="color: #339933; line-height: 22px; font-weight: bold;">使用</span>
							</a><?php endif; ?>
						<?php if($vo['useStatus'] == 1): ?><a href="<?php echo U('Adslots/lausestatus');?>?id=<?php echo ($vo["id"]); ?>&type=<?php echo ($vo["useStatus"]); ?>" target="ajaxTodo" rel="updateStatus" title="点击是否展示">
								<span style="color: #330000; line-height: 22px; font-weight: bold;">不使用</span>
							</a><?php endif; ?>
					</td>
					<td>
						<?php if($vo['status'] == 1): ?><span style="color: #7e8025; line-height: 22px; font-weight: bold;">正常</span>
							<?php if($vo['useStatus'] == 1): ?><span style="color: #7e8025; line-height: 22px; font-weight: bold;">（正在使用）</span>
								<?php else: ?>
								<span style="color: #330000; line-height: 22px; font-weight: bold;">（未使用）</span><?php endif; endif; ?>
						<?php if($vo['status'] == 2): ?><span style="color: #330000; line-height: 22px; font-weight: bold;">作废</span><?php endif; ?>
					</td>
					<td><?php echo ($vo["createTime"]); ?></td>

				</tr><?php endforeach; endif; else: echo "" ;endif; ?>

		</tbody>
	</table>

	<div class="panelBar">
		<div class="pages">
			<span>显示</span>
			<select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage: this.value})">
				<option value="40" <?php if($numPerPage == 40): ?>selected="selected"<?php endif; ?>>40
				</option>
				<option value="80" <?php if($numPerPage == 80): ?>selected="selected"<?php endif; ?>>80
				</option>
				<option value="120" <?php if($numPerPage == 120): ?>selected="selected"<?php endif; ?>>120
				</option>
				<option value="200" <?php if($numPerPage == 200): ?>selected="selected"<?php endif; ?>>200
				</option>
			</select>
			<span>条，共<?php echo ($totalCount); ?>条</span>
		</div>

		<div class="pagination" targetType="navTab" totalCount="<?php echo ($totalCount); ?>" numPerPage="<?php echo ($numPerPage); ?>" pageNumShown="10" currentPage="<?php echo ($page); ?>"></div>
	</div>
</div>
<script type="text/javascript">
	function homeChangeOrd(obj, id) {
		var val = obj.text();
		var c = obj.parent("div");
		obj
			.parent("div")
			.html(
				"<input type='text' style='text-align:center;width:50px;' onFocus=this.select()  onblur=homeChangeOrdConfirm($(this)," +
				id + ") value='" + val + "' />");

		c.children("input").focus();
	}

	function homeChangeOrdConfirm(obj, id) {
		var ord = obj.val();
		$.ajax({
			url: "<?php echo U('HomeModule/Sort');?>",
			type: "POST",
			async: false,
			data: {
				hid: id,
				sortNum: ord
			},
			success: function (data) {
				$('#SlideForms').submit();
			}
		});
	}
</script>