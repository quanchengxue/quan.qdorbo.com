<?php 
/*
外链接口，301跳转
*/

include_once 'config.php';
include_once 'conn.php';

$Mysql_table = 'linkurl';
$lailu = $_SERVER['HTTP_REFERER'];
$keywords = $_GET['keywords'];


/*
判断是否为主页
*/
$type = $_GET['type'];


$ret= array();
function return_keywords($value)//判断是否为主页
{
	global $ret;
	global $pdo;
	global $table;
	global $keywords;
	if ($value == "index") {
		//获取mysql最大值
		$SelectMax = "SELECT MAX(id) FROM $table";
	    //执行查询
	    $MaxQuery = $pdo->query($SelectMax);
	    //返回结果
	    $MaxRow = $MaxQuery->fetch();
	    $MaxId = $MaxRow[0];
	    //获取相关关键字
	    $Rand = '';
	    for ($i = 0; $i < 30; $i++) {
	        $Rand.= "," . mt_rand(1, $MaxId);
	    }
	    $Rand = substr($Rand, 1);
	    $QueryKey = "SELECT $table.keywords FROM $table WHERE id in($Rand)";
	    $ReturnKey = $pdo->query($QueryKey);
	    $RandKeyword = $ReturnKey->fetchAll(PDO::FETCH_COLUMN,0);
	    return $RandKeyword;
	    break;
	}elseif ($value == "item") {
		$SelectMax = "SELECT MAX(id) FROM $table";
	    //执行查询
	    $MaxQuery = $pdo->query($SelectMax);
	    //返回结果
	    $MaxRow = $MaxQuery->fetch();
	    $MaxId = $MaxRow[0];
	    //获取相关关键字
	    $Rand = '';
	    for ($i = 0; $i < 10; $i++) {
	        $Rand.= "," . mt_rand(1, $MaxId);
	    }
	    $Rand = substr($Rand, 1);
	    $QueryKey = "SELECT $table.keywords FROM $table WHERE id in($Rand)";
	    $ReturnKey = $pdo->query($QueryKey);
	    $RandKeyword = $ReturnKey->fetchAll(PDO::FETCH_COLUMN,0);
		// return $RandKeyword;

	    $websitename = array('walmart','ebay','amazon' );
	    $website = $websitename[mt_rand(0,2)];
	    if ($website=='walmart') {
	        $url = 'http://www.walmart.com/search/autocomplete/v1/0/'.rawurlencode($keywords).'.js';
	        $contents = file_get_contents($url);
	        $isMatched = preg_match('/(\],"[\s\S]*?"])/', $contents, $matches);
	        $matches = str_replace('],','[',$matches[0]);
	        $contents = json_decode($matches);
	        print 'walmart';
	        // return $contents;
	    }
	    elseif ($website=='ebay') {
	        // $ebayIp = array('66.135.212.230','66.135.212.139','66.135.223.120' );
	        $url = 'http://autosug.ebaystatic.com/autosug?sId=0&kwd='.rawurlencode($keywords);
	        $contents = file_get_contents($url);
	        $isMatched = preg_match('/(\["[\s\S]*?])/', $contents, $matches);
	        $contents = json_decode($matches[0]);
	        print 'ebay';
	        // return $contents;
	    }
	    elseif ($website=='amazon') {
	        $AmazonIp = array('72.21.214.50','205.251.242.50','207.171.162.173' );
	        $url = 'http://'.$AmazonIp[mt_rand(0,2)].'/search/complete?mkt=1&search-alias=aps&q='.rawurlencode($keywords);
	        $contents = file_get_contents($url);
	        $contents = json_decode($contents);
	        print 'amazon';
	        $contents =  $contents[1];
	        // return $contents;
	    }
	    $ret['randkeywords'] = $RandKeyword;
	    $ret['contents'] = $contents;
	    return $ret;
	}
}


$data = getMillisecond();


//先获取10条最新外链。
$select = "SELECT * FROM linkurl ORDER BY linkurl.time DESC LIMIT 0, 10";//获取最新10条外链
$ReturnKey = $pdo->query($select);
$retrun_linkurl = $ReturnKey->fetchAll(PDO::FETCH_ASSOC);
$ret['link_keywords'] = $retrun_linkurl;
$ret = return_keywords($type);


var_dump($ret);
//显示为json格式
print json_encode($ret);


//再写入新的外链
// $pdo->exec("INSERT INTO `$dbname`.`linkurl` (`url`, `keywords`, `time`, `click`) VALUES ('$lailu', '$keywords', '$data', '');");//插入新外链

//获取13位时间戳

function getMillisecond() {
	list($t1, $t2) = explode(' ', microtime());
	return $t2 . ceil( ($t1 * 1000) );
}



exit();

/*
$pdo->exec("INSERT INTO `$dbname`.`$table` (`id`, `keywords`) VALUES ('', '$value');");
INSERT INTO `$dbname`.`linkurl` (`url`, `keywords`, `time`, `click`) VALUES ('$lailu', '$keywords', '$data', '');
SELECT * FROM linkurl ORDER BY linkurl.click , linkurl.time LIMIT 0, 10


*/

/*

Sql代码 
SELECT * FROM A WHERE id < $id ORDER BY id DESC LIMIT 1  
 
下一条：
Sql代码 
SELECT * FROM A WHERE id > $id ORDER BY id ASC LIMIT 1  


*/
 ?>