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
							<span>商户列表</span>
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
						<li>
							<a href="javascript">商户等级</a>
							<ul>
								<?php if(is_array($resArray)): $i = 0; $__LIST__ = $resArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><li><a href="<?php echo U('Business/lists');?>?plat=<?php echo ($plat); ?>&level=<?php echo ($v["englishname"]); ?>" target="ajax" rel="jbsxBoxBUSYSLISTCD"><?php echo ($v["classname"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
							</ul>
						</li>

					</ul>
				</div>

				<div id="jbsxBoxBUSYSLISTCD" class="unitBox" style="margin-left: 246px;">
					<!--#include virtual="list1.html" -->
				</div>

			</div>
		</div>
		<div class="tabsFooter">
			<div class="tabsFooterContent"></div>
		</div>
	</div>

</div>