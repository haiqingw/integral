<?php if (!defined('THINK_PATH')) exit();?><style>
	/* 清楚默认样式 */
	* {
		margin: 0;
		padding: 0;
	}

	img {
		width: 100%;
	}

	a {
		text-decoration: none;
	}

	a,
	input,
	textarea,
	button {
		-webkit-tap-highlight-color: rgba(0, 0, 0, 0);
		outline: none;
	}

	i,
	em {
		font-style: normal;
	}

	ul,
	li {
		margin: 0;
		padding: 0;
		font-style: normal;
		list-style: none;
	}

	.RindexContainer {
		background-image: url('/Public/images/indexBgImga.jpg');
		background-size: 100% auto;
		background-repeat: no-repeat;
		background-position: center bottom;
		position: absolute;
		left: 0;
		top: 0;
		bottom: 0;
		right: 0;
	}

	.RindexContainer>h3 {
		height: 50px;
		font-size: 36px;
		color: #fff;
		text-align: center;
		padding-top: 12%;
	}

	.RindexMenuMain {
		width: 40%;
		margin: 0 auto;
		overflow: hidden;
		padding-top: 100px;
	}

	.RindexMenuMain>div {
		text-align: center;
		width: 50%;
		float: left;
		font-weight: bold;
		color: #fff;
	}

	.RindexMenuMain>div p {
		font-size: 24px;
		line-height: 40px;
		padding-bottom: 15px;
	}

	.RindexMenuMain>div div {
		font-size: 48px;
		padding-bottom: 10px;
	}

	.RindexMenuMain>div div span {
		font-size: 24px;
	}

	.RYQBtn {
		width: 100px;
		height: 30px;
		line-height: 30px;
		text-align: center;
		border: 1px solid #fff;
		border-radius: 5px;
		margin: 15px auto;
		display: block;
		color: #fff;
		font-size: 14px;
	}

	.menuBoxItem {
		box-sizing: border-box;
	}
</style>
<section class="RindexContainer">
	<h3>欢迎进入三人共创POS分销后台管理系统</h3>
	<div class="RindexMenuMain">
		<div class="menuBoxItem" style="border-right:1px solid #fff;">
			<p>账户余额·元</p>
			<div><span>&yen;</span><?php echo ($balance["balance"]); ?></div>
			<a href="javascript:;" onclick="detailTrade()" class="RYQBtn" title="财务支出支入">查看详情</a>
		</div>
		<div class="menuBoxItem">
			<p>代付可用余额·元</p>
			<div><span>&yen;</span><?php echo ($balance["daifuBalance"]); ?></div>
			<a href="javascript:;" onclick="detailDraw()" class="RYQBtn" title="可用余额支出动态">查看详情</a>
		</div>
	</div>
</section>
<script>
	function detailDraw() {
		var url = "<?php echo U('Accountbalance/drawlists');?>";
		$.pdialog.open(url, "dialog", "可用余额支出动态", {
			mask: true,
			width: 1300,
			height: 768,
			drawable: false,
			maxable: false,
			minable: false,
			resizable: false
		});
	}
	/**
	 * 明细列表
	 */
	function detailTrade() {
		var url = "<?php echo U('Accountbalance/balancelists');?>";
		$.pdialog.open(url, "dialog", "财务支出支入", {
			mask: true,
			width: 1300,
			height: 768,
			drawable: false,
			maxable: false,
			minable: false,
			resizable: false
		});
	}
</script>