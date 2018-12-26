<?php if (!defined('THINK_PATH')) exit();?><div class="pageContent" style="padding: 5px">
	<!-- <div class="panel" defH="40"> -->
	<!-- </div> -->
	<div class="divider"></div>
	<div class="tabs">
		<div class="tabsHeader">
			<div class="tabsHeaderContent">
				<ul>
					<li>
						<a href="javascript:;">
							<span>收益返现</span>
						</a>
					</li>
				</ul>
			</div>
		</div>
		<div class="tabsContent">
			<div>
				<div layoutH="70" style="float: left; display: block; overflow: auto; width: 240px; border: solid 1px #CCC; line-height:
						21px; background: #fff">
					<ul class="tree treeFolder">
						<?php if(is_array($resArray)): $i = 0; $__LIST__ = $resArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$va): $mod = ($i % 2 );++$i;?><li>
								<a href="javascript" class="myTree"><?php echo ($va['category']); ?></a>
								<ul>
									<?php if(is_array($va['list'])): $i = 0; $__LIST__ = $va['list'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><li>
											<!-- <a href="<?php echo U('Cashlog/lists');?>?id=<?php echo ($v["id"]); ?>" target="ajax" rel="jbsxBox"><?php echo ($v["product"]); ?> -->
											<a class="myTreeA"><?php echo ($v["product"]); ?>
												<?php if($checkUser == 1): ?>（<?php echo ($v["platu"]); ?>）<?php endif; ?>
											</a>
											<ul>
												<?php if(is_array($v['data'])): $i = 0; $__LIST__ = $v['data'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><li><a href="<?php echo U('Cashlog/lists');?>?id=<?php echo ($v["id"]); ?>&types=<?php echo ($val["englishname"]); ?>&plat=<?php echo ($val["plat"]); ?>" target="ajax" rel="jbsxBoxBUSCASHlOG"><?php echo ($val["classname"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
											</ul>
										</li><?php endforeach; endif; else: echo "" ;endif; ?>
								</ul>
							</li><?php endforeach; endif; else: echo "" ;endif; ?>
					</ul>
				</div>
				<div id="jbsxBoxBUSCASHlOG" class="unitBox" style="margin-left: 246px;">
					<!--#include virtual="list1.html" -->
				</div>

			</div>
		</div>
		<div class="tabsFooter">
			<div class="tabsFooterContent"></div>
		</div>
	</div>
</div>
<script>
	$(function () {
		setTimeout(function () {
			$('.myTree').trigger('click');
			$('.myTreeA').trigger('click');
		}, 100);
	});
</script>