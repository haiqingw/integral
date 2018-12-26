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

<div class="pageContent" style="padding: 5px;">
    <div class="panel" defH="40">
        <h1>商品基本信息</h1>
        <div>
            商品编号：<input type="text" name="patientNo" value="<?php echo ($goodsId); ?>" />
            <ul class="rightTools">
                <li><a class="button" target="navTab" href="<?php echo U('Goods/add');?>" mask="true"><span>添加商品</span></a></li>
                <li><a class="button" target="navTab" href="<?php echo U('Goods/update');?>?id=<?php echo ($goodsId); ?>" mask="true"><span>修改商品</span></a></li>
            </ul>
        </div>
    </div>
    <div class="divider"></div>
    <div class="tabs" currentIndex="2" eventType="click">
        <div class="tabsHeader">
            <div class="tabsHeaderContent">
                <ul>
                    <li><a class="add" href="<?php echo U('Goods/update');?>?id=<?php echo ($goodsId); ?>" target="navTab"><span>商品基本信息</span></a></li>
                    <li><a class="add" href="<?php echo U('Goodsguige/add');?>?goodsId=<?php echo ($goodsId); ?>" target="navTab"><span>商品规格</span></a></li>
                    <li><a href="<?php echo U('Goodsphoto/index');?>?goodsId=<?php echo ($goodsId); ?>" target="navTab"><span>商品图片</span></a></li>
                </ul>
            </div>
        </div>
        <div class="tabsContent">
            <div></div>
            <div></div>
            <!--不能删除-->
            <div>
                <div layoutH="146" style="float: left; display: block; overflow: auto; width: 240px; border: solid 1px #CCC; line-height: 21px; background: #fff">
                    <ul class="tree treeFolder">
                        <li><a href="<?php echo U('Goodsphoto/index');?>?goodsId=<?php echo ($goodsId); ?>" target="navTab" rel="jbsxBox">商品</a></li>
                        <?php if(is_array($guiges)): $i = 0; $__LIST__ = $guiges;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li><a href="<?php echo U('Goodsphoto/index');?>?goodsId=<?php echo ($goodsId); ?>&guigeId=<?php echo ($vo["id"]); ?>" target="navTab"
                                    rel="jbsxBox"><?php echo ($vo["name"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
                    </ul>
                </div>

                <div layoutH="146" id="jbsxBox" class="unitBox" style="margin-left: 246px;">
                    <form method="post" id="myform" action="<?php echo U('Goodsphoto/add');?>" class="pageForm required-validate"
                        onsubmit="return iframeCallback(this);" target="callbackframe" enctype="multipart/form-data">
                        <h4>商品：<?php if($guigeId > 0): echo ($good['name']); ?> / <?php echo ($guige['name']); else: echo ($good['name']); endif; ?>的图片</h4>
                        <?php if(is_array($photos)): $i = 0; $__LIST__ = $photos;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><dl class="nowrap" style="width:80px;height:80px;margin-left:20px;">
                                <dt>
                                    <?php if(!empty($vo['photo'])): ?><img style="width: 60px;" src="<?php echo ($vo['photo']); ?>" />
                                        <?php else: ?> 未上传<?php endif; ?>
                                </dt>
                            </dl>
                            <dd><a href="<?php echo U('Goodsphoto/del');?>?id=<?php echo ($vo["id"]); ?>" target="navTab"><span>删除</span></a></dd><?php endforeach; endif; else: echo "" ;endif; ?>

                        <input type="hidden" name="goodsId" value="<?php echo ($goodsId); ?>" />
                        <input type="hidden" name="guigeId" value="<?php echo ($guigeId); ?>" />
                        <br />
                        <dl class="nowrap">
                            <dt>添加图片</dt>
                            <dd>
                                <input name="photo1" type="file" />
                                <p style="clear: both; width: 100%">
                                    <font style="margin-left: 130px; color: #FF0000;">提示:图片尺寸为237*70</font>
                                </p>
                            </dd>
                        </dl>

                        <div class="formBar">
                            <ul style="float:left">
                                <li style="margin-left: 125px; margin-right: 35px">
                                    <div class="buttonActive">
                                        <div class="buttonContent">
                                            <button type="submit">保存</button>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="button">
                                        <div class="buttonContent">
                                            <button type="button" class="close">关闭</button>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
    <div class="tabsFooter">
        <div class="tabsFooterContent"></div>
    </div>
</div>

</div>