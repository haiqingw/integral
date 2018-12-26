<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" method="post" action="<?php echo U('Bank/index');?>">
	<input type="hidden" name="status" value="${param.status}">
	<input type="hidden" name="sssDate" value="<?php echo ($sssDate); ?>" />
	<input type="hidden" name="eeeDate" value="<?php echo ($eeeDate); ?>" />
	<input type="hidden" name="bkeywords" value="<?php echo ($params["bkeywords"]); ?>" />
	<input type="hidden" name="xstatus" value="<?php echo ($params["xstatus"]); ?>" />
	<input type="hidden" name="pageNum" value="<?php echo ($page); ?>" />
	<input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>" />
</form>
<div class="pageHeader">
	<form id="form" action="<?php echo U('Bank/index');?>" onsubmit="return navTabSearch(this);" method="post">
		<div class="searchBar" style="margin-left: 2%">
			<table class="searchContent" style="margin-top: 8px;">
				<tr>
					<td>
						<label>添加时间：</label>
						<input type="text" name="sssDate" class="date" size="15" readonly="true" datefmt="yyyy-MM-dd" value="<?php echo ($sssDate); ?>"
						    placeholder="开始时间" style="text-align: center;" /> ~
						<input type="text" name="eeeDate" class="date" readonly="true" size="15" datefmt="yyyy-MM-dd" value="<?php echo ($eeeDate); ?>"
						    placeholder="结束时间" style="text-align: center;" />
					</td>
					<td>
						<label>关键字：</label>
						<input type="text" name="bkeywords" style="width: 160px; text-align: center;" value="<?php echo ($params["bkeywords"]); ?>"
						    placeholder="用户姓名,联系电话" />
					</td>
					<td>
						<label>状态：</label>
						<select name="xstatus" class="combox">
							<option value="">请选择</option>
							<option value="1" <?php if($params['xstatus'] == 1): ?>selected="selected"<?php endif; ?>>正常
							</option>
							<option value="2" <?php if($params['xstatus'] == 2): ?>selected="selected"<?php endif; ?>>冻结
							</option>
							<option value="3" <?php if($params['xstatus'] == 3): ?>selected="selected"<?php endif; ?>>失败
							</option>

						</select>
					</td>
					<td align="left">
						<div class="buttonActive">
							<div class="buttonContent">
								<button type="submit">检索</button>
							</div>
						</div>
					</td>
				</tr>
			</table>
			<table class="searchContent" style="margin-top: 8px;">
				<tr></tr>
			</table>
		</div>
	</form>
</div>

<div class="pageContent">
	<div class="panelBar">
		<ul class="toolBar">
			<li>
				<a class="edit" href="<?php echo U('Bank/restore');?>?id={sid}" target="ajaxTodo" title="确认已删除 ，恢复操作!">
					<span>恢复</span>
				</a>
			</li>
			<li class="line"></li>
			<li>
				<a class="add" href="<?php echo U('Bank/add');?>" target="dialog" width="450" height="320" mask="true" target="dialog" drawable="false"
				    maxable="false" minable="false" resizable="false" rel="bankadd" title="添加银行卡">
					<span>添加</span>
				</a>
			</li>
			<li class="line"></li>
			<li>
				<a class="edit" href="<?php echo U('Bank/modify');?>?id={sid}" target="dialog" width="450" height="320" mask="true" target="dialog"
				    drawable="false" maxable="false" minable="false" resizable="false" rel="bankadd" title="修改银行卡">
					<span>修改</span>
				</a>
			</li>
			<li class="line"></li>
		</ul>
	</div>
	<table width="100%" class="table" layoutH="160">
		<thead>
			<tr>
				<th width="4%" align="center">序号</th>
				<?php if($checkUser == 1): ?><th width="7%" align="center">平台</th><?php endif; ?>
				<th width="6%" align="center">商户名称</th>
				<th width="6%" align="center">商户电话</th>
				<th width="6%" align="center">持卡人姓名</th>
				<th width="9%" align="center">身份证号</th>
				<th width="10%" align="center">银行卡号</th>
				<th width="8%" align="center">所属银行</th>
				<th width="8%" align="center">开户行</th>
				<th width="8%" align="center">开户城市</th>
				<th width="8%" align="center">预留电话</th>
				<th width="5%" align="center">显示状态</th>
				<th width="5%" align="center">状态</th>
				<th width="8%" align="center">添加时间</th>
				<th width="8%" align="center">操作</th>

			</tr>
		</thead>

		<?php if($totalCount > 0): ?><tbody id="checkview">
				<?php if(is_array($bankArray)): $i = 0; $__LIST__ = $bankArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr target="sid" rel="<?php echo ($vo["id"]); ?>" data-desknum="">
						<td><?php echo ($i); ?></td>
						<?php if($checkUser == 1): ?><td style="font-weight: bold;"><?php echo ($vo["platu"]); ?></td><?php endif; ?>
						<td>
							<span style="color: #000000; font-weight: bold;"><?php echo ($vo['busname']); ?></span>
						</td>
						<td>
							<span style="color: #003366; font-weight: bold;"><?php echo ($vo['tel']); ?></span>
						</td>
						<td>
							<span style="color: #663333; font-weight: bold;"><?php echo ($vo['name']); ?></span>
						</td>
						<td>
							<span style="color: #000000; font-weight: bold;"><?php echo ($vo['idCard']); ?></span>
						</td>
						<td>
							<span style="color: #336699; font-weight: bold;"><?php echo ($vo['card_number']); ?></span>
						</td>
						<td><?php echo ($vo['bank_name']); ?></td>
						<td><?php echo ($vo['opening_bank']); ?></td>
						<td>
							<span style="color: #000000; font-weight: bold;"><?php echo ($vo['city']); ?></span>
						</td>
						<td>
							<span style="color: #000000; font-weight: bold;"><?php echo ($vo['phone']); ?></span>
						</td>
						<td>
							<?php if($vo['default_status'] == 1): ?><span style="color: #336666; font-weight: bold;">未默认</span><?php endif; ?>
							<?php if($vo['default_status'] == 2): ?><span style="color: #333333; font-weight: bold;">默认</span><?php endif; ?>
						</td>

						<td>
							<?php if($vo['status'] == 1): ?><span style="color: #006633; font-weight: bold;">正常</span><?php endif; ?>
							<?php if($vo['status'] == 2): ?><span style="color: #660033; font-weight: bold;">冻结</span><?php endif; ?>
							<?php if($vo['status'] == 3): ?><span style="color: #660033; font-weight: bold;">已删除</span><?php endif; ?>
						</td>
						<td><?php echo (date('Y/m/d H:i:s',$vo['addtime'])); ?></td>
						<td>
							<?php if($vo['status'] == 3): ?><span style="color: #660033; font-weight: bold;">已删除</span>
								<?php else: ?>
								<?php if($vo['status'] == 1): ?><a class="edit" id="" onclick="frozenThawed(1 , '<?php echo ($vo['id']); ?>' , '<?php echo ($vo['card_number']); ?>')" title="点击冻结">
										<span class='changeBankStatus'>冻结</span>
									</a>
									<?php elseif($vo['status'] == 2): ?>
									<a class="edit" id="" onclick="frozenThawed(2 , '<?php echo ($vo['id']); ?>' , '<?php echo ($vo['card_number']); ?>')" title="点击解冻">
										<span class='changeBankStatus_ca'>解冻</span>
									</a><?php endif; ?>
								<a class="edit" id="" onclick="frozenThawed(3 , '<?php echo ($vo['id']); ?>' , '<?php echo ($vo['card_number']); ?>')" title="点击冻结">
									<span class='changeBankStatus'>删除</span>
								</a><?php endif; ?>
						</td>
					</tr><?php endforeach; endif; else: echo "" ;endif; ?>
			</tbody>
			<?php else: ?>
			<tbody>
				<tr>
					<td align="center" colspan="8" style="color: red;">抱歉，没有找到符合的记录！</td>
				</tr>
			</tbody><?php endif; ?>
	</table>
	<div class="panelBar">
		<div class="pages">
			<span>显示</span>
			<select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage: this.value})">
				<option value="20" <?php if($Page['numPerPage'] == 30): ?>selected="selected"<?php endif; ?>>20
				</option>
				<option value="50" <?php if($Page['numPerPage'] == 50): ?>selected="selected"<?php endif; ?>>50
				</option>
				<option value="100" <?php if($Page['numPerPage'] == 100): ?>selected="selected"<?php endif; ?>>100
				</option>
				<option value="200" <?php if($Page['numPerPage'] == 200): ?>selected="selected"<?php endif; ?>>200
				</option>

			</select>
			<span>条，共<?php echo ($totalCount); ?>条</span>
		</div>

		<div class="pagination" targetType="navTab" totalCount="<?php echo ($totalCount); ?>" numPerPage="<?php echo ($numPerPage); ?>" pageNumShown="10" currentPage="<?php echo ($page); ?>"></div>
	</div>
</div>
<script>
	/**
	 * 冻结 / 解冻   / 删除  操作
	 */
	function frozenThawed(t, id, card) {
		var $title = "冻结该银行卡:" + card + ",是否冻结请确认 !"
		if (t == 2) {
			$title = "解冻该银行卡:" + card + ",是否解冻请确认 !"
		}
		if (t == 3) {
			$title = "删除该银行卡:" + card + ",是否删除请确认 !"
		}
		alertMsg.confirm($title, {
			okCall: function () {
				$.ajax({
					url: "<?php echo U('Bank/CloseOperat');?>",
					type: "POST",
					dataType: "JSON",
					async: false,
					data: {
						id: id,
						type: t,
						card: card
					},
					success: function (ret) {
						if (ret.status) {
							navTab.reload(); //刷新当前页 
							alertMsg.correct(ret.msg);
						} else {
							alertMsg.warn(ret.msg);
						}
					}
				});
			}
		});

	}
</script>
<style>
	.changeBankStatus_ca {
		position: relative;
		cursor: pointer;
		background: #6633CC;
		color: #FFFFFF;
		border: 1px solid gray;
		border-radius: 10px;
		box-shadow: 1px 1px 1px 1px rgba(0, 0, 0, 0.5);
		padding: 3px 5px;
		font-weight: bold;
		font-size: 11px;
	}

	.changeBankStatus_ca:hover {
		left: 1px;
		top: 1px;
	}

	.changeBankStatus {
		position: relative;
		cursor: pointer;
		background: #990000;
		color: #FFFFFF;
		border: 1px solid gray;
		border-radius: 10px;
		box-shadow: 1px 1px 1px 1px rgba(0, 0, 0, 0.5);
		padding: 3px 5px;
		font-weight: bold;
		font-size: 11px;
	}

	.changeBankStatus:hover {
		left: 1px;
		top: 1px;
	}
</style>