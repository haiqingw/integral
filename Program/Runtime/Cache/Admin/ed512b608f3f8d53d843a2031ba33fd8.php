<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" onsubmit="return divSearch(this, 'jbsxBox');" action="<?php echo U('detail');?>?id=<?php echo ($id); ?>" method="post"></form>
<div class="pageHeader" style="border:1px #B8D0D6 solid">
	<form method="post" action="<?php echo U('editTemplateRun');?>" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone)">
	<div class="searchBar">
		<table class="searchContent">
			<tr>
				<!-- <td class="dateRange">
					<input type="text" value="" readonly="readonly" class="date" name="dateStart">
					<span class="limit">-</span>
					<input type="text" value="" readonly="readonly" class="date" name="dateEnd">
				</td> -->				
				<td>
					<input type="hidden" name="id" value="<?php echo ($tempDetail["id"]); ?>"/>
					表名：<input type="text" name="tableName" value="<?php echo ($tempDetail["tableName"]); ?>" class="required"/>
				</td>
				<td>
					唯一字段：<input type="text" name="unique" value="<?php echo ($tempDetail["unique"]); ?>" class="required"/>
				</td>
				<td>
					最多处理条数：<input type="text" style="width:50px" name="limitRow" value="<?php echo ($tempDetail["limitRow"]); ?>" class="required digits"/>
				</td>
				<td>
					校验字段：<input type="text" style="width:500px" name="checkFields" value="<?php echo ($tempDetail["checkFields"]); ?>" class="required"/>
				</td>
				<td>
					状态：
					<input type="radio" name="status" value="1" class="required" <?php if($tempDetail['status'] == 1): ?>checked<?php endif; ?>/>未开启&nbsp;
					<input type="radio" name="status" value="2" class="required" <?php if($tempDetail['status'] == 2): ?>checked<?php endif; ?>/>开启中
				</td>
				<td><div class="buttonActive"><div class="buttonContent"><button type="submit">保存</button></div></div></td>
			</tr>
		</table>
	</div>
	</form>
</div>

<div class="pageContent" style="border-left:1px #B8D0D6 solid;border-right:1px #B8D0D6 solid">
<div class="panelBar">
		<ul class="toolBar">
			<li><a class="add" href="<?php echo U('addAttr');?>?id=<?php echo ($tempDetail["id"]); ?>" target="dialog" mask="true"><span>添加属性</span></a></li>
			<li class="line">line</li>
			<li><a class="edit" href="<?php echo U('editAttr');?>?key={key}&id=<?php echo ($tempDetail["id"]); ?>" target="dialog" mask="true"><span>修改</span></a></li>
			<li class="line">line</li>
			<li><a class="delete" href="<?php echo U('delAttrRun');?>?key={key}&id=<?php echo ($tempDetail["id"]); ?>" target="ajaxTodo" title="确定要删除吗?"><span>删除</span></a></li>
		</ul>
	</div>
	<table class="table" width="50%" layoutH="218" rel="jbsxBox">
		<thead>
			<tr>
				<th width="33%" align="center">属性名称</th>
				<th width="33%" align="center">属性字段</th>
				<th width="33%" align="center">属性类型</th>
			</tr>
		</thead>
		<tbody>
			<?php if(count($attrList)): if(is_array($attrList)): $i = 0; $__LIST__ = $attrList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr target="key" rel="<?php echo ($vo["key"]); ?>">
				<td><?php echo ($vo["value"]); ?></td>
				<td><?php echo ($vo["key"]); ?></td>
				<td><?php echo ($vo["type"]); ?></td>
			</tr><?php endforeach; endif; else: echo "" ;endif; ?>
			<?php else: ?>
			<tr colspan=3> <td style="color:red;">暂无数据！</td> </tr><?php endif; ?>
		</tbody>
	</table>
</div>