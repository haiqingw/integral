<?php if (!defined('THINK_PATH')) exit();?><style type="text/css">
	ul.rightTools {float:right; display:block;}
	ul.rightTools li{float:left; display:block; margin-left:5px}
</style>

<div class="pageContent" style="padding:5px">
	<!-- <div class="panel" defH="25">
		<h1>业务员拿返现推荐激活个数设置（0为不设限制，直接可拿返现）</h1>
		<div>
			<input type="text" id="slmt" value="<?php echo ($salesmanCashLimit); ?>" onkeyup="value=value.replace(/[^\d]/g,'')" maxlength=3 style="float:left;width:50px;text-align:center;"/>
			<a class="button" rel="saveSlmt" href="javascript:ajaxSlmt()"><span>保存</span></a>
		</div>
	</div>
	<div class="divider"></div>	 -->
	<div class="tabs">
		<div class="tabsHeader">
			<div class="tabsHeaderContent">
				<ul>
					<li><a href="javascript:;"><span>产品列表</span></a></li>
<!-- 					<li><a href="javascript:;"><span>病人处方</span></a></li>
					<li><a href="javascript:;"><span>病人服药情况</span></a></li>
					<li><a href="javascript:;"><span>基线调查</span></a></li>
					<li><a href="javascript:;"><span>随访</span></a></li> -->
				</ul>
			</div>
		</div>
		<div class="tabsContent">
			<div>
	
				<div layoutH="53" style="float:left; display:block; overflow:auto; width:240px; border:solid 1px #CCC; line-height:21px; background:#fff">
				    <ul class="tree treeFolder">
						<li><a href="javascript">产品列表</a>
							<ul>
								<?php if(is_array($category)): $i = 0; $__LIST__ = $category;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li><a href="<?php echo U('publicpage');?>?id=<?php echo ($vo["id"]); ?>" target="ajax" rel="jbsxBoxCb"><?php echo ($vo["brandName"]); ?>-<?php echo ($vo["productName"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
							</ul>
						</li>
						
				     </ul>
				</div>
				
				<div id="jbsxBoxCb" class="unitBox" style="margin-left:246px;">
					<!--#include virtual="list1.html" -->
				</div>
	
			</div>
			<!-- 
			<div>病人处方</div>
			
			<div>病人服药情况</div>
			
			<div>基线调查</div>
			
			<div>随访</div> -->
		</div>
		<div class="tabsFooter">
			<div class="tabsFooterContent"></div>
		</div>
	</div>
	<script>
		function ajaxSlmt(){
			var $slmt = $('#slmt').val();
			$.post("<?php echo U('saveSalesmanCashLimit');?>",{slmt:$slmt},function(res){
				alertMsg.correct('保存成功！');
			});
		}
	</script>
</div>