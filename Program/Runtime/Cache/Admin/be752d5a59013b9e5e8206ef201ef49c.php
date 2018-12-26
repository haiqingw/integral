<?php if (!defined('THINK_PATH')) exit();?><style type="text/css">
    ul.rightTools {
        float: right;
        display: block;
    }
    
    ul.rightTools li {
        float: left;
        display: block;
        margin-left: 5px
    }
    </style>
    
    <div class="pageContent" style="padding: 5px">
        <div class="panel" defH="40">
            <h1>病人基本信息</h1>
            <div>
                病人编号：<input type="text" name="patientNo" />
                <ul class="rightTools">
                    <li><a class="button" target="dialog" href="demo/pagination/dialog1.html" mask="true"><span>创建病例</span></a></li>
                    <li><div class="buttonDisabled">
                            <div class="buttonContent">
                                <button>病人治疗流程</button>
                            </div>
                        </div></li>
                    <li><div class="buttonDisabled">
                            <div class="buttonContent">
                                <button>按病人编号检索病例</button>
                            </div>
                        </div></li>
                    <li><div class="buttonDisabled">
                            <div class="buttonContent">
                                <button>从病人列表选取病例</button>
                            </div>
                        </div></li>
                </ul>
            </div>
        </div>
        <div class="divider"></div>
        <div class="tabs">
            <div class="tabsHeader">
                <div class="tabsHeaderContent">
                    <ul>
                        <li><a href="javascript:;"><span>用户列表</span></a></li>
                    </ul>
                </div>
            </div>
            <div class="tabsContent">
                <div>
    
                    <div layoutH="146" style="float: left; display: block; overflow: auto; width: 240px; border: solid 1px #CCC; line-height: 21px; background: #fff">
                        <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><ul class="tree treeFolder">
                            <li><a href="javascript"><?php echo ($vo["company"]); ?></a>
                                <ul>
                                    <?php if(is_array($vo['data'])): foreach($vo['data'] as $key=>$fo): ?><li><a href="<?php echo U('Users/lists');?>?plat=<?php echo ($fo["plat"]); ?>&level=<?php echo ($fo["level"]); ?>" target="ajax" rel="jbsxBox"><?php echo ($fo["classname"]); ?></a></li><?php endforeach; endif; ?>
                                </ul></li>
    
                        </ul><?php endforeach; endif; else: echo "" ;endif; ?>
                    </div>
    
                    <div id="jbsxBox" class="unitBox" style="margin-left: 246px;">
                        <!--#include virtual="list1.html" -->
                    </div>
    
                </div>
            </div>
            <div class="tabsFooter">
                <div class="tabsFooterContent"></div>
            </div>
        </div>
    
    </div>