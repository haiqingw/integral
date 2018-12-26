<?php if (!defined('THINK_PATH')) exit();?><div class="pageContent" style="padding: 5px">
    <div class="panel" defH="60">
        <h1>查找平台用户</h1>
        <div>
            <form id="form" action="<?php echo U('Commodity/index');?>" onsubmit="return navTabSearch(this);" method="post">
                <div class="searchBar" style="margin-left: 2%">
                    <table class="searchContent" style="margin-top: 8px;">
                        <tr>
                            <td>关键字：
                                <input type="text" name="keywords" style="width: 160px; text-align: center;" value="<?php echo ($keywords); ?>"
                                    placeholder="登录账号,联系电话，公司名称" />
                            </td>
                            <td>
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
            <!-------------收益返现---------------->
            <div>
                <div layoutH="180" style="float: left; display: block; overflow: auto; width: 240px; border: solid 1px #CCC; line-height:21px; background: #fff">
                    <ul class="tree treeFolder" layoutH="240">
                        <li>
                            <a href="javascript">管理平台</a>
                            <ul>
                                <?php if(is_array($resArray)): $i = 0; $__LIST__ = $resArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><li>
                                        <a href="<?php echo U('Commodity/lists');?>?plat=<?php echo ($v["plat"]); ?>" target="ajax" rel="jbsxBoxCOMMONDITYLIST"><?php echo ($v["company"]); ?></a>
                                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
                            </ul>
                        </li>
                    </ul>

                </div>
                <div id="jbsxBoxCOMMONDITYLIST" class="unitBox" style="margin-left: 246px;">
                    <!--#include virtual="list1.html" -->
                </div>

            </div>
        </div>
        <div class="tabsFooter">
            <div class="tabsFooterContent"></div>
        </div>
    </div>

</div>