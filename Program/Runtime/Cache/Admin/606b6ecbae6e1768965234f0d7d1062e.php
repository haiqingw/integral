<?php if (!defined('THINK_PATH')) exit();?><div layoutH="3"  class="pageContent" style="border-left:1px #B8D0D6 solid;border-right:1px #B8D0D6 solid">
	<table class="table" width="100%" layoutH="30">
		<thead>
			<tr>
				<th width="8%" align="center">结算模式</th>
				<th width="8%" align="center">交易类型</th>
				<th width="15%" align="center">【用户】交易返现比例</th>
				<th width="15%" align="center">【用户商】交易返现比例</th>
				<th width="15%" align="center">【代理商】交易返现比例</th>
				<th width="15%" align="center">【服务商】交易返现比例</th>
			</tr>
		</thead>
		<tbody>
			<?php if(count($list)): if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr target="key" rel="<?php echo ($vo["id"]); ?>">
				<td><?php echo ($vo["billingcycle"]); ?></td>
				<td><?php echo ($vo["bname"]); ?></td>
				<td><?php echo ($vo["valsa"]); ?></td>
				<td><?php echo ($vo["valsb"]); ?></td>
				<td><?php echo ($vo["valsc"]); ?></td>
				<td><?php echo ($vo["valsd"]); ?></td>
			</tr><?php endforeach; endif; else: echo "" ;endif; ?>
			<?php else: ?>
			<tr colspan=10> <td style="color:red;">暂无数据！</td> </tr><?php endif; ?>
		</tbody>
	</table>
</div>