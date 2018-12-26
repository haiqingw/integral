<?php if (!defined('THINK_PATH')) exit();?><div class="pageContent">
    <form method="post" id="myform" action="<?php echo U('Goodsguige/updateFunction');?>" class="pageForm required-validate"
        onsubmit="return iframeCallback(this);" target="callbackframe" enctype="multipart/form-data">
        <div class="pageFormContent" layoutH="56">
            <div class="unit">
                <input name="goodsId" value="<?php echo ($goodsId); ?>" type="hidden" />
                <fieldset id="guigelist">
                    <legend>商品信息编辑</legend>
                    <div class="tabs" currentIndex="1" eventType="click">
                        <div class="tabsHeader">
                            <div class="tabsHeaderContent">
                                <ul>
                                    <li><a href="<?php echo U('Goods/update');?>?goodsId=<?php echo ($goodsId); ?>" target="navTab"><span>商品基本信息</span></a></li>
                                    <li><a class="add" href="javascript:;" target="navTab"><span>商品规格</span></a></li>
                                    <li><a href="<?php echo U('Goodsphoto/index');?>?goodsId=<?php echo ($goodsId); ?>" target="navTab"><span>商品图片</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <p>商品规格列表</p>
                    <span onclick="addguige()" class="add">再加一项</span>
                    <?php if($guigenum > 0): if(is_array($guiges)): $i = 0; $__LIST__ = $guiges;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><dl class="nowrap" id="guigeid<?php echo ($vo["id"]); ?>">
                                <input type="hidden" name="id[]" value="<?php echo ($vo["id"]); ?>" />
                                <dt><input type="text" name="name[]" class="required" value="<?php echo ($vo["name"]); ?>" placeholder="规格名称" /></dt>
                                <dt>
                                    <input type="text" name="buyPrice[]" value="<?php echo ($vo["buyPrice"]); ?>"
                                        placeholder="规格市场价格" />
                                </dt>
                                <dt>
                                    <input type="text" name="jifen[]" value="<?php echo ($vo["jifen"]); ?>"
                                        placeholder="规格积分" />
                                </dt>
                                <dt><input type="text" name="nowPrice[]" value="<?php echo ($vo["nowPrice"]); ?>"
                                    placeholder="价格" />
                                </dt>
                                <dt>
                                    <span title="确定要删除这些记录吗?" target="selectedTodo" rel="decldel" postType="string" onclick="delguige('<?php echo ($vo["id"]); ?>');"
                    class="delete"><span>删除</span></span>
                                </dt>
                            </dl><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                    <dl class="nowrap">
                        <dt><input type="text" name="name[]" class="required" value=""
                                placeholder="规格名称" /></dt>
                        <dt>
                            <input type="text" name="buyPrice[]" value="" placeholder="规格市场价格" />
                        </dt>
                        <dt>
                            <input type="text" name="jifen[]" value="" placeholder="规格现卖积分" />
                        </dt>
                        <dt>
                            <input type="text" name="nowPrice[]" value="" placeholder="规格现卖价格" />
                        </dt>
                    </dl>
                </fieldset>
            </div>
        </div>
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
<script type="text/javascript">
    function addguige() {
        $('#guigelist').append('<dl class="nowrap">'
            + '<dt><input type="text" name="name[]" value="" placeholder="规格名称"/></dt>'
            + '<dt><input type="text" name="buyPrice[]" value="" placeholder="规格市场价格"/></dt>'
            + '<dt><input type="text" name="jifen[]" value="" placeholder="规格现卖积分"/></dt>'
            + '<dt><input type="text" name="nowPrice[]" value="" placeholder="规格现卖价格"/></dt>'
            + '</dl>');
    }

    function delguige(id){
        $.ajax({
            url:'<?php echo U("Goodsguige/delguige");?>?id='+id+'',
            type:'post',
            data:{id:id},
            success:function(data){
                if(data){
                    $('#guigeid'+id+'').remove();
                }
            }
        });
    }
</script>