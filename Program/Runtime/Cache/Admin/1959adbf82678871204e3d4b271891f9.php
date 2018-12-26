<?php if (!defined('THINK_PATH')) exit();?><div layoutH="3"  class="pageContent" style="border-left:1px #B8D0D6 solid;border-right:1px #B8D0D6 solid">
	<table class="table" width="100%" layoutH="30">
		<thead>
			<tr>
				<th width="8%" align="center">激活模式</th>
				<th width="8%" align="center">激活达标金额</th>
				<th width="15%" align="center">【用户】激活返现金额</th>
				<th width="15%" align="center">【用户商】激活返现金额</th>
				<th width="15%" align="center">【代理商】激活返现金额</th>
				<th width="15%" align="center">【服务商】激活返现金额</th>
			</tr>
		</thead>
		<tbody>
			<?php if(count($list)): if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr target="key" rel="<?php echo ($vo["id"]); ?>">
				<td>
					<?php if($vo["conditions"] == 1): ?>普通激活
					<?php elseif($vo["conditions"] == 2): ?>当月激活
					<?php elseif($vo["conditions"] == 3): ?>30天内激活
					<?php elseif($vo["conditions"] == 4): ?>3个月内激活
					<?php elseif($vo["conditions"] == 5): ?>半年内激活<?php endif; ?>
				</td>
				<td><?php echo ($vo["money"]); ?></td>
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