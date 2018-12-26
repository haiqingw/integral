<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" method="post" action="<?php echo U('Commodity/platindex');?>" onsubmit="return navTabSearch(this);">
    <input type="hidden" name="keywords" value="<?php echo ($keywords); ?>" />
    <input type="hidden" name="keywords1" value="<?php echo ($keywords1); ?>" />
    <input type="hidden" name="pageNum" value="<?php echo ($page); ?>" />
    <input type="hidden" name="startDate" value="<?php echo ($startDate); ?>" />
    <input type="hidden" name="endDate" value="<?php echo ($endDate); ?>" />
    <input type="hidden" name="busType" value="<?php echo ($busType); ?>">
    <input type="hidden" name="status" value="<?php echo ($status); ?>">
    <input type="hidden" name="al" value="<?php echo ($al); ?>">
</form>
<!-- 查找直属人开始 -->
<script type="text/javascript">
    function showseach() {
        $(".showsearch").toggle();
    }
    $("#searchmerber").click(
        function () {
            var jobnum = $("input[name='searchjob']").val();
            if (jobnum == "") {
                $('input[name="MemberNames"]').val('');
                $('input[name="MemberPaths"]').val('');
                $('input[name="MemberLevels"]').val('');
            } else {
                $.ajax({
                    url: "<?php echo U('findmember');?>",
                    type: "POST",
                    data: {
                        Jobnum: jobnum,
                    },
                    success: function (data) {
                        if (data == null || data == "") {
                            $('input[name="MemberNames"]').val('');
                            $('input[name="MemberPaths"]').val('');
                            $('input[name="MemberLevels"]').val('');
                        } else {
                            $('input[name="MemberNames"]').val(
                                data.MemberName);
                            $('input[name="MemberPaths"]').val(
                                data.MemberDirectlyUnderPeopleIDPath);
                            $('input[name="MemberLevels"]').val(
                                data.MemberLevel);
                        }
                    }
                });
            }
        });
</script>
<!-- /查找直属人结束 -->
<div class="pageHeader">
    <form onsubmit="return navTabSearch(this);" action="<?php echo U('index');?>" method="post" id="BusinessList">
        <div class="searchBar">
            <table class="searchContent">
                <tr>
                    <td align="left"></td>
                    <!-- <td>添加时间：<input type="text" name="startDate"
						style="width: 80px;" class="date" readonly="true"
						datefmt="yyyy-MM-dd" value="<?php echo ($startDate); ?>" /> ~ <input type="text"
						name="endDate" style="width: 80px;" class="date" readonly="true"
						datefmt="yyyy-MM-dd" value="<?php echo ($endDate); ?>" />
					</td> -->
                    <!-- <td align="left">商户类型：</td>
					<td><select name="busType" class="combox">
							<option value="">请选择</option>
							<option value="D"<?php if($busType == 'D'): ?>selected<?php endif; ?>>代理商
							</option>
							<option value="P"<?php if($busType == 'P'): ?>selected<?php endif; ?>>普通
							</option>
					</select></td>
					<td align="left">代理商：</td>
					<td> -->
                    <!-- <select name="al" class="combox">
							<option value="">请选择</option>
							<?php if(is_array($agentList)): $i = 0; $__LIST__ = $agentList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>" <?php if($al == $vo['id']): ?>selected<?php endif; ?>><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
						</select> -->
                    <!-- </td> -->
                    <td align="left">商品状态：</td>
                    <td>
                        <select name="status" class="combox">
                            <option value="">请选择</option>
                            <option value="1" <?php if($status == '1'): ?>selected<?php endif; ?>>未上架
                            </option>
                            <option value="2" <?php if($status == '2'): ?>selected<?php endif; ?>>已上架
                            </option>
                        </select>
                    </td>
                    <!-- <td align="left">是否业务员：</td>
					<td><select name="isClerk" class="combox">
							<option value="">请选择</option>
							<option value="1"<?php if($isClerk == '1'): ?>selected<?php endif; ?>>是
							</option>
							<option value="2"<?php if($isClerk == '2'): ?>selected<?php endif; ?>>否
							</option>
					</select></td> -->
                    <td align="right">关键词：
                        <input type="text" value="<?php echo ($keywords); ?>" id="keywords" name="keywords" autocomplete="off"
                            placeholder="商品名称" style="width: 160px;" />
                    </td>
                    <td align="left">
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

<div class="pageContent">
    <div class="panelBar">

        <ul class="toolBar">
            <li class="line">line</li>
            <li>
                <a class="add" href="<?php echo U('addCommodity');?>" target="navTab" rel="addCommodity" title="商品添加">
                    <span>添加</span>
                </a>
            </li>
            <li class="line">line</li>
            <li>
                <a class="edit" href="<?php echo U('addCommodity');?>?cid={cid}" target="navTab" rel="addCommodity" title="商品修改">
                    <span>修改</span>
                </a>
            </li>
            <li class="line">line</li>
            <li>
                <a class="delete" href="<?php echo U('delCommodityProcess');?>?cid={cid}" target="ajaxTodo" title="确定要删除吗?">
                    <span>删除</span>
                </a>
            </li>
            <li class="line">line</li>
        </ul>
    </div>
    <table class="table" width="100%" layoutH="141">
        <thead>
            <tr>
                <th align="center" width="3%">序号</th>
                <th align="center" width="7%">名称</th>
                <th align="center" width="7%">标题</th>
                <th align="center" width="6%">详情</th>
                <th align="center" width="6%">类别</th>
                <th align="center" width="6%">原价</th>
                <th align="center" width="6%">现价</th>
                <th align="center" width="6%">押金</th>
                <th align="center" width="6%">费率</th>
                <th align="center" width="6%">库存</th>
                <th align="center" width="5%">已售</th>
                <th align="center" width="6%">图片展示</th>
                <th align="center" width="6%">视频展示</th>
                <th align="center" width="4%">商品状态</th>
                <th align="center" width="7%">推荐状态</th>
                <th align="center" width="7%">添加时间</th>
            </tr>
        </thead>
        <?php if($totalCount > 0): ?><tbody>
                <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr target="cid" rel="<?php echo ($v["id"]); ?>">
                        <td><?php echo ($i); ?></td>
                        <td><?php echo ($v["commodityName"]); ?></td>
                        <td><?php echo (msubstr($v["commodityTitle"],0,10)); ?>...</td>
                        <td><?php echo (msubstr(fhtml($v["commodityDetail"]),0,10)); ?>...</td>
                        <td><?php echo ($v["categoryName"]); ?></td>
                        <td><?php echo ($v["originalPrice"]); ?></td>
                        <td><?php echo ($v["nowPrice"]); ?></td>
                        <td><?php echo ($v["deposit"]); ?></td>
                        <td><?php echo ($v["rate"]); ?></td>
                        <td><?php echo ($v["stock"]); ?></td>
                        <td><?php echo ($v["sold"]); ?></td>
                        <td>
                            <a href="<?php echo U('ViewBox');?>?ids=<?php echo ($v["imgPath"]); ?>" target="dialog" mask="true" rel="ViewBox" width="300"
                                height="600">图片展示</a>
                        </td>
                        <td>
                            <?php if(empty($v['videoPath'])): ?>暂无
                                <?php else: echo ($v["videoPath"]); endif; ?>
                        </td>
                        <td>
                            <?php if($v["status"] == 1): ?><font>未上架</font>
                                <?php elseif($v["status"] == 2): ?>
                                <font>已上架</font><?php endif; ?>
                            </switch>
                        </td>
                        <td>
                            <?php if($v["type"] == 1): ?><font>正常</font>
                                <?php elseif($v["type"] == 2): ?>
                                <font>推荐</font>
                                <?php elseif($v["type"] == 3): ?>
                                <font>爆款</font><?php endif; ?>
                        </td>
                        <td><?php echo (dateformat($v["addtime"])); ?></td>
                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
            <?php else: ?>
            <tbody>
                <tr>
                    <td align="center" colspan="14" style="color: red;">抱歉，没有找到符合的记录！</td>
                </tr>
            </tbody><?php endif; ?>
    </table>
    <div class="panelBar tongji">
        <div class="pages tongji_1">
            <span>用户总数：共<?php echo ($totalCount); ?></span>
        </div>
    </div>
    <style>
        .layui-layer-content {
            margin-top: -11px;
        }

        .layui-layer-content img {
            float: left;
            margin-top: 2px;
        }

        .layui-layer.layui-anim.layui-layer-tips,
        .layui-layer-content {
            height: auto !important;
        }
    </style>
    <style>
        .tongji {
            padding-left: 40%;
        }

        .tongji .tongji_1 {
            padding: 0 0 0 20px;
            text-align: center;
        }

        .tongji .tongji_1 span {
            color: red;
        }
    </style>
    <div class="panelBar">
        <div class="pages">
            <span>显示</span>
            <select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage: this.value})">
                <option value="20" <?php if($numPerPage == 20): ?>selected="selected"<?php endif; ?>>20
                </option>
                <option value="50" <?php if($numPerPage == 50): ?>selected="selected"<?php endif; ?>>50
                </option>
                <option value="100" <?php if($numPerPage == 100): ?>selected="selected"<?php endif; ?>>100
                </option>
                <option value="200" <?php if($numPerPage == 200): ?>selected="selected"<?php endif; ?>>200
                </option>
            </select>
            <span>条，共<?php echo ($totalCount); ?>条</span>
        </div>
        <div class="pagination" targetType="navTab" totalCount="<?php echo ($totalCount); ?>" numPerPage="<?php echo ($numPerPage); ?>" pageNumShown="10"
            currentPage="<?php echo ($page); ?>"></div>
    </div>
</div>
<script>
    /**
     *   查看信息
     */
    function tipThis(obj, n, i, b, t) {
        var $str = '（' + n + '）（' + i + '）';
        if (t == 1) {
            $str = '（' + n + '）（' + i + '）（' + b + '）';
        }
        layer.tips($str, $(obj), {
            tips: [2, 'green'],
            area: ['380px', '23px'],
            offset: "", //右下角弹出
            time: 3000
        });
    }
    /**
     * 提现卡 信息
     */
    function drawDankDetail(obj, id) {
        $.ajax({
            url: '<?php echo U("Business/ajaxBankDetail");?>',
            type: "POST",
            dataType: 'Json',
            data: {
                "bids": id
            },
            success: function (ret) {
                if (ret.status == 1) {
                    var $d = ret.data;
                    layer.tips($d.name + '　---　' + $d.card_number + '　---　' +
                        $d.bank_name, $(obj), {
                            tips: [2, 'green'],
                            area: ['380px', '23px'],
                            offset: "", //右下角弹出
                            time: 3000
                        });
                } else {
                    alertMsg.warn(ret.msg);
                }
            }
        });
    }

    function changeClerk(id, type) {
        alertMsg.confirm("是否修改业务员状态", {
            okCall: function () {
                ajaxTodo("<?php echo U('changeClerk');?>?id=" + id + "&type=" + type);
            }
        });
    }
</script>