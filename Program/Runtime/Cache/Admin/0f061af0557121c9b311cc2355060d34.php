<?php if (!defined('THINK_PATH')) exit();?><div class="pageContent">
    <form method="post" id="myform" action="<?php echo U('Goods/addgoodsFunc');?>" class="pageForm required-validate" onsubmit="return iframeCallback(this);"
        target="callbackframe" enctype="multipart/form-data">
        <div class="pageFormContent" layoutH="56">
            <div class="unit">
                <fieldset>
                    <legend>商品入库</legend>
                    <dl class="nowrap">
                        <dt>入库商品：</dt>
                        <dd>
                            <input type="hidden" name="goodsId" value="<?php echo ($good["id"]); ?>"  />
                            <input type="text" class="required disabled" value="<?php echo ($good["name"]); ?>" readonly/>
                        </dd>
                    </dl>
                    <dl class="nowrap">
                        <dt>入库数量：</dt>
                        <dd>
                            <input type="text" name="num"  class="required" value="" placeholder="入库数量"/>
                        </dd>
                    </dl>
                    <dl class="nowrap">
                        <dt>入库价格：</dt>
                        <dd>
                            <input type="text" name="addPrice"  class="required" value=""  placeholder="入库价格 /元"/>
                        </dd>
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