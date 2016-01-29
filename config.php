<?php 
/*
*Affcms配置文件
*
*
*/


//设置数据库
$mysqlip = "localhost";
$dbuser = "root";
$dbpass = "root";
$dbname = "amazon";
$table = "keywords";


//配置网站信息
$webname = "pktniu";
$webkeywords = "pktniu";
$webdesc = "pktniu";
$weburl = "http://quan.qdorbo.com/";


//统计代码设置
$tongji = <<<EOF
<!-- Start of StatCounter Code for Default Guide -->
<script type="text/javascript">
var sc_project=10639545; 
var sc_invisible=1; 
var sc_security="e5dde278"; 
var scJsHost = (("https:" == document.location.protocol) ?
"https://secure." : "http://www.");
document.write("<sc"+"ript type='text/javascript' src='" +
scJsHost+
"statcounter.com/counter/counter.js'></"+"script>");
</script>
<noscript><div class="statcounter"><a title="shopify site
analytics" href="http://statcounter.com/shopify/"
target="_blank"><img class="statcounter"
src="http://c.statcounter.com/10639545/0/e5dde278/1/"
alt="shopify site analytics"></a></div></noscript>
<!-- End of StatCounter Code for Default Guide -->
EOF;



//设置amazon信息
$public_key = 'AKIAJAQCWCPNK73ZYP4Q';
$private_key = 'ORaYUUm9pS7FnfTMvn6yZ1dc/1PLvD4bJ4OgLg1A';
$associate_tag = 'chengxuequan-20';
$amacountry = 'com';

//本地时间
$localtime = date('M d,Y', time());


 ?>