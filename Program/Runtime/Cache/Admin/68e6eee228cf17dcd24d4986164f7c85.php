<?php if (!defined('THINK_PATH')) exit();?><style>
button {
	margin-right:3px;
}
.encodeTextarea {width:100%;height:100px;}
</style>
<script>
function encode(i){
	var $first = $("#firstTextarea");
	if($first.val() == ""){
		layer.msg("请输入");
		return false;
	}
	myAjax("<?php echo U('encode');?>",{i:i,content:$first.val()},encodeStatus);
}
function encodeStatus(ret){
	//console.log(JSON.stringify(ret));
	var $last = $("#lastTextarea");
	$last.val(ret.msg);
}
function myAjax($url,$data,$back){
    var $index;
    $.ajax({
        url: $url, 
        type: "POST", 
        dataType: "JSON", 
        data:$data,
        beforeSend:function(){
            $index = layer.load(0, {shade:  [0.1,'#fff'] }); 
        },
        success: function (data){
            $back(data);
        },
        error:function(data){ },
        complete:function(){
            layer.close($index);
        }
    });
}
</script>
<textarea class="encodeTextarea" id="firstTextarea"></textarea>
<p>
	<button onclick="encode(1)">RSA_encode</button>
	<button onclick="encode(2)">RSA_decode</button>
	<button onclick="encode(3)">BASE64_encode</button>
	<button onclick="encode(4)">BASE64_decode</button>
	<button onclick="encode(5)">MD5</button>
	<button onclick="encode(6)">JSON_encode</button>
	<button onclick="encode(7)">JSON_decode</button>
</p>
<textarea class="encodeTextarea" id="lastTextarea"></textarea>