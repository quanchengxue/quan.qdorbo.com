<?php 
/*
建立数据库文件
*/
// 导入配置文件
include_once 'config.php';
include_once 'conn.php';

//建立数据库linkurl
$create = "CREATE TABLE `linkurl` (`url` varchar(150) NOT NULL,`keywords` varchar(150) NOT NULL,`time` varchar(13) NOT NULL,`click` varchar(7) NOT NULL,PRIMARY KEY (`url`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
if ($return = $pdo->query($create) != false) {
	print "Create DateBase === link is OK!!!\n";
	print "plese waite 5 second!!\n";

}else{
	$arr = $return->errorInfo();
	print_r($arr);
}

sleep(5);

//建立数据库 amazon_linkurl
$create  = "CREATE TABLE `amazon_linkurl` (`id` INT (32) NOT NULL AUTO_INCREMENT,`url` VARCHAR (100) NOT NULL,`keywords` VARCHAR (100) NOT NULL,PRIMARY KEY (`id`)) ENGINE = MyISAM DEFAULT CHARSET = utf8;";

if ($return = $pdo->query($create) != false) {
	print "Create DateBase === amazon_linkurl is OK!!!\n";
	print "plese waite 5 second!!\n";
}else{
	$arr = $return->errorInfo();
	print_r($arr);
}

 ?>
