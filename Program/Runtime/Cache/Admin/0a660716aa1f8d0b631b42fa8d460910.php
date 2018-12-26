<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" action="<?php echo U('searchBack');?>">
	<input type="hidden" name="pageNum" value="<?php echo ($page); ?>" /> 
	<input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>" /> 
	<input type="hidden" name="keywords" value="<?php echo ($keywords); ?>" />
</form>
<div class="pageHeader">
	<form rel="pagerForm" method="post" action="<?php echo U('searchBack');?>"
		onsubmit="return dialogSearch(this);">
		<div class="searchBar">
			<input type="hidden" name="mid" value="<?php echo ($mid); ?>" />
			<ul class="searchContent">
				<li><label>关键词:</label> <input class="textInput"
					name="keywords" placeholder="用户名称，电话" value="<?php echo ($keywords); ?>" type="text" /></li>
			</ul>
			<div class="subBar">
				<ul>
					<li><div class="buttonActive">
							<div class="buttonContent">
								<button type="submit">查询</button>
							</div>
						</div></li>
				</ul>
			</div>
		</div>
	</form>
</div>
<div class="pageContent">
	<table class="table" layoutH="118" targetType="dialog" width="100%">
		<thead>
			<tr>
				<th>商户名称</th>
				<th>商户电话</th>
				<th>选择</th>
			</tr>
		</thead>
		<tbody>
			<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
				<td><?php echo ($vo["busname"]); ?></td>
				<td><?php echo ($vo["phone"]); ?></td>
				<td><a class="btnSelect" title="选择"
					href="javascript:$.bringBack({id:'<?php echo ($vo["id"]); ?>', name:'<?php echo ($vo["busname"]); ?>', phone:'<?php echo ($vo["phone"]); ?>'})" style="cursor: pointer;">选择</a>
				</td>
			</tr><?php endforeach; endif; else: echo "" ;endif; ?>
		</tbody>
	</table>

	<div class="panelBar">
		<div class="pages">
			<span>每页</span> <select name="numPerPage"
				onchange="dwzPageBreak({targetType:'dialog', numPerPage:this.value})">
				<option value="10"<?php if($numPerPage == 10): ?>selected="selected"<?php endif; ?>>10
				</option>
				<option value="20"<?php if($numPerPage == 20): ?>selected="selected"<?php endif; ?>>20
				</option>
				<option value="50"<?php if($numPerPage == 50): ?>selected="selected"<?php endif; ?>>50
				</option>
				<option value="100"<?php if($numPerPage == 100): ?>selected="selected"<?php endif; ?>>100
				</option>
			</select> <span>条，共<?php echo ($totalCount); ?>条</span>
		</div>
		<div class="pagination" targetType="dialog" totalCount="<?php echo ($totalCount); ?>"
			numPerPage="<?php echo ($numPerPage); ?>" pageNumShown="5" currentPage="<?php echo ($page); ?>"></div>
	</div>
</div>