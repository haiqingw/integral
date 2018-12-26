<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" action="<?php echo U('SystemRacharge/lookup');?>">
	<input type="hidden" name="pageNum" value="<?php echo ($page); ?>" />
	<input type="hidden" name="numPerPage" value="${model.numPerPage}" />
	<input type="hidden" name="orderField" value="${param.orderField}" />
	<input type="hidden" name="keyword" value="<?php echo ($keyword); ?>" />
</form>

<div class="pageHeader">
	<form rel="pagerForm" method="post" action="<?php echo U('SystemRacharge/lookup');?>" onsubmit="return dwzSearch(this, 'dialog');">
		<div class="searchBar">
			<ul class="searchContent">
				<li>
					<label>关键字:</label>
					<input class="textInput" name="keyword" value="<?php echo ($keyword); ?>" type="text" placeholder="用户姓名,联系电话">
				</li>

			</ul>
			<div class="subBar">
				<ul>
					<li>
						<div class="buttonActive">
							<div class="buttonContent">
								<button type="submit">查询</button>
							</div>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</form>
</div>
<div class="pageContent">

	<table class="table" layoutH="118" targetType="dialog" width="100%">
		<thead>
			<tr>
				<th orderfield="orgName" align="center">序号</th>
				<th orderfield="orgNum" align="center">平台名称</th>
				<th orderfield="creator" align="center">用户电话</th>
				<th width="80" align="center">查找带回</th>
			</tr>
		</thead>
		<tbody>
			<?php if(is_array($rArray)): $i = 0; $__LIST__ = $rArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><tr>
					<td><?php echo ($i); ?></td>
					<td><?php echo ($val["busname"]); ?></td>
					<td><?php echo ($val["phone"]); ?></td>
					<td>
						<a class="btnSelect" href="javascript:$.myBringBack({id:'<?php echo ($val["id"]); ?>', orgName:'<?php echo ($val["busname"]); ?>'},'<?php echo ($val["id"]); ?>')" title="查找带回">选择</a>
					</td>
				</tr><?php endforeach; endif; else: echo "" ;endif; ?>
		</tbody>
	</table>
</div>
<script>
	$.myBringBack = function (data, id) {
		selectCycle(id);
		$.bringBack(data);
	};
</script>