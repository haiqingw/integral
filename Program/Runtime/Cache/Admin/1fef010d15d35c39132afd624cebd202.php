<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>后台管理系统</title>
    <style>
        .managementSystemRight {
            width: 43%;
            height: 100%;
            position: absolute;
            right: 0;
            bottom: 0;
            z-index: 999;
        }

        .managementSystemRight>h2 {
            font-size: 35px;
            line-height: 150px;
            padding: 50px 0 30px;
        }

        .noticeMain {
            font-size: 20px;
            line-height: 30px;
            padding-top: 10px;
        }

        .noticeListMain {
            width: 90%;
            font-size: 16px;
            line-height: 28px;
            height: 250px;
            overflow-x: hidden;
        }

        .copyRightP {
            width: 100%;
            height: 40px;
            font-size: 18px;
            position: absolute;
            right: 0;
            bottom: 0;
        }

        .bmg {
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 100%;
            background: url(/Public/images/indexBgImg.jpg) no-repeat;
            background-size: 100% auto;
            background-position: center top;
            overflow: hidden;
        }

        .balanceMain>h3 {
            font-size: 24px;
            line-height: 50px;
            padding-top: 50px;
        }

        .balanceMain>p {
            font-size: 80px;
            color: #f33;
            line-height: 150px;
            font-weight: bold;
            padding-bottom: 15px;
        }

        .balanceMain>p em {
            font-size: 50px;
            font-style: normal;
        }

        .balanceMain>a {
            font-size: 16px;
            color: #333;
            border-radius: 5px;
            border: 1px solid #333;
            padding: 8px 15px;
        }

        .balanceMain>a:hover {
            text-decoration: none;
            border-color: #f33;
            color: #f33;
        }
    </style>
    <div class="bmg">

        <div class="managementSystemRight">
            <h2>欢迎进入<span style="color:#FF0000;font-size: 37px;font-family:'楷体';font-weight: bold;">POS分销</span>后台管理系统！</h2>
            <!-- 当前日期天气 -->
            <iframe width="800" scrolling="no" height="120" frameborder="0" allowtransparency="true" src="http://i.tianqi.com/index.php?c=code&id=19&icon=1&temp=0&num=5&site=12"></iframe>
            <!-- 当前余额 -->
            <div class="balanceMain">
                <h3>收入服务费</h3>
                <p>
                    <em>￥</em><?php echo ($serviceFeeSum); ?></p>
                <a href="javascript:;" onclick="jumpPage()" title="服务费列表">查看详情</a>
            </div>
            <!-- 版权所有 -->
        </div>
    </div>
    <script>
        function jumpPage() {
            navTab.openTab("SystemRacharge/index", "<?php echo U('SystemRacharge/index');?>", {
                title: "服务费列表",
                fresh: false
            });
        }
    </script>