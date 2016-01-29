<?php 
/*
amacms接口
*/

//设置头部
header("Content-type:text/html;charset=UTF-8");
//包含数据库文件
include 'conn.php';
include 'config.php';




//获取Keywords
$keywords = $_GET['keywords'];

//开始调用API
$request = aws_signed_request($amacountry, array(
        'Operation' => 'ItemSearch',
        //'ItemId' => 'B00T4OEM7A',
        'Keywords' => $keywords,
        'SearchIndex' => 'All',
        'ResponseGroup'=>'Medium'
        //'ResponseGroup' => 'Small'
        ), $public_key, $private_key, $associate_tag);


//访问Api地址
// $response = Curl_Get($request);
$response = Curl_Get('http://localhost/jiekou/xml.xml');



//解析xml
if ($response === FLASE) {
    echo "Request failed.\n";
}else{
    $pxml = simplexml_load_string($response);
    $Item_Arr = $pxml->Items;
    $Item_Arr = json_encode($Item_Arr);
    $Item_Arr = json_decode($Item_Arr,true);
    // $retrun_arr =array();
    $New_Arr = array();
    foreach ($Item_Arr['Item'] as $key => $value) {
        // array_push($return_arr['ASIN'],$value->ASIN);
/*
        print 'ASIN: '.$value['ASIN']."<br>";//ASIN ASIN码
        print 'IMAGE: '.$value['LargeImage']['URL']."<br>";//image 图片
        print 'Binding: '.$value['ItemAttributes']['Binding']."<br>";//Binding
        print 'Brand: '.$value['ItemAttributes']['Brand']."<br>";//Brand 品牌
        print 'Label: '.$value['ItemAttributes']['Label']."<br>";//Label 标签
        print 'Manufacturer: '.$value['ItemAttributes']['Manufacturer']."<br>";//Manufacturer 制造商
        print 'ProductGroup: '.$value['ItemAttributes']['ProductGroup']."<br>";//ProductGroup 产品种类
        print 'ProductTypeName: '.$value['ItemAttributes']['ProductTypeName']."<br>";//ProductTypeName 产品种类
        print 'Publisher: '.$value['ItemAttributes']['Publisher']."<br>";//Publisher 发行人
        print 'Studio: '.$value['ItemAttributes']['Studio']."<br>";//Studio 工作室
        print 'Title: '.$value['ItemAttributes']['Title']."<br>";//Title 标题
        print 'FormattedPrice: '.$value['ItemAttributes']['ListPrice']['FormattedPrice']."<br>";//FormattedPrice 价格
        print 'Content: '.$value['EditorialReviews']['EditorialReview']['Content']."<br>";//Content 内容
        print "<hr>";
        */
        $New_Arr['item'][$key]['ASIN'] = $value['ASIN'];
        $New_Arr['item'][$key]['IMAGE'] = $value['LargeImage']['URL'];
        $New_Arr['item'][$key]['Brand'] = $value['ItemAttributes']['Brand'];
        $New_Arr['item'][$key]['Label'] = $value['ItemAttributes']['Label'];
        $New_Arr['item'][$key]['ProductGroup'] = $value['ItemAttributes']['ProductGroup'];
        $New_Arr['item'][$key]['ProductTypeName'] = $value['ItemAttributes']['ProductTypeName'];
        $New_Arr['item'][$key]['Title'] = $value['ItemAttributes']['Title'];
        $New_Arr['item'][$key]['FormattedPrice'] = $value['ItemAttributes']['ListPrice']['FormattedPrice'];
        $New_Arr['item'][$key]['Content'] = $value['EditorialReviews']['EditorialReview']['Content'];
    }
    foreach (GetReKeywords(10) as $key => $value) {
        $New_Arr['keywords'][$key] = $value;
    }
}

$New_Arr = json_encode($New_Arr);

print $New_Arr;





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
    $QueryKey = "SELECT * FROM $table WHERE id in($Rand)";
    $ReturnKey = $pdo->query($QueryKey);
    $RandKeyword = $ReturnKey->fetchAll(PDO::FETCH_COLUMN,1);
    return $RandKeyword;
}

/*
Curl
 */
function Curl_Get($durl){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $durl);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);
    curl_setopt($ch, CURLOPT_REFERER,_REFERER_);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
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

 ?>