<?php 
/*
核心文件
*/


/*
获取mysql最大值
*/
function GetMysqlMax()
{
	global $table;
	//获取MYSQL最大值
	$SelectMax = "SELECT MAX(id) FROM $table";
	//执行查询
	$MaxQuery = $pdo->query($SelectMax);
	//返回结果
	$MaxRow = $MaxQuery->fetch();
	return $MaxRow[0];
}

/*
获取相关关键字
*/
function GetReKeywords($Max)
{
    global $MaxId;
    global $pdo;
    global $table;

    // global $pdo;
    //取得MYsql最大值
    $SelectMax = "SELECT MAX(id) FROM $table";
    //执行查询
    $MaxQuery = $pdo->query($SelectMax);
    //返回结果
    $MaxRow = $MaxQuery->fetch();
    $MaxId = $MaxRow[0];
    //获取相关关键字
    $Rand = '';
    for ($i = 0; $i < $Max; $i++) {
        $Rand.= "," . mt_rand(1, $MaxId);
    }
    $Rand = substr($Rand, 1);
    $QueryKey = "SELECT $table.keywords,$table.short FROM $table WHERE id in($Rand)";
    $ReturnKey = $pdo->query($QueryKey);
    // $RandKeyword = $ReturnKey->fetchAll(PDO::FETCH_COLUMN,1);
    $RandKeyword = $ReturnKey->fetchAll();
    return $RandKeyword;
}

/*
网址算法
*/

function base62($i)
{
    if($i<0) return '';
    $ch = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $a='';
    do{$a=$ch[$i%62].$a;$i=intval($i/62);
    }while($i>0);
    return $a;
}

/*
Curl
 */
function Curl_Get($durl,$REFERER=_REFERER_){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $durl);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);
    curl_setopt($ch, CURLOPT_REFERER,$REFERER);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $r = curl_exec($ch);
    curl_close($ch);
    return $r;
}

/*
Curl Post
*/

function Curl_Post($url,$data)
{
    $ch  =  curl_init ();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST,  1 );
    curl_setopt($ch, CURLOPT_POSTFIELDS,  $data );
    curl_setopt($ch, CURLOPT_COOKIE, "username=admin; password=b26cf77aeb5b2cb899d426e02cf91cce; addinfozblog=%7B%22dishtml5%22%3A0%2C%22chkadmin%22%3A1%2C%22chkarticle%22%3A1%2C%22levelname%22%3A%22%5Cu7ba1%5Cu7406%5Cu5458%22%2C%22userid%22%3A%221%22%2C%22useralias%22%3A%22admin%22%7D; timezone=8; __atuvc=1%7C32; CNZZDATA5541078=cnzz_eid%3D1875873004-1446271408-%26ntime%3D1446271408; _ga=GA1.1.659124922.1446618788; AJSTAT_ok_times=2");
    $r = curl_exec($ch);
    curl_close($ch);
    return $r;
}

//AMAZON API调用方法
function aws_signed_request($region, $params, $public_key, $private_key, $associate_tag=NULL, $version='2011-08-01')
{    
    /*
    Parameters:
        $region - the Amazon(r) region (ca,com,co.uk,de,fr,co.jp)
        $params - an array of parameters, eg. array("Operation"=>"ItemLookup",
                        "ItemId"=>"B000X9FLKM", "ResponseGroup"=>"Small")
        $public_key - your "Access Key ID"
        $private_key - your "Secret Access Key"
        $version (optional)
    */
    
    // some paramters
    $method = 'GET';
    $host = 'webservices.amazon.'.$region;
    $uri = '/onca/xml';
    
    // additional parameters
    $params['Service'] = 'AWSECommerceService';
    $params['AWSAccessKeyId'] = $public_key;
    // GMT timestamp
    $params['Timestamp'] = gmdate('Y-m-d\TH:i:s\Z');
    // API version
    $params['Version'] = $version;
    if ($associate_tag !== NULL) {
        $params['AssociateTag'] = $associate_tag;
    }
    
    // sort the parameters
    ksort($params);
    
    // create the canonicalized query
    $canonicalized_query = array();
    foreach ($params as $param=>$value)
    {
        $param = str_replace('%7E', '~', rawurlencode($param));
        $value = str_replace('%7E', '~', rawurlencode($value));
        $canonicalized_query[] = $param.'='.$value;
    }
    $canonicalized_query = implode('&', $canonicalized_query);
    
    // create the string to sign
    $string_to_sign = $method."\n".$host."\n".$uri."\n".$canonicalized_query;
    
    // calculate HMAC with SHA256 and base64-encoding
    $signature = base64_encode(hash_hmac('sha256', $string_to_sign, $private_key, TRUE));
    
    // encode the signature for the request
    $signature = str_replace('%7E', '~', rawurlencode($signature));
    
    // create request
    $request = 'http://'.$host.$uri.'?'.$canonicalized_query.'&Signature='.$signature;
    
    return $request;

}

//检测是否为蜘蛛及认证访问方式
function IsSpider($power)
{
    $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    $referer = strtolower($_SERVER['HTTP_REFERER']);
    $spider_arr = array('google','bing','yahoo','duckduck','local');
    if ($power='on' && !empty($agent) or !empty($referer)) {
        foreach ($spider_arr as $value) {
            if (stripos($agent,$value)!== false) {
                return true;
                break;
            }
        }
        foreach ($spider_arr as $value) {
            if (stripos($referer,$value)!== false) {
                return true;
                break;
            }
        }
    }else{
        return false;
    }
}




 ?>