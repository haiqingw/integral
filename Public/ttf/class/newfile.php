<?php
header("Content-Type:text/html;charset=utf-8");
function rsa_publickey_encrypt($pubk, $data){
	$pubk = openssl_get_publickey($pubk);
	openssl_public_encrypt($data,$en,$pubk,OPENSSL_PKCS1_PADDING);
	return $en;
}
function rsa_privatekey_decrypt($prik, $data){
	$prik = openssl_get_privatekey($prik);
	openssl_private_decrypt($data,$de,$prik,OPENSSL_PKCS1_PADDING);
	return $de;
}
function rsa_encrypt($method, $key, $data, $rsa_bit = 1024){
	$inputLen = strlen($data);
	$offSet = 0;
	$i = 0;
	$maxDecryptBlock = $rsa_bit / 8 - 11;
	$en = '';
	// 对数据分段加密
	while($inputLen - $offSet > 0){
		if($inputLen - $offSet > $maxDecryptBlock){
			$cache = $method($key,substr($data,$offSet,$maxDecryptBlock));
		}else{
			$cache = $method($key,substr($data,$offSet,$inputLen - $offSet));
		}
		$en = $en . $cache;
		$i++;
		$offSet = $i * $maxDecryptBlock;
	}
	return $en;
}
function rsa_decrypt($method, $key, $data, $rsa_bit = 1024){
	$inputLen = strlen($data);
	$offSet = 0;
	$i = 0;
	$maxDecryptBlock = $rsa_bit / 8;
	$de = '';
	$cache = '';
	// 对数据分段解密
	while($inputLen - $offSet > 0){
		if($inputLen - $offSet > $maxDecryptBlock){
			$cache = $method($key,substr($data,$offSet,$maxDecryptBlock));
		}else{
			$cache = $method($key,substr($data,$offSet,$inputLen - $offSet));
		}
		$de = $de . $cache;
		$i = $i + 1;
		$offSet = $i * $maxDecryptBlock;
	}
	return $de;
}
$prik = '-----BEGIN RSA PRIVATE KEY-----
MIICXQIBAAKBgQCtxKMuGIv1ERWmJm4g7a9SfOXymu1pGv1AolFnkjHSa+edVJop
kIg0QDyW7fC14NPZXLT6V765YtZv7EU6OEnrZ+lxrQS2gAbbj0F+OEzO9yd/9cKc
XoRb7EBYiw91Lc49cBcAn0QMO9iYb95qRxEdzxymAs9Te5B1B+sATVa7cQIDAQAB
AoGAd9BRw4LhXcS97KYq4UGB1ZqQ4sq4T/RwEpTZFFTVTYVhWjXvZiFmCMESBe9i
PcYbzJADqWm+9AyWVu3Ofeo57JfpxUJw93mVyUvj6IIs+3ktmY3Db/G0RoGpao3C
NvsIwZDjQBlyHH4/ZuIHfRQ80PZCvylx1jBC9SZ2pLYixJECQQDZPgEms96zkJK1
vuwsf510IaQz79w9Rb1nSG08iBlxNJjbQAhwrNbxXjRz6Afd9RfZLoE01YNhg7ZK
+1YbIagnAkEAzMUP9yeFdQ1Hxmw5f4t9e0RL3Tbyf6A9uUr4V2hPCh/h8BFcaDo4
Nk98svsgJtabMBRo8d1xjHVFj+7O8pnmpwJAV4YnqJQnUWkZ8qdtN7Bim3tCULp+
nSEP4iDIAe9DcNykCRGPVPYN00kFEP2WzdIFPbcCz2qGeC88rpD8bAnvWQJBAJxn
FDe6JxRtrVngRdamq5RgaPWxR2217g8+NQtGL8DS81bTW9p8RX0uH1fxufAQUP5b
SIEcm+Mlm5lBVS414NcCQQC3N4m1L8UmoX+64DkYrrj1s/2IWMUX594qD7hNyRC2
urDAx2ImZbpnfosueHiryTA3G5QV7Y2VoFRvkr/sImTk
-----END RSA PRIVATE KEY-----';
$pubk = '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCtxKMuGIv1ERWmJm4g7a9SfOXy
mu1pGv1AolFnkjHSa+edVJopkIg0QDyW7fC14NPZXLT6V765YtZv7EU6OEnrZ+lx
rQS2gAbbj0F+OEzO9yd/9cKcXoRb7EBYiw91Lc49cBcAn0QMO9iYb95qRxEdzxym
As9Te5B1B+sATVa7cQIDAQAB
-----END PUBLIC KEY-----';
$t1 = 'Welcome to <a href="http://www.juwends.com" target="_blank">
www.juwends.com</a>
        这是个非常好的博客，非常欢迎大家经常来访问，谢谢<br>(&$(@^%)&)&)%<br>
 Juwend\'s的人员构成：如下<br>
                管理员：Juwend & Bigworld<br>
  作者：Juwend & Bigworld & 郭小枫 & 果果<br>
  93572504375lsdah;ldvjg;dzlj8(*^(*%Q*^)(&９３７５０（＆)<br>
  <img alt="" src=" ... 请自行到实例页面查看源码并复制这里的数据 ... " />
  <br>可以看到，上面的图片也可以被加密，取到图片的数据：data:image/png;base64，
  然后可以进行加密，再解密<br>
  这下数据就非常的长了，也可以RSA加解密了~~~~~~';
echo "<p><span style=\"color: #ff0000;\">明文：</span><br>" . $t1 . "</p>";
$r = rsa_encrypt('rsa_publickey_encrypt',$pubk,$t1);
echo "<p><span style=\"color: #ff0000;\">密文：</span><br>" . $r . "</p>";
$de = rsa_decrypt('rsa_privatekey_decrypt',$prik,$r);
echo "<p><span style=\"color: #ff0000;\">解密的明文：</span><br>" . $de . "</p>";
echo "<p><span style=\"color: #ff0000;\">明文 " . ($t1 == $de ? '==' : '!=') . " 密文</span></p>";