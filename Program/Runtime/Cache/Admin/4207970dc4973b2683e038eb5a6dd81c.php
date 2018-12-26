<?php if (!defined('THINK_PATH')) exit();?><style>
.date {
	text-align: center;
}
</style>
<div class="pageContent">
	<div class="panelBar">
		<ul class="toolBar">
			<li class="line">line</li>
			<li><a class="add" href="<?php echo U('Buspublicmanage/add');?>" target="dialog" width="600" height="350" mask="true" rel="add" title="添加"><span>添加</span></a></li>
			<li class="line">line</li>
			<li><a class="edit" href="<?php echo U('Buspublicmanage/modify');?>?id={id}" target="dialog" width="600" height="350" mask="true " rel="modify" title="修改"><span>修改</span></a></li>
			<li class="line">line</li>
		</ul>
	</div>
	<table class="table" width="100%" layoutH="87">
		<thead>
			<tr>
				<th width="4%" align="center">序号</th>
				<th width="8%" align="center">平台</th>
				<th width="8%" align="center">私钥</th>
				<th width="8%" align="center">公钥地址</th>
				<th width="8%" align="center">私钥地址</th>
				<th width="6%" align="center">状态</th>
			</tr>
		</thead>
		<?php if($totalCount > 0): ?><tbody>
			<?php if(is_array($array)): $i = 0; $__LIST__ = $array;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr target="id" rel="<?php echo ($v["id"]); ?>">
				<td align="center"><?php echo ($i); ?></td>
				<td align="center"><?php echo ($v["companyID"]); ?></td>
				<td align="center"><?php echo ($v["secretKey"]); ?></td>
				<td align="center"><?php echo ($v["publicKeyUrl"]); ?></td>
				<td align="center"><?php echo ($v["privateKeyUrl"]); ?></td>
				<td align="center"><?php echo ($v["status"]); ?></td>
			</tr><?php endforeach; endif; else: echo "" ;endif; ?>
		</tbody>
		<?php else: ?>
		<tbody>
			<tr>
				<td align="center" colspan="13" style="color: red;">抱歉，没有找到符合的记录！</td>
			</tr>
		</tbody><?php endif; ?>
	</table>
	<div class="panelBar">
		<div class="pages">
			<span>显示</span> <select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage: this.value})">
				<option value="20"<?php if($numPerPage == 20): ?>selected="selected"<?php endif; ?>>20
				</option>
				<option value="50"<?php if($numPerPage == 50): ?>selected="selected"<?php endif; ?>>50
				</option>
				<option value="100"<?php if($numPerPage == 100): ?>selected="selected"<?php endif; ?>>100
				</option>
				<option value="200"<?php if($numPerPage == 200): ?>selected="selected"<?php endif; ?>>200
				</option>
			</select> <span>条，共<?php echo ($totalCount); ?>条</span>
		</div>
		<div class="pagination" targetType="navTab" totalCount="<?php echo ($totalCount); ?>" numPerPage="<?php echo ($numPerPage); ?>" pageNumShown="10" currentPage="<?php echo ($page); ?>"></div>
	</div>
</div>
<script>
	function viewOrder(or, type) {
		$.ajax({
			url : "<?php echo U('getOrderInfo');?>",
			data : {
				orderNum : or,
				type : type
			},
			dataType : "json",
			type : "post",
			success : function(ret) {
				//alert(JSON.stringify(ret));
				if (ret.type == 'trade') {
					var s = "订单号：" + ret.orderNum + "<br>商户信息："
							+ ret.merchantName + "<br>订单金额：" + ret.payMoney;
				} else {
					var s = "该用户直属返现次数：" + ret.one + "<br>间属返现次数：" + ret.two
							+ "<br>第三级返现次数：" + ret.three;
				}
				layer.alert(s);
			}
		});
	}
</script>
<style>
.statisticsAd {
	bottom: 28px;
	height: 60px;
	left: 0;
	position: absolute;
	width: 100%;
}

.statisticsAd ul {
	overflow: hidden;
	text-align: center;
}

.statisticsAd ul li {
	/*width:14%;*/
	display: inline-block;
	height: 40px;
	border: 2px #999999 solid;
	/*text-align:left;*/
	line-height: 20px;
	margin-left: 5px;
	padding: 5px 10px;
	font-weight: bold;
	color: #371414;
}

.statisticsAd ul li span {
	display: block;
	line-height: 20px;
}

.statisticsAd ul li span em {
	color: #996600;
	font-size: 14px;
}

.statisticsAd ul li  a {
	padding-left: 15px;
	color: #993333;
	display: block;
	line-height: 36px;
}

.tongji {
	padding-left: 33%;
}

.tongji .tongji_1 {
	padding: 0 0 0 20px;
	text-align: center;
}

.tongji .tongji_1 span {
	color: red;
}
</style>