<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" method="post" action="<?php echo U('Order/index');?>" onsubmit="return navTabSearch(this);">
	<input type="hidden" name="keywords" value="<?php echo ($params['keywords']); ?>" />
	<input type="hidden" name="pageNum" value="<?php echo ($page); ?>" />
	<input type="hidden" name="startDate" value="<?php echo ($startDate); ?>" />
	<input type="hidden" name="endDate" value="<?php echo ($endDate); ?>" />
	<input type="hidden" name="busType" value="<?php echo ($busType); ?>">
	<input type="hidden" name="status" value="<?php echo ($params['status']); ?>">
	<input type="hidden" name="al" value="<?php echo ($al); ?>">
	<input type="hidden" name="parent" value="<?php echo ($params['parent']); ?>">
	<input type="hidden" name="isReceipt" value="<?php echo ($params['isReceipt']); ?>">
	<input type="hidden" name="isPay" value="<?php echo ($params['isPay']); ?>">
	<input type="hidden" name="isOrder" value="<?php echo ($params['isOrder']); ?>">
	<input type="hidden" name="isShip" value="<?php echo ($params['isShip']); ?>">

</form>
<div class="pageHeader">
	<form onsubmit="return navTabSearch(this);" action="<?php echo U('Order/index');?>" method="post" id="BusinessList">
		<div class="searchBar">
			<table class="searchContent">
				<tr>
					<td align="left"></td>
					<td>订单时间：
						<input type="text" name="startDate" style="width: 80px;" class="date" readonly="true" datefmt="yyyy-MM-dd" value="<?php echo ($startDate); ?>"
						/> ~
						<input type="text" name="endDate" style="width: 80px;" class="date" readonly="true" datefmt="yyyy-MM-dd" value="<?php echo ($endDate); ?>"
						/>
					</td>
					<td align="right">关键词：
						<input type="text" value="<?php echo ($params['keywords']); ?>" id="keywords" name="keywords" autocomplete="off" placeholder="商户名称,联系电话"
						    style="width: 160px;" />
						<td align="left">订单状态：</td>
						<td>
							<select name="isOrder" class="combox">
								<option value="">请选择</option>
								<option value="1" <?php if($params['isOrder'] == '1'): ?>selected<?php endif; ?>>正常
								</option>
								<option value="2" <?php if($params['isOrder'] == '2'): ?>selected<?php endif; ?>>已取消
								</option>
								<option value="3" <?php if($params['isOrder'] == '3'): ?>selected<?php endif; ?>>作废
								</option>
							</select>
						</td>
						<td align="left">支付状态：</td>
						<td>
							<select name="isPay" class="combox">
								<option value="">请选择</option>
								<option value="2" <?php if($params['isPay'] == '2'): ?>selected<?php endif; ?>>已支付
								</option>
								<option value="1" <?php if($params['isPay'] == '1'): ?>selected<?php endif; ?>>未支付
								</option>
							</select>
						</td>

						<td align="left">发货状态：</td>
						<td>
							<select name="isReceipt" class="combox">
								<option value="">请选择</option>
								<option value="1" <?php if($params['isReceipt'] == '1'): ?>selected<?php endif; ?>>已完成
								</option>
								<option value="2" <?php if($params['isReceipt'] == '2'): ?>selected<?php endif; ?>>已发货
								</option>
								<option value="3" <?php if($params['isReceipt'] == '3'): ?>selected<?php endif; ?>>等待发货
								</option>
								<option value="4" <?php if($params['isReceipt'] == '4'): ?>selected<?php endif; ?>>带评论
								</option>
							</select>
						</td>
						<td>发货时间：
							<input type="text" name="" style="width: 80px;" class="date" readonly="true" datefmt="yyyy-MM-dd" value="" />
						</td>
						<td align="left">收货状态：</td>
						<td>
							<select name="isShip" class="combox">
								<option value="">请选择</option>
								<option value="1" <?php if($params['isShip'] == '1'): ?>selected<?php endif; ?>>未签收
								</option>
								<option value="2" <?php if($params['isShip'] == '2'): ?>selected<?php endif; ?>>已签收
								</option>
							</select>
						</td>
						<td>收货时间：
							<input type="text" name="" style="width: 80px;" class="date" readonly="true" datefmt="yyyy-MM-dd" value="" />
						</td>
					</td>

					<td align="left">
						<div class="buttonActive">
							<div class="buttonContent">
								<button type="submit">检索</button>
							</div>
						</div>
					</td>
				</tr>
				</tr>
			</table>
		</div>
	</form>
</div>
<script>
	//new $.dynSelect({inputID:'businessName',dataUrl:"<?php echo U('Business/getBusName');?>"});
</script>

<div class="pageContent">
	<div class="panelBar">
		<ul class="toolBar">
			<li class="line">line</li>
			<li>
				<a class="edit" href="<?php echo U('Order/send');?>?id={id}" target="dialog" width="600" height="350" mask="true" rel="add" title="发货">
					<span>发货</span>
				</a>
			</li>
			<li class="line">line</li>
			<li>
				<a class="edit" href="<?php echo U('Order/delivery');?>?id={id}" target="dialog" width="600" height="350" mask="true " rel="add" title="修改发货信息">
					<span>修改发货信息</span>
				</a>
			</li>
			<li class="line">line</li>
			<li>
				<a class="edit" href="<?php echo U('Order/detail');?>?id={id}" target="dialog" width="1024" height="650" mask="true " rel="add" title="详情">
					<span>详情</span>
				</a>
			</li>
			<li class="line">line</li>
			<li>
				<a class="edit" href="<?php echo U('Order/statistics');?>" target="dialog" width="1024" height="768" mask="true " rel="add" title="（<?php echo ($startDate); ?>）至（<?php echo ($endDate); ?>）订单统计">
					<span>订单统计</span>
				</a>
			</li>
			<li class="line">line</li>
			<li>
				<a class="edit" href="<?php echo U('Order/refund');?>?id={id}" target="dialog" width="500" height="400" mask="true " rel="edit" title="退押金">
					<span>退押金</span>
				</a>
			</li>
			<li class="line">line</li>
		</ul>
	</div>
	<table class="table" width="100%" layoutH="141">
		<thead>
			<tr>
				<th width="3%" align="center">序号</th>
				<?php if($checkUser == 1): ?><th width="5%" align="center">平台</th><?php endif; ?>
				<th width="5%" align="center">商户姓名</th>
				<th width="5%" align="center">上级</th>
				<th width="5%" align="center">发货人</th>
				<th width="7%" align="center">订单号</th>
				<th width="4%" align="center">商品金额</th>
				<th width="4%" align="center">商品支付</th>
				<th width="4%" align="center">押金金额</th>
				<th width="4%" align="center">押金支付</th>
				<th width="4%" align="center">支付金额</th>
				<th width="4%" align="center">支付状态</th>
				<th width="6%" align="center">支付时间</th>
				<th width="4%" align="center">发货状态</th>
				<th width="6%" align="center">发货时间</th>
				<th width="5%" align="center">收货状态</th>
				<th width="6%" align="center">收货时间</th>
				<th width="5%" align="center">激活状态</th>
				<th width="5%" align="center">押金退还状态</th>
				<!-- <th width="5%" align="center">产品信息</th>
				<th width="5%" align="center">商户信息</th>
				<th width="5%" align="center">收货人信息</th>
				<th width="6%" align="center">物流信息</th> -->
				<th width="4%" align="center">订单状态</th>
				<th width="8%" align="center">订单时间</th>
			</tr>
		</thead>
		<?php if($totalCount > 0): ?><tbody>
				<?php if(is_array($resArray)): $i = 0; $__LIST__ = $resArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr target="id" rel="<?php echo ($v["id"]); ?>">
						<td><?php echo ($i); ?></td>
						<?php if($checkUser == 1): ?><td style="font-weight: bold;"><?php echo ($v["platu"]); ?></td><?php endif; ?>
						<td><?php echo ($v["busname"]); ?></td>
						<td><?php echo ($v["vname"]); ?></td>
						<td><?php echo ($v["consignor"]); ?></td>
						<td><?php echo ($v["ordernum"]); ?></td>
						<td><?php echo ($v["orderMoney"]); ?></td>
						<td>
							<?php if($v['isPay'] == 1): ?><span style="color: #006600; font-weight: bold;"><?php echo ($v["pstatus"]); ?></span><?php endif; ?>
							<?php if($v['isPay'] == 2): ?><span style="color: #660000; font-weight: bold;"><?php echo ($v["pstatus"]); ?></span><?php endif; ?>
							<?php if($v['isPay'] == 3): ?><span style="color: #660000; font-weight: bold;"><?php echo ($v["pstatus"]); ?></span><?php endif; ?>
						</td>
						<td><?php echo ($v["depositMoney"]); ?></td>
						<td>
							<?php if($v['isDeposit'] == 1): ?><span style="color: #006600; font-weight: bold;"><?php echo ($v["dstatus"]); ?></span><?php endif; ?>
							<?php if($v['isDeposit'] == 2): ?><span style="color: #660000; font-weight: bold;"><?php echo ($v["dstatus"]); ?></span><?php endif; ?>
							<?php if($v['isDeposit'] == 3): ?><span style="color: #660000; font-weight: bold;"><?php echo ($v["dstatus"]); ?></span><?php endif; ?>
						</td>
						<td><?php echo ($v["wxpayMoney"]); ?></td>
						<td>
							<?php if($v['wxIsPay'] == 1): ?><span style="color: #660000; font-weight: bold;"><?php echo ($v["wstatus"]); ?></span><?php endif; ?>
							<?php if($v['wxIsPay'] == 2): ?><span style="color: #006600; font-weight: bold;"><?php echo ($v["wstatus"]); ?></span><?php endif; ?>
							<?php if($v['wxIsPay'] == 3): ?><span style="color: #660000; font-weight: bold;"><?php echo ($v["wstatus"]); ?></span><?php endif; ?>
						</td>
						<td><?php echo ($v["payTime"]); ?></td>
						<td>
							<?php if($v['isReceipt'] == ''): ?><span style="color: #666666; font-weight: bold;"><?php echo ($v["rstatus"]); ?></span><?php endif; ?>
							<?php if($v['isReceipt'] == 1): ?><span style="color: #660000; font-weight: bold;"><?php echo ($v["rstatus"]); ?></span><?php endif; ?>
							<?php if($v['isReceipt'] == 2): ?><span style="color: #006600; font-weight: bold;"><?php echo ($v["rstatus"]); ?></span><?php endif; ?>
							<?php if($v['isReceipt'] == 3): ?><span style="color: #660000; font-weight: bold;"><?php echo ($v["rstatus"]); ?></span><?php endif; ?>
							<?php if($v['isReceipt'] == 4): ?><span style="color: #663300; font-weight: bold;"><?php echo ($v["rstatus"]); ?></span><?php endif; ?>
						</td>
						<td>
							<?php if($v['isReceipt'] == ''): ?><span style="color: #660000; font-weight: bold;"><?php echo ($v["receiptTime"]); ?></span>
								<?php else: ?>
								<?php if($v['isReceipt'] == 3): ?><span style="color: #660000; font-weight: bold;"><?php echo ($v["receiptTime"]); ?></span>
									<?php else: ?> <?php echo ($v["receiptTime"]); endif; endif; ?>
						</td>
						<td><?php echo ($v["sstatus"]); ?></td>
						<td><?php echo ($v["shipTime"]); ?></td>
						<td><?php echo ($v["jhzt"]); ?></td>
						<td><?php echo ($v["returned"]); ?></td>
						<!-- <td><a href="#" class="bankCard" onclick="tipThis(this, '商品分类：<?php echo ($v['pcname']); ?>', '商品名称：<?php echo ($v['pname']); ?>', '终端号：<?php echo ($v['terminalNumber']); ?>' , 1)"> <span style="color: #666600; font-weight: bold; line-height: 22px;">（查看）</span>
				</a></td>
				<td><a href="#" class="bankCard" onclick="tipThis(this, '商户名称：<?php echo ($v['busname']); ?>', '商户电话：<?php echo ($v['phone']); ?>',2 , 2)"> <span style="color: #666600; font-weight: bold; line-height: 22px;">（查看）</span>
				</a></td>
				<td><a href="#" class="bankCard" onclick="tipThis(this, '<?php echo ($v['consignee']); ?>', '收货人电话：<?php echo ($v['consigneePhone']); ?>','收货地址：<?php echo ($v['address']); ?>' , 1)"> <span style="color: #666600; font-weight: bold; line-height: 22px;">（查看）</span>
				</a></td>
				<td>
					<?php if($v['wxIsPay'] == 1): ?><span style="color: #666666; font-weight: bold;"><?php echo ($v["courier"]); ?></span>
					<?php else: ?>
						<?php if($v['isReceipt'] == 3): ?><span style="color: #660000; font-weight: bold;"><?php echo ($v["courier"]); ?></span>
						<?php else: ?>
						<a href="#" class="bankCard" onclick="tipThis(this, '<?php echo ($v['courier']); ?>', 2,2 , 3)"> <span style="color: #666600; font-weight: bold; line-height: 22px;">（查看）</span><?php endif; endif; ?>
				
				</a></td> -->
						<td>
							<?php if($v['isOrder'] == 1): ?><span style="color: #006600; font-weight: bold;"><?php echo ($v["ostatus"]); ?></span><?php endif; ?>
							<?php if($v['isOrder'] == 2): ?><span style="color: #000066; font-weight: bold;"><?php echo ($v["ostatus"]); ?></span><?php endif; ?>
							<?php if($v['isOrder'] == 3): ?><span style="color: #660000; font-weight: bold;"><?php echo ($v["ostatus"]); ?></span><?php endif; ?>
						</td>
						<td><?php echo ($v["orderTime"]); ?></td>
					</tr><?php endforeach; endif; else: echo "" ;endif; ?>
			</tbody>
			<?php else: ?>
			<tbody>
				<tr>
					<td align="center" colspan="14" style="color: red;">抱歉，没有找到符合的记录！</td>
				</tr>
			</tbody><?php endif; ?>
	</table>
	<div class="panelBar tongji">
		<div class="pages tongji_1">
			<span>用户总数：共<?php echo ($totalCount); ?></span>
		</div>
	</div>
	<style>
		.layui-layer-content {
			margin-top: -11px;
		}

		.layui-layer-content img {
			float: left;
			margin-top: 2px;
		}

		.layui-layer.layui-anim.layui-layer-tips,
		.layui-layer-content {
			height: auto !important;
		}
	</style>
	<style>
		.tongji {
			padding-left: 40%;
		}

		.tongji .tongji_1 {
			padding: 0 0 0 20px;
			text-align: center;
		}

		.tongji .tongji_1 span {
			color: red;
		}
	</style>
	<div class="panelBar">
		<div class="pages">
			<span>显示</span>
			<select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage: this.value})">
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
<script>
	/**
	 *   查看信息
	 */
	function tipThis(obj, n, i, b, t) {
		var $str = '（' + n + '）（' + i + '）';
		if (t == 1) {
			$str = '（' + n + '）（' + i + '）（' + b + '）';
		}
		if (t == 3) {
			$str = '（' + n + '）';
		}
		layer.tips($str, $(obj), {
			tips: [3, 'green'],
			area: ['380px', '23px'],
			offset: "", //右下角弹出
			time: 2000
		});
	}
	/**
	 * 提现卡 信息
	 */
	function drawDankDetail(obj, id) {
		$.ajax({
			url: '<?php echo U("Business/ajaxBankDetail");?>',
			type: "POST",
			dataType: 'Json',
			data: {
				"bids": id
			},
			success: function (ret) {
				if (ret.status == 1) {
					var $d = ret.data;
					layer.tips($d.name + '　---　' + $d.card_number + '　---　' +
						$d.bank_name, $(obj), {
							tips: [2, 'green'],
							area: ['380px', '23px'],
							offset: "", //右下角弹出
							time: 3000
						});
				} else {
					alertMsg.warn(ret.msg);
				}
			}
		});
	}

	function changeClerk(id, type) {
		alertMsg.confirm("是否修改业务员状态", {
			okCall: function () {
				ajaxTodo("<?php echo U('changeClerk');?>?id=" + id + "&type=" + type);
			}
		});
	}
</script>