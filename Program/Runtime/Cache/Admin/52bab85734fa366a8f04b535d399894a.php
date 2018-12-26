<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" onsubmit="return divSearch(this, 'jbsxBoxCb');" action="<?php echo U('publicpage');?>?id=<?php echo ($id); ?>" method="post"></form>
<style>
.grid .gridTbody td div{ height:auto }
</style>
<div class="pageContent" style="border-left:1px #B8D0D6 solid;border-right:1px #B8D0D6 solid">
<div class="panelBar">
		<ul class="toolBar">
			<li><a class="add" href="<?php echo U('publicadd');?>?id=<?php echo ($id); ?>" target="dialog" mask="true" width="1002" height="725"><span>添加返现模板(<?php echo ($category["brandName"]); ?>-<?php echo ($category["productName"]); ?>)</span></a></li>
			<li class="line">line</li>
			<li><a class="edit" href="<?php echo U('publicadd');?>?key={key}&id=<?php echo ($id); ?>" target="dialog" mask="true" width="1002" height="725"><span>修改</span></a></li>
			<li class="line">line</li>
			<li><a class="delete" href="<?php echo U('delHandle');?>?key={key}&id=<?php echo ($id); ?>" target="ajaxTodo" title="确定要删除吗?"><span>删除</span></a></li>
		</ul>
	</div>
	<table class="table" width="100%" layoutH="100" rel="jbsxBoxCb">
		<thead>
			<tr>
				<th width="3%" align="center">模板ID</th>
				<th width="8%" align="center">模板名称</th>
				<th width="10%" align="center">模板激活描述</th>
				<th width="9%" align="center">激活返现规则</th>
				<th width="9%" align="center">交易返现规则</th>
				<th width="9%" align="center">模板状态</th>
			</tr>
		</thead>
		<tbody>
			<?php if(count($list)): if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr target="key" rel="<?php echo ($vo["id"]); ?>">
				<td><?php echo ($vo["id"]); ?></td>
				<td><?php echo ($vo["title"]); ?></td>
				<td class="activeType"><font color=blue><?php if($vo['activeType'] == 1): ?>激活两台达标<?php else: ?>没有达标条件<?php endif; ?></font></td>
				<td><a href="<?php echo U('viewRule');?>?id=<?php echo ($vo["id"]); ?>" width="800" target="dialog" title="激活返现规则">【点击查看】</a></td>
				<td><a href="<?php echo U('viewRulePay');?>?&id=<?php echo ($vo["id"]); ?>" width="800" target="dialog" title="交易返现规则"><?php echo ($vo["salesman"]); ?>条【点击查看】</a></td>
				<td>
					<?php if($vo["status"] == 1): ?><font color=#0099FF>正常</font><?php endif; ?>
					<?php if($vo["status"] == 2): ?><font color=#FF0000>已关闭</font><?php endif; ?>
					&nbsp;
					<?php if($vo["status"] == 1): ?><a href="<?php echo U('stopCategory');?>?cid=<?php echo ($vo["id"]); ?>" target="ajaxTodo" title="停用后使用该模板的终端无法获得返现！确定要停用？"><font color=#FF0000>点击停用</font></a><?php endif; ?>
					<?php if($vo["status"] == 2): ?><a href="<?php echo U('openCategory');?>?cid=<?php echo ($vo["id"]); ?>" target="ajaxTodo" title="是否开启该模板？"><font color=#0099FF>点击开启</font></a><?php endif; ?>
				</td>
			</tr><?php endforeach; endif; else: echo "" ;endif; ?>
			<?php else: ?>
			<tr colspan=10> <td style="color:red;">暂无数据！</td> </tr><?php endif; ?>
		</tbody>
	</table>
</div>