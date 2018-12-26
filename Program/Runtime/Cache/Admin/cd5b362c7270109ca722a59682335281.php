<?php if (!defined('THINK_PATH')) exit();?><div class="pageContent" style="padding: 5px">
    <!-- <div class="panel" defH="40"> -->
    <!-- </div> -->
    <div class="divider"></div>
    <div class="tabs">
        <div class="tabsHeader">
            <div class="tabsHeaderContent">
                <ul>
                    <li><a href="javascript:;"><span>交易列表</span></a></li>
                    <li><a href="javascript:;"><span>机具列表</span></a></li>
                </ul>
            </div>
        </div>
        <div class="tabsContent">
            <!-------------收益返现---------------->
            <div>
                <div layoutH="70" style="float: left; display: block; overflow: auto; width: 240px; border: solid 1px #CCC; line-height:
						21px; background: #fff">
                    <ul class="tree treeFolder">
                        <?php if(is_array($resArray)): $i = 0; $__LIST__ = $resArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$va): $mod = ($i % 2 );++$i;?><li>
                                <a href="javascript"><?php echo ($va['category']); ?></a>
                                <ul>
                                    <?php if(is_array($va['list'])): $i = 0; $__LIST__ = $va['list'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><li>
                                            <a><?php echo ($v["product"]); ?></a>
                                            <ul>
                                                <?php if(is_array($v['company'])): $i = 0; $__LIST__ = $v['company'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><li>
                                                        <a href="<?php echo U('POSstatistics/translists');?>?productTypes=<?php echo ($val["ruleList"]); ?>&plat=<?php echo ($val["plat"]); ?>"
                                                            target="ajax" rel="jbsxBoxSYSZXLB"><?php echo ($val["companyName"]); ?></a>
                                                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
                                            </ul>
                                        </li><?php endforeach; endif; else: echo "" ;endif; ?>
                                </ul>
                            </li><?php endforeach; endif; else: echo "" ;endif; ?>
                    </ul>
                </div>
                <div id="jbsxBoxSYSZXLB" class="unitBox" style="margin-left: 246px;">
                    <!--#include virtual="list1.html" -->
                </div>
            </div>
            <div>
                <div layoutH="70" style="float: left; display: block; overflow: auto; width: 240px; border: solid 1px #CCC; line-height:
						21px; background: #fff">
                    <ul class="tree treeFolder">
                        <?php if(is_array($resArray)): $i = 0; $__LIST__ = $resArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$va): $mod = ($i % 2 );++$i;?><li>
                                <a href="javascript"><?php echo ($va['category']); ?></a>
                                <ul>
                                    <?php if(is_array($va['list'])): $i = 0; $__LIST__ = $va['list'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><li>
                                            <a><?php echo ($v["product"]); ?></a>
                                            <ul>
                                                <?php if(is_array($v['company'])): $i = 0; $__LIST__ = $v['company'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><li>
                                                        <a href="<?php echo U('POSstatistics/terminallist');?>?plat=<?php echo ($val["plat"]); ?>&proid=<?php echo ($val["proid"]); ?>"
                                                            target="ajax" rel="jbsxBoxSYSTERM"><?php echo ($val["companyName"]); ?></a>
                                                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
                                            </ul>
                                        </li><?php endforeach; endif; else: echo "" ;endif; ?>
                                </ul>
                            </li><?php endforeach; endif; else: echo "" ;endif; ?>
                    </ul>
                </div>
                <div id="jbsxBoxSYSTERM" class="unitBox" style="margin-left: 246px;">
                    <!--#include virtual="list1.html" -->
                </div>
            </div>
        </div>
        <div class="tabsFooter">
            <div class="tabsFooterContent"></div>
        </div>
    </div>

</div>