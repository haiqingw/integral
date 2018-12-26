<?php if (!defined('THINK_PATH')) exit();?><div class="pageContent" style="padding: 5px">
    <div class="panel" defH="60">
        <h1>查找平台用户</h1>
        <div>
            <form id="form" action="<?php echo U('WithdrawalManage/index');?>" onsubmit="return navTabSearch(this);" method="post">
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
                    <li><a href="javascript:;"><span>提现列表</span></a></li>
                </ul>
            </div>
        </div>
        <div class="tabsContent">
            <!-------------收益返现---------------->
            <div>
                <div layoutH="180" style="float: left; display: block; overflow: auto; width: 340px; border: solid 1px #CCC; line-height:
                                21px; background: #fff">
                    <ul class="tree treeFolder" layoutH="240">
                        <li>
                            <a href="javascript">平台用户</a>
                            <ul>
                                <?php if(is_array($resArray)): $i = 0; $__LIST__ = $resArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><li>
                                        <a href="<?php echo U('WithdrawalManage/platlists');?>?plat=<?php echo ($v["plat"]); ?>" target="ajax" rel="jbsxBoxDRAWLISTS"><?php echo ($v["company"]); ?>
                                            <!-- <em style="font-style: normal;color: #800000;text-align: center;line-height:22px;padding-right:10px;;;">(<?php echo ($v["usertable_Name"]); ?>)</em> -->
                                            <span style="background:#696969;color:#fff;padding:0 8px; border-radius:15px; font-size:12px;float:right;display:inline-block; line-height:16px;margin:4px 10px 0 0;height:15px;overflow: hidden;"><?php echo ($v["drawcon"]); ?>&nbsp;条</span>
                                        </a>
                                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div id="jbsxBoxDRAWLISTS" class="unitBox" style="margin-left: 346px;">
                    <!--#include virtual="list1.html" -->
                </div>
            </div>
        </div>
        <div class="tabsFooter">
            <div class="tabsFooterContent"></div>
        </div>
    </div>

</div>