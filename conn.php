<?php 
/*
AffCms
数据库配置文件
*/

//设置头部信息
header("Content-type:text/html;charset=UTF-8");
//包含配置文件
include 'config.php';
//设置链接语句
$dsn = 'mysql:dbname='.$dbname.';host='.$mysqlip;
//实例化PDO数据库类
$pdo = new PDO($dsn, $dbuser,$dbpass); 



// //数据库查询语句
// $sql = 'SELECT * FROM keywords WHERE id = 35';
// //执行查询
// $query = $pdo->query($sql);
// //返回结果
// $row = $query->fetch();

// print_r($row);


 ?>