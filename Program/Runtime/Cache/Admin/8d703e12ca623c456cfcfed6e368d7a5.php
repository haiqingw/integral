<?php if (!defined('THINK_PATH')) exit();?>
<style type="text/css">
	ul.rightTools {float:right; display:block;}
	ul.rightTools li{float:left; display:block; margin-left:5px}
</style>

<div class="pageContent" style="padding:5px">
	<div class="panel" defH="25">
		<h1>Excel模板设置</h1>
		<div>
			<ul class="leftTools">
				<li><a class="button" target="dialog" rel="addTemplate" href="<?php echo U('addTemplate');?>" mask="true"><span>创建模板</span></a></li>
				<li><div class="buttonDisabled"><div class="buttonContent"><button>修改模板</button></div></div></li>
				<li><div class="buttonDisabled"><div class="buttonContent"><button>删除模板</button></div></div></li>
			</ul>
		</div>
	</div>
	<div class="divider"></div>
	<div class="tabs">
		<div class="tabsHeader">
			<div class="tabsHeaderContent">
				<ul>
					<li><a href="javascript:;"><span>模板列表</span></a></li>
<!-- 					<li><a href="javascript:;"><span>病人处方</span></a></li>
					<li><a href="javascript:;"><span>病人服药情况</span></a></li>
					<li><a href="javascript:;"><span>基线调查</span></a></li>
					<li><a href="javascript:;"><span>随访</span></a></li> -->
				</ul>
			</div>
		</div>
		<div class="tabsContent">
			<div>
	
				<div layoutH="131" style="float:left; display:block; overflow:auto; width:240px; border:solid 1px #CCC; line-height:21px; background:#fff">
				    <ul class="tree treeFolder">
						<li><a href="javascript">模板列表</a>
							<ul>
								<?php if(is_array($tempList)): $i = 0; $__LIST__ = $tempList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li><a href="<?php echo U('detail');?>?id=<?php echo ($vo["id"]); ?>" target="ajax" rel="jbsxBox"><?php echo ($vo["name"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
							</ul>
						</li>
						
				     </ul>
				</div>
				
				<div id="jbsxBox" class="unitBox" style="margin-left:246px;">
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
	
</div>