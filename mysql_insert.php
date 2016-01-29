<?php 
include_once 'config_mysql.php';
include_once 'conn_mysql.php';

//获取MYSQL最大值
$SelectMax = "SELECT MAX(id) FROM $table";
//执行查询
$MaxQuery = $pdo->query($SelectMax);
//返回结果
$MaxRow = $MaxQuery->fetch();

print ("Total data is $MaxRow[0]. \n");
//删除short字段

$count  =  $pdo -> exec ( "ALTER TABLE `$table` DROP COLUMN short" );
print( "Deleted  $count  rows.\n" );

//再添加short字段
$count  =  $pdo -> exec ( "ALTER TABLE `$table` ADD COLUMN `short`  varchar(32) NULL AFTER `keywords`, ADD UNIQUE INDEX `short` (`short`) ;" );
print ("ADD COLUMN $count OK!!!.\n");



//写入short code
for ($i=1; $i <= $MaxRow[0] ; $i++) { //$MaxRow[0]
	a:
	$short_code = getRandomString(6);
	if ($up = $pdo-> exec("UPDATE `$table` SET `short`='$short_code' WHERE (`id`='$i')")) {
		print( "UPDATE DATE ID $i is OK..\n" );
	}elseif(strpos($pdo->errorInfo()[2], "short")){//如果有碰撞，则跳转到a位置，重新生成，并且写入。
		// $short_code = getRandomString(6);
		goto a;
	}else{
		echo $pdo->errorInfo()[2];
		break;
	}
}


// $pdo->exec("INSERT INTO `$dbname`.`$table` (`id`, `keywords`) VALUES ('', '$keyword');");




//生成随机字符串方法
function getRandomString($len, $chars=null)
{
    if (is_null($chars)){
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    }  
    mt_srand(10000000*(double)microtime());
    for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < $len; $i++){
        $str .= $chars[mt_rand(0, $lc)];  
    }
    return $str;
}
 ?>