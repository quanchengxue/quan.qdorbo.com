<?php 
/*
测试mysql备份恢复
*/

//导入类库
include_once 'DbManage.class.php';

//进行备份
//分别是主机，用户名，密码，数据库名，数据库编码
$db = new DBManage ( 'localhost', 'root', 'root', 'amazon', 'utf8' );

$db->backup('goto','linkurl');


 ?>