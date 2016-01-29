<?php 

$mysqlip = "localhost";
$dbuser = "root";
$dbpass = "root";
$dbname = "mysqltest";
$table = "test";


$dsn = 'mysql:dbname='.$dbname.';host='.$mysqlip;
//实例化PDO数据库类
$pdo = new PDO($dsn, $dbuser,$dbpass); 



/*  删除 FRUIT 数据表中满足条件的所有行 */

$count = $pdo-> exec("INSERT INTO `test` (`short`, `keywords`) VALUES ('111','111'),('222','222'),('333','333'),('444','444'),('555','555')");

/*for ($i=0; $i <10 ; $i++) { 
	$count = $pdo-> exec("INSERT INTO `test` (`short`, `keywords`) VALUES ('shor$i', 'keywords$i')");
}*/

var_dump($count);

echo $pdo -> lastinsertid()+4; 

exit();

if($pdo -> exec("INSERT INTO `test` (`short`, `keywords`) VALUES ('1115454', '15114811')")){ 
echo "插入成功！"; 
echo $pdo -> lastinsertid(); 
} else{ 
    echo 'Connection failed: ' .$pdo->errorCode();
    echo $pdo->errorInfo()[2];
}  

 ?>