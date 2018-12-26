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

    .RTabs .tabsHeader li a {
        padding: 0 10px;
        background: #b8d0d6;
        margin-left: 2px;
        margin-right: 3px;
        background-image: none;
        height: 25px;
        line-height: 26px;
        overflow: hidden;
        border-radius: 3px;
    }

    .RTabs .tabsHeader li.selected a {
        background: #6d9dd7;
        color: #fff;
        border-radius: 3px;
    }

    .RTabs .tabsHeader li span {
        padding: 0;
    }

    .RTabs .tabsHeader li span {
        background: #b8d0d6;
        background-image: none;
        height: 25px;
        line-height: 26px;
        border-radius: 3px;
    }

    .RTabs .tabsHeader li.selected span {
        background: #6d9dd7;
        padding: 0;
        color: #fff;
        border-radius: 3px;
    }

    .RTabs .tabsHeader li {
        background: none;
    }
</style>

<div class="pageContent" style="padding:5px">
    <div class="panel" defH="60">
        <h1>查找平台用户</h1>
        <div>
            <form id="form" action="<?php echo U('Onlineorder/index');?>" onsubmit="return navTabSearch(this);" method="post">
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
    <div class="tabs RTabs">
        <div class="tabsHeader">
            <div class="tabsHeaderContent">
                <ul>
                    <li><a href="javascript:;"><span>平台订单</span></a></li>
                    <li><a href="javascript:;"><span>退款审核</span></a></li>
                    <!-- <li><a href="javascript:;"><span>普通商户</span></a></li> -->
                </ul>
            </div>
        </div>
        <div class="tabsContent">
            <!-----商户等级设置---->
            <div>
                <div layoutH="180" style="float:left; display:block; overflow:auto; width:240px; border:solid 1px #CCC; line-height:21px; background:#fff">
                    <ul class="tree treeFolder">
                        <li><a href="javascript">平台列表</a>
                            <ul>
                                <?php if(is_array($resArray)): $i = 0; $__LIST__ = $resArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><li>
                                        <a href="<?php echo U('Onlineorder/lists');?>?plat=<?php echo ($val["plat"]); ?>" target="ajax" rel="jbsxBoxONLINEORDER"><?php echo ($val["company"]); ?></a>
                                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div id="jbsxBoxONLINEORDER" class="unitBox" style="margin-left:246px;">
                </div>
            </div>
            <!----------返现类型设置----------->
            <div>
                <div layoutH="200" style="float:left; display:block; overflow:auto; width:240px; border:solid 1px #CCC; line-height:21px; background:#fff">
                    <ul class="tree treeFolder">
                        <li><a href="javascript">平台列表</a>
                            <ul>
                                <?php if(is_array($resArray)): $i = 0; $__LIST__ = $resArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><li>
                                        <a href="<?php echo U('Onlineorder/audits');?>?plat=<?php echo ($val["plat"]); ?>" target="ajax" rel="jbsxBoxONLINEORDERAUDIT"><?php echo ($val["company"]); ?></a>
                                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div id="jbsxBoxONLINEORDERAUDIT" class="unitBox" style="margin-left:246px;">
                </div>
            </div>
        </div>
    </div>
    <div class="tabsFooter">
        <div class="tabsFooterContent"></div>
    </div>
</div>

</div>