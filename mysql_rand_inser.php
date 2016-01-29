<?php 
/*
打乱顺序
建立新数据库，并且从老数据库中重新读取并写入
*/

include_once 'config.php';
include_once 'conn.php';



$id_arr = array(1);
for ($i=2; $i <75610 ; $i++) { 
	array_push($id_arr,$i);
}
shuffle($id_arr);

var_dump($return_keywords['keywords']);

$create = "CREATE TABLE `newkeywords` (`id` int(11) NOT NULL AUTO_INCREMENT,`keywords` varchar(128) NOT NULL,`short` varchar(32) DEFAULT NULL,PRIMARY KEY (`id`),UNIQUE KEY `keywords` (`keywords`),UNIQUE KEY `short` (`short`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
if ($return = $pdo->query($create) != false) {
	print "Create DateBase === link is OK!!!\n";
	print "plese waite 5 second!!\n";

}else{
	$arr = $return->errorInfo();
	print_r($arr);
}

sleep(5);

foreach ($id_arr as $value) {
	$select = "SELECT keywords.keywords FROM `keywords` WHERE `id` = $value";
	$return = $pdo->query($select);
	$return_keywords = $return->fetch();
	$keywords = $return_keywords['keywords'];
	//写入数据
	$count = $pdo-> exec("INSERT INTO `newkeywords` (`keywords`) VALUES ('$keywords')");
	if ($count !== false) {
		print "Insert ID:$value is Ok! Keywords: $keywords\n";
	}
}



 ?>