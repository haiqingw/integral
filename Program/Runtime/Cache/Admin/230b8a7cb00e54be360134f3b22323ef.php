<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" method="post" action="<?php echo U('Comment/index');?>">
	<input type="hidden" name="status" value="${param.status}">
	<input type="hidden" name="keywords" value="${param.keywords}" />
	<input type="hidden" name="pageNum" value="1" />
	<input type="hidden" name="startDate" value="<?php echo ($startDate); ?>" />
	<input type="hidden" name="endDate" value="<?php echo ($endDate); ?>" />
	<!--每页显示多少条数据开始-->
	<input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>" />
	<!--每页显示多少条数据开始-->
	<input type="hidden" name="orderField" value="${param.orderField}" />
</form>
<div class="pageHeader">
	<form onsubmit="return navTabSearch(this);" action="<?php echo U('Comment/index');?>" method="post" id="BusinessList">
		<div class="searchBar">
			<table class="searchContent">
				<tr>
					<td align="left"></td>
					<td>添加时间：
						<input type="text" name="startDate" style="width: 80px;" class="date" readonly="true" datefmt="yyyy-MM-dd" value="<?php echo ($startDate); ?>"
						/> ~
						<input type="text" name="endDate" style="width: 80px;" class="date" readonly="true" datefmt="yyyy-MM-dd" value="<?php echo ($endDate); ?>"
						/>
					</td>
					<td align="left">活动类型：</td>
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
<div class="pageContent">
	<div class="panelBar">
		<ul class="toolBar">
			<li>
				<a class="add" href="<?php echo U('checkview');?>?cid={cid}" target="dialog" mask="true" width="400" height="300">
					<span>审核</span>
				</a>
			</li>
			<li class="line">line</li>

			<li>
				<a class="delete" href="<?php echo U('helpdel');?>?cid={cid}" target="ajaxTodo" title="确定要删除吗?">
					<span>删除</span>
				</a>
			</li>
		</ul>
	</div>
	<table class="table" width="100%" layoutH="125">
		<thead>
			<tr>
				<th width="8%" align="center">序号</th>
				<?php if($checkUser == 1): ?><th width="7%" align="center">平台</th><?php endif; ?>

				<th width="6%" align="center">产品</th>
				<th width="10%" align="center">评论商户</th>
				<th align="center" width="20%">评论内容</th>
				<th align="center" width="8%">状态</th>
				<th width="10%" align="center">评论时间</th>
				<th width="10%" align="center">评分</th>
				<th width="10%" align="center">操作</th>
			</tr>
		</thead>
		<?php if($totalCount > 0): ?><tbody>
				<?php if(is_array($resArray)): $i = 0; $__LIST__ = $resArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr target="cid" rel="<?php echo ($v["id"]); ?>">
						<td align="center"><?php echo ($i); ?></td>
						<?php if($checkUser == 1): ?><td style="font-weight: bold;"><?php echo ($v["platu"]); ?></td><?php endif; ?>
						<td align="center"><?php echo ($v["product"]); ?></td>
						<td align="center"><?php echo ($v["busname"]); ?></td>
						<td align="center"><?php echo ($v["content"]); ?></td>
						<td align="center">
							<?php if($v['status'] == 3): ?><span style="color: #930114;"><?php echo ($v["stat"]); ?></span><?php endif; ?>
							<?php if($v['status'] == 2): ?><span style="color: #930114;"><?php echo ($v["stat"]); ?></span><?php endif; ?>
							<?php if($v['status'] == 1): ?><span style="color: #1E8EFC;"><?php echo ($v["stat"]); ?></span><?php endif; ?>
						</td>
						<td align="center"><?php echo ($v["createTime"]); ?></td>
						<td align="center"><?php echo ($v["score"]); ?></td>

						<td align="center"><?php echo ($v["auditTime"]); ?></td>
					</tr><?php endforeach; endif; else: echo "" ;endif; ?>
			</tbody>
			<?php else: ?>
			<tbody>
				<tr>
					<td align="center" colspan="6" style="color: red;">抱歉， 没有找到符合的记录！</td>
				</tr>
			</tbody><?php endif; ?>
	</table>
	<div class="panelBar">
		<div class="pages">
			<span>显示</span>
			<select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage:this.value})">
				<option value="20" <?php if($numPerPage == 20): ?>selected="selected"<?php endif; ?>>20
				</option>
				<option value="50" <?php if($numPerPage == 50): ?>selected="selected"<?php endif; ?>>50
				</option>
				<option value="100" <?php if($numPerPage == 100): ?>selected="selected"<?php endif; ?>>100
				</option>
				<option value="200" <?php if($numPerPage == 200): ?>selected="selected"<?php endif; ?>>200
				</option>
			</select>
			<span>条，共<?php echo ($totalCount); ?>条</span>
		</div>
		<div class="pagination" targetType="navTab" totalCount="<?php echo ($totalCount); ?>" numPerPage="<?php echo ($numPerPage); ?>" pageNumShown="10" currentPage="<?php echo ($page); ?>"></div>
	</div>
</div>