<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" method="post" action="<?php echo U('systemlist');?>">
	<input type="hidden" name="status" value="${param.status}">
	<input type="hidden" name="keywords" value="${param.keywords}" />
	<input type="hidden" name="pageNum" value="1" />
	<!--每页显示多少条数据开始-->
	<input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>" />
	<!--每页显示多少条数据开始-->
	<input type="hidden" name="orderField" value="${param.orderField}" />
</form>
<div class="pageContent">
	<div class="panelBar">
		<ul class="toolBar">
			<li>
				<a class="add" href="<?php echo U('Systemuser/add');?>" target="navTab" rel="systemadd">
					<span>添加</span>
				</a>
			</li>
			<li class="line">line</li>
			<li>
				<a class="edit" href="<?php echo U('Systemuser/update');?>?uid={sid_user}" target="navTab">
					<span>修改</span>
				</a>
			</li>
			<li class="line">line</li>
			<li>
				<a title="确定要删除这些记录吗?" target="selectedTodo" rel="decldel" postType="string" href="<?php echo U('Systemuser/del');?>"
                    class="delete"><span>删除</span></a>
			</li>
		</ul>
	</div>
	<table class="table" width="100%" layoutH="75">
		<thead>
			<tr>
				<th width="2%"><input type="checkbox" group="decldel" class="checkboxCtrl"></th>
				<th width="80" align="center">ID编号</th>
				<th width="120" align="center">用户账号</th>
				<th width="120" align="center">用户密码</th>
				<th width="120" align="center">所属角色</th>
				<th width="100" align="center">用户手机</th>
				<th width="150" align="center">用户邮箱</th>
				<!-- <th width="150" align="center">创建区域</th> -->
				<th width="100" align="center">创建时间</th>
				<th width="80" align="center">创建IP</th>
			</tr>
		</thead>
		<?php if($totalCount > 0): ?><tbody>
				<?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr target="sid_user" rel="<?php echo ($v["usertable_ID"]); ?>">
						<td><input name="decldel" value="<?php echo ($v["usertable_ID"]); ?>" type="checkbox"></td>
						<td align="center"><?php echo ($v["usertable_ID"]); ?></td>
						<td align="center"><?php echo ($v["usertable_Name"]); ?></td>
						<td align="center">密码已加密</td>
						<td align="center"><?php echo ($v["rolename"]); ?></td>
						<td align="center"><?php echo ($v["usertable_Phone"]); ?></td>
						<td align="center"><?php echo ($v["usertable_Email"]); ?></td>
						<!-- <td align="center"><?php echo ($v["usertable_createarea"]); ?></td> -->
						<td align="center"><?php echo ($v["usertable_createtime"]); ?></td>
						<td align="center"><?php echo ($v["usertable_createip"]); ?></td>
					</tr><?php endforeach; endif; else: echo "" ;endif; ?>
			</tbody>
			<?php else: ?>
			<tbody>
				<tr>
					<td align="center" colspan="8" style="color:red;">抱歉， 没有找到符合的记录！</td>
				</tr>
			</tbody><?php endif; ?>
	</table>
	<div class="panelBar">
		<div class="pages">
			<span>显示</span>
			<select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage:this.value})">
				<option value="20" <?php if($numPerPage == 20): ?>selected="selected"<?php endif; ?>>20</option>
				<option value="50" <?php if($numPerPage == 50): ?>selected="selected"<?php endif; ?>>50</option>
				<option value="100" <?php if($numPerPage == 100): ?>selected="selected"<?php endif; ?>>100</option>
				<option value="200" <?php if($numPerPage == 200): ?>selected="selected"<?php endif; ?>>200</option>
			</select>
			<span>条，共<?php echo ($totalCount); ?>条</span>
		</div>
		<div class="pagination" targetType="navTab" totalCount="<?php echo ($totalCount); ?>" numPerPage="<?php echo ($numPerPage); ?>" pageNumShown="10" currentPage="<?php echo ($page); ?>"></div>
	</div>
</div>