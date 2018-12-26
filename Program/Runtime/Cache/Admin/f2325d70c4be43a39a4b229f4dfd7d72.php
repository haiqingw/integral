<?php if (!defined('THINK_PATH')) exit();?><style>
.ryqRow .ryqContainerHeight {
	position: relative;
}

.ryqRow .ryqContainerLeft {
	float:left;
	width: 50%;
	height: 50%;
}

.ryqRow .fuckRyq .combox {
	margin-top: 4px;
}

.ryqRow .ryqContainerRight {
	clear:both;
	width: 100%;
	height: 100%;
}

.ryqRow .panelContent {
	border-bottom: 1px solid #99bbe8;
	overflow-x: hidden;
}

.rDivItem {
	overflow: hidden;
}

.rDivItem>p {
	line-height: 30px;
	font-weight: bold;
	background: #e4ebf6;
	color: #15428B;
	border: 1px solid #99bbe8;
	position: relative;
	padding-left: 10px;
}

.rDivItem>p a {
	height: 30px;
	line-height: 30px;
	width: 80px;
	text-align: center;
	position: absolute;
	right: 0;
	top: 0;
	background: #f33;
	color: #fff;
	text-decoration: none;
}

.rDivItem .tips {
	background: #fff;
	border-bottom: 1px solid #99bbe8;
	color: #3777DD;
}

.rDivItem .tips em {
	color: red;
	font-style: normal;
}

.rDivItem div.combox {
	float: left;
}

.rDivItem>input {
	width: 100%;
	padding: 0 10px;
	box-sizing: border-box;
	height: 40px;
	font-size: 16px;
}

.ryqContainer {
	background: #fff;
	border: 1px solid #99bbe8;
	padding: 10px;
	box-sizing: border-box;
}

.firstRDiveItem {
	line-height: 23px;
	font-weight: bold;
	background: #e4ebf6;
	color: #15428B;
	border: 1px solid #99bbe8;
	margin-bottom: 10px;
	padding-top: 1px;
}

.firstRDiveItem>p {
	background: none;
	border: none;
}

.rDivItem textarea {
	width: 100%;
	height: 200px;
	resize: none;
	padding: 5px;
	box-sizing: border-box;
	line-height: 20px;
	font-size: 14px;
	vertical-align: middle;
}

div.Rcombox {
	float: right;
	height: 23px;
	background-position: 100% -50px;
	margin-top: 3px;
	margin-right: 4px;
	border: 1px solid #B8D0D6;
	box-sizing: border-box;
	border-radius: 3px;
	background-color: #fff;
	background-position: 100% -50px;
}

.Rcombox select {
	border-radius: 3px;
	text-align:center;
	font-size: 12px;
	height: 23px;
	line-height: 23px;
	appearance: none;
	-moz-appearance: none;
	/*  -webkit-appearance:none; */
	background: none;
	border: none;
	color: #15428B;
}
</style>
<div layoutH="0">
	<div class="row ryqRow">
		<div class="ryqContainerLeft">
			<div class="panel collapse">
				<h1>机具连号入库</h1>
				<div>
					<div class='ryqContainer fuckRyq'>
						<!-- start -->
						<div class="rDivItem firstRDiveItem">
							<p style='float: left;'>请选择入库产品：</p>
							<select class="combox" name="product" ref="template" refUrl="<?php echo U('findTemplate');?>?id={value}">
								<option value="">请选择</option>
								<?php if(is_array($product)): $i = 0; $__LIST__ = $product;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
							</select>
							<select class="combox" id="template" name="template" >
								<option value="">请选择返现模板</option>
							</select>
						</div>
						<div class="rDivItem">
							<p>
								机具终端号/SN号：
								<a href="javascript:void(0);" class='rSubmitButton' style="background:#3777DD">入库</a>
								<input type="text" class="putInStart digits" placeholder='开始' /> <em>~</em> <input type="text" class="putInEnd digits" placeholder='结束' />
							</p>
							<p class='tips'>
								<em>★</em> 连号入库
							</p>
						</div>
						<!-- end -->
					</div>
				</div>
			</div>
		</div>
		<div class="ryqContainerLeft">
			<div class="panel collapse">
				<h1>机具扫码入库</h1>
				<div>
					<div class='ryqContainer fuckRyq'>
						<!-- start -->
						<div class="rDivItem firstRDiveItem">
							<p style='float: left;'>请选择入库产品：</p>
							<select class="combox" name="productID" id="productID" ref="template1" refUrl="<?php echo U('findTemplate');?>?id={value}">
								<option value="">请选择</option>
								<?php if(is_array($product)): $i = 0; $__LIST__ = $product;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>"<?php if($vo['id'] == $productID): ?>selected="selected"<?php endif; ?>><?php echo ($vo["name"]); ?>
								</option><?php endforeach; endif; else: echo "" ;endif; ?>
							</select>
							<select class="combox" id="template1" name="template1" >
								<?php if(!empty($cashid)): ?><option value="<?php echo ($cashid); ?>">模板ID:<?php echo ($cashid); ?></option>
								<?php else: ?>
								<option value="">请选择返现模板</option><?php endif; ?>
							</select>
						</div>
						<div class="rDivItem  ">
							<p style="border: 1px solid #99bbe8; ">
								机具终端号/SN号：<a href="javascript:smPutIn('termanal');">扫码录入</a>
								<input type="text" autofocus="autofocus"  class="termanal" id="termanal" size="42" placeholder='扫码入库' onchange="sweepCode()" />
							</p>
							<p class='tips' style="border: 1px solid #99bbe8;color:red;font-weight:bold;">
								<em>★</em> 扫码入库 ，不能批量输入!（建议使用扫码枪）如果没有扫码枪，输完终端后回车录入。
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="ryqContainerRight">

			<div class="panel collapse">
				<h1>机具列表</h1>
				<div>
					<!-- start -->
					<form id="pagerForm" method="post" action="<?php echo U('machineIn');?>" onsubmit="return navTabSearch(this);">
						<input type="hidden" name="keywords" value="<?php echo ($keywords); ?>" /> <input type="hidden" name="pageNum" value="<?php echo ($page); ?>" /> <input type="hidden" name="startDate" value="<?php echo ($startDate); ?>" /> <input type="hidden" name="endDate" value="<?php echo ($endDate); ?>" /> <input type="hidden" name="cid" value="<?php echo ($cid); ?>" /> <input type="hidden" name="useStatus" value="<?php echo ($useStatus); ?>" /> <input type="hidden" name="allotStatus" value="<?php echo ($allotStatus); ?>" /> <input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>" /> <input type="hidden" name="asc" value="<?php echo ($asc); ?>" /> <input type="hidden" name="orderField" value="<?php echo ($orderField); ?>" /> <input type="hidden" name="obj_name" value="<?php echo ($obj_name); ?>" /> <input type="hidden" name="obj_id" value="<?php echo ($obj_id); ?>" /> <input type="hidden" name="pr" id="pr" value="<?php echo ($productID); ?>" />
						<input type="hidden" name="customer" id="customer" value="<?php echo ($customer); ?>" />
					</form>

					<div class="pageHeader">
						<form onsubmit="return navTabSearch(this);" action="<?php echo U('machineIn');?>" method="post" id="machineIn">
							<input type="hidden" name="shaomaproductid" id="shaomaproductid" value="" />
							<input type="hidden" name="shaomacashid" id="shaomacashid" value="" />
							<div class="searchBar">
								<table class="searchContent">
									<tr>
										<td align="left">入库产品：</td>
										<td align="right"><select class="combox" name="cid" id="cid">
												<option value="">请选择</option>
												<?php if(is_array($product)): $i = 0; $__LIST__ = $product;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
										</select></td>
										<td><label>调拨状态：</label><select name="allotStatus" id="allotStatus" class="combox">
												<option value="">请选择</option>
												<option value="1"<?php if($allotStatus == 1): ?>selected="selected"<?php endif; ?>>未调拨
												</option>
												<option value="2"<?php if($allotStatus == 2): ?>selected="selected"<?php endif; ?>>已调拨
												</option>
										</select></td>
										<td><label>使用状态：</label><select name="useStatus" id="useStatus" class="combox">
												<option value="">请选择</option>
												<option value="1"<?php if($useStatus == 1): ?>selected="selected"<?php endif; ?>>未使用
												</option>
												<option value="2"<?php if($useStatus == 2): ?>selected="selected"<?php endif; ?>>已使用
												</option>
										</select></td>
										<td align="right">所属商户：<input type="text" value="<?php echo ($obj_name); ?>" id="belongName" name="obj.name" lookupPk="name" autocomplete="off" placeholder="点击选择" onclick="openSearchBack('#searchBack')" style="width: 100px; text-align: center; cursor: pointer" readonly /> <input name="obj.id" id="belongID" value="<?php echo ($obj_id); ?>" type="hidden" /> <a href="<?php echo U('searchBack');?>" lookupGroup="obj" id="searchBack" width="600" height="400" rel="modify" title="所属商户" style="display: none;"></a> <a href="javascript:clearBelong()" style="color: skyblue">clear</a>
										</td>
										<td align="right">关键词：<input type="text" value="<?php echo ($keywords); ?>" id="keywords" name="keywords" autocomplete="off" placeholder="终端号,批次号" style="width: 160px;" />
										</td>
										<td align="right">客户：<input type="text" value="<?php echo ($customer); ?>" id="customer" name="customer" autocomplete="off" placeholder="客户姓名、电话" style="width: 160px;" />
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
							</div>
						</form>
					</div>
					<div class="pageContent">
						<div class="panelBar">
							<ul class="toolBar">
								<li><a class="edit" href="<?php echo U('MachineManage/mtemplate');?>?id={id}" target="dialog" rel="jjtb" title="修改返现模板" wwidth="600" height='400' mask="true"> <span>修改返现模板</span>
								</a></li>
								<li><a class="edit" href="<?php echo U('MachineManage/batch');?>" target="dialogBat" rel="ids" title="批量调拨机具" width="700" height='700' mask="true"> <span>批量勾选-调拨</span>
								</a></li>
								<li class="line"></li>
								<li><a class="edit" href="<?php echo U('MachineManage/batchdial');?>" target="dialog" rel="jjtb" title="批量调拨机具" width="700" height='700' mask="true"> <span>连号-调拨</span>
								</a></li>
								<li class="line"></li>
								<li><a class="edit" href="<?php echo U('MachineManage/onetransfers');?>" target="dialog" rel="jjtb" title="机具调拨-按终端号" width="600" height='400' mask="true"><span>按终端-调拨</span></a></li>
								<li class="line">line</li>
								<li><a class="edit" href="<?php echo U('MachineManage/transfers');?>" target="dialog" rel="jjtb" title="机具调拨-按批次" width="700" height='550' mask="true"><span>按批次-调拨</span></a></li>
								<li class="line">line</li>
								<li><a class="edit" href="<?php echo U('MachineManage/modify');?>?id={id}" target="dialog" rel="jjtb" title="修改调拨" width="600" height='400' mask="true"><span>修改调拨</span></a></li>
								<li class="line">line</li>
								<li><a class="delete" href="<?php echo U('MachineManage/batchDel');?>" target="selectedTodo" rel="ids" postType="string" title="确定要删除机具么,(不删除已拨码机具)"><span>批量-删除</span></a></li>
								<li class="line">line</li>
								<li><a class="delete" href="<?php echo U('MachineManage/del');?>" target="dialog" rel="jjtb" title="按批次删除机具" width="600" height='400' mask="true"><span>按批次-删除</span></a></li>
								<li class="line">line</li>
								<li><a class="delete" href="<?php echo U('MachineManage/deleteMachine');?>?id={id}" target="ajaxTodo" rel="" title="删除机具"><span>单机具-删除</span></a></li>
								<li class="line">line</li>
								<li><a class="edit" href="<?php echo U('MachineManage/BackYards');?>?id={id}" target="ajaxTodo" rel="" title="此功能仅限于已调拨未使用机具的回库，核对信息，确认退码"><span>退码</span></a></li>
								<li class="line">line</li>
								<li><a class="edit" href="<?php echo U('MachineManage/changemachine');?>?id={id}" target="dialog" rel="jjtb" title="调换已使用机具" width="600" height='610' mask="true"><span>调换已使用机具</span></a></li>
								<li class="line">line</li>
								<li><a class="edit" href="<?php echo U('MachineManage/machineBack');?>?id={id}" target="ajaxTodo" rel="" title="此功能，一个人多装机具或装错机具时退回原样，退回确认之前核对好信息，避免误操作，确定要退回机具么，"><span>退回已使用机具</span></a></li>
								<li class="line">line</li>
							</ul>
						</div>
						<table class="table" width="100%" layoutH="320">
							<thead>
								<tr>
									<th align="center" width="22"><input type="checkbox" group="ids" class="checkboxCtrl"></th>
									<th align="center">序号</th>
									<th align="center">产品名称</th>
									<th align="center">终端号</th>
									<th align="center">返现模板</th>
									<th align="center">批次号</th>
									<th align="center">调拨状态</th>
									<th align="center">所属服务商</th>
									<th align="center">调拨时间</th>
									<th align="center">使用状态</th>
									<th align="center">使用者</th>
									<th align="center">使用时间</th>
									<th align="center">激活状态</th>
									<th align="center">入库时间</th>
									<th align="center">拨码位置</th>
									<th align="center">拨码人</th>
									<th align="center">机具状态</th>
								</tr>
							</thead>
							<?php if($totalCount > 0): ?><tbody>
								<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr target="id" rel="<?php echo ($v["id"]); ?>">
									<td><input name="ids" value="<?php echo ($v["id"]); ?>" type="checkbox"></td>
									<td align="center"><?php echo ($i+$serial); ?></td>
									<td align="center"><?php echo ($v["commodityName"]); ?></td>
									<td align="center"><?php echo ($v["terminalNo"]); ?></td>
									<td align="center">模板ID:<?php echo ($v["cbrID"]); ?>
										<?php if($v['cbrstatus'] == 1): ?><span style="color:#228B22;float:right;padding-right:5px;display: block;line-height:23px;"><?php endif; ?>
										<?php if($v['cbrstatus'] == 2): ?><span style="color:#000000;float:right;padding-right:5px;display: block;line-height:23px;"><?php endif; ?>
										<?php if($v['cbrstatus'] == 3): ?><span style="color:#B22222;float:right;padding-right:5px;display: block;line-height:23px;"><?php endif; ?>
											(<?php echo ($v["cbrstat"]); ?>)</span>
									
									</td>
									<td align="center"><?php echo ($v["batchNo"]); ?></td>
									<td align="center"><?php if($v['allotStatus'] == 1): ?>未调拨<?php else: ?>已调拨<?php endif; ?></td>
									<td align="center"><?php if($v['allotStatus'] == 1): ?>未调拨<?php else: echo ($v["belongName"]); ?>(<?php echo ($v["belongPhone"]); ?>)<?php endif; ?></td>
									<td align="center"><?php if($v['allotStatus'] == 1): ?>未调拨<?php else: echo (dateformat($v["allotTime"],2)); endif; ?></td>
									<td align="center"><?php if($v['useStatus'] == 1): ?>未使用<?php else: ?>已使用<?php endif; ?></td>
									<td align="center"><?php if($v['useStatus'] == 1): ?>未使用<?php else: echo ($v["useName"]); endif; ?></td>
									<td align="center"><?php if($v['useStatus'] == 1): ?>未使用<?php else: echo (dateformat($v["useTime"],2)); endif; ?></td>
									<td style="font-weight: bold">
										<?php if($v['isActive'] == 3): ?><span style="color:#4B0082;"><?php endif; ?>
										<?php if($v['isActive'] == 1): ?><span style="color:#DC143C;"><?php endif; ?>
										<?php if($v['isActive'] == 2): ?><span style="color:#006600;"><?php endif; ?>
										<?php echo ($v["isAct"]); ?></span></td>
									<td align="center"><?php echo (dateformat($v["createTime"],2)); ?></td>
									<td align="center"><?php if($v['allot'] == 2): ?><spanv style="color:#006600;font-weight:bold;"><?php echo ($v["allotes"]); ?></span>（<a href="<?php echo U('MachineManage/dialdetail');?>?id=<?php echo ($v["id"]); ?>&productID=<?php echo ($v["productID"]); ?>" target="dialog" rel="MachineManage/dialdetail" title="拨码记录" width="1024" height='768' mask="true"><span style="color: #993300; font-weight: bold;">拨码记录</span></a>）<?php else: ?> <span style="color: #006699; font-weight: bold;"><?php echo ($v["allotes"]); ?></span><?php endif; ?></td>
									<td align="center"><?php if($v['allot'] == 2): ?><span style="color: #006600; font-weight: bold;"><?php echo ($v["codeMan"]); ?></span> <?php else: ?> <span style="color: #006699; font-weight: bold;"><?php echo ($v["codeMan"]); ?></span><?php endif; ?></td>
									<td align="center"><?php echo ($v["mstatus"]); ?></td>
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
					<!-- end -->
				</div>
			</div>

		</div>
	</div>
</div>
<script>
	//验证是否为数字
	function isNumber(value) {
		var patrn = /^(-)?\d+(\.\d+)?$/;
		if (patrn.exec(value) == null || value == "") {
			return false
		} else {
			return true
		}
	}
	function clearBelong() {
		$('#belongID,#belongName').val("");
	}
	var $timeout = null;
	$(function() {
		$("#termanal").focus();
		hasA();
		setTimeout(function() {
			clearTimeout($timeout);
		}, 3000);
		$('.rSubmitButton').click(function() {
					var obj = {};
					var product = $('select[name=product]').val();
					if (product == "") {
						alertMsg.warn('请选择入库产品！')
						return;
					}
					obj.product = product;
					var template = $('select[name=template]').val();
					if (template == "") {
						alertMsg.warn('请选择返现模板！')
						return;
					}
					obj.cbrID= template;
					var start = $('.putInStart').val();
					var end = $('.putInEnd').val();
					var p = /[a-z]/i;
					if (p.test(start) || p.test(end)) {
						var p_start = start.replace(/[^a-z]+/ig, "");
						var p_end = end.replace(/[^a-z]+/ig, "");
						if (p_start != p_end) {
							alertMsg.warn('前缀不统一请选择扫码入库！')
							return;
						} else {
							var check_start = start.substr(start
									.lastIndexOf(p_start)
									+ p_start.length);
							var check_end = end.substr(end.lastIndexOf(p_end)
									+ p_end.length);
							if (check_start > check_end) {
								alertMsg.warn('连号入库开始不能大于结束！')
								return;
							} else {
								var s = check_end - check_start;
								if (Number(s) > 50) {
									alertMsg.warn('连号入库最多只能入库50个！')
									return;
								}
							}

						}
					}else{
						if (start > end) {
							alertMsg.warn('连号入库开始不能大于结束！')
							return;
						} else {
							var s = end - start;
							if (Number(s) > 50) {
								alertMsg.warn('连号入库最多只能入库50个！')
								return;
							}
						}
					}
					obj.start = start;
					obj.end = end;
					if (start == "" && end == "") {
						alertMsg.error('对不起，没有机具要入库！')
						return;
					}
					$.ajax({
						url : "<?php echo U('putInProcess');?>",
						type : "post",
						data : obj,
						dataType : "json",
						success : function(ret) {
							if (ret.status) {
								alertMsg.correct(ret.msg);
								$('#machineIn').submit();
							} else {
								alertMsg.error("入库失败");
							}
						}
					}); 
				});
	});
	//扫码入库
	function sweepCode() {
		var obj = {};
		//产品
		var product = $('select[id=productID]').val();
		if (product == "") {
			alertMsg.warn('请选择入库产品！');
			$("#termanal").val('');
			return;
		}
		obj.product = product;
		var template1 = $('select[name=template1]').val();
		if (template1 == "") {
			alertMsg.warn('请选择返现模板！')
			return;
		}
		obj.cbrID= template1;
		//终端号
		var termanal = $('#termanal').val();
		if (termanal == "") {
			alertMsg.warn('请选择入库产品！')
			return;
		}
		obj.termanal = termanal;
		$.ajax({
			url : "<?php echo U('MachineManage/sweepCode');?>",
			type : "post",
			data : obj,
			dataType : "json",
			success : function(ret) {
				if (ret.status) {
					$("#termanal").val('');
					alertMsg.correct(ret.msg);
					//localStorage.proccc = ret.pica;
					$("#shaomaproductid").val(ret.pica);
				 	$("#shaomacashid").val(ret.cashid);
					$('#machineIn').submit();
				} else {
					alertMsg.error(ret.msg);
				}
			}
		});
	}
	function openSearchBack(id) {
		$(id).trigger('click');
	}
	function smPutIn(c) {
		$('.' + c).focus();
	}
	function hasA() {
		$('a.collapsable').remove();
		$timeout = setTimeout(function() {
			hasA();
		}, 500);
	}
	function ready(fn) {
		if (document.addEventListener) {
			document.addEventListener('DOMContentLoaded', function() {
				document.removeEventListener('DOMContentLoaded',
						arguments.callee);
				fn();
			});
		} else if (document.attachEvent) {
			document.attachEvent('onreadystatechange', function() {
				if (document.readystate == 'complete') {
					document.dispatchEvent('onreadystatechange',
							arguments.callee);
					fn();
				}
			})
		}
	}
</script>