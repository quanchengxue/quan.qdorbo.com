<?php 
//载入配置文件
include_once 'config.php';
include_once 'conn.php';
include_once 'function.php';
//判断是否为认证访问方式
if (IsSpider('on') != true) {
  include_once '404.html';
  exit();
}


/*
=====标签备注======

得到外链数组结果(从linkurl.php)         $linkurl_arr
外链数组结果(写入到Mysql)               $RetLink
获取随机关键字方法                      GetReKeywords(10)

*/


//如果没获取到关键字，则随机提取，作为主页显示
if ($_GET['short'] == null) {
  $id = mt_rand(1,50000);
  $QueryKey = "SELECT $table.keywords FROM `$table` WHERE `id` = '$id'";
  $ReturnKey = $pdo->query($QueryKey);
  // $RandKeyword = $ReturnKey->fetchAll(PDO::FETCH_COLUMN,1);
  $RetKeywords = $ReturnKey->fetch();
  $keywords = $RetKeywords['keywords'];
  $keywords_id = $RetKeywords['id'];
}else{
  //解析shortcode
  $short_code = $_GET['short'];
  $QueryKey = "SELECT $table.keywords FROM `$table` WHERE `short` = '$short_code'";
  $ReturnKey = $pdo->query($QueryKey);
  // $RandKeyword = $ReturnKey->fetchAll(PDO::FETCH_COLUMN,1);
  $RetKeywords = $ReturnKey->fetch();
  //获取Keywords
  $keywords = $RetKeywords['keywords'];
  $keywords_id = $RetKeywords['id'];

  //这里开始进行外链读取
  //测试来路地址
  $jiekou_url = 'http://quan.qdorbo.com/linkurl.php?link_keywords='.$keywords;
  $pageurl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
  $linkurl = Curl_Get($jiekou_url,$pageurl);
  $linkurl_arr = json_decode($linkurl,true);
  echo "<hr>这里是linkurl_arr get<hr>";
  // var_dump($linkurl_arr);
  //循环写入
  $sql_value = '';
  foreach ($linkurl_arr as $value) {
    // $sql_value .= "($value['url'] , $value['keywords'])";
    // $sql_value .= '('.$value['url'].','.$value['keywords'].'),';
    $sql_value .= "('".$value['url']."','".$value['keywords']."'),";
  }
  $sql_value = rtrim($sql_value,',');
  // var_dump($sql_value);
  $count = $pdo-> exec("INSERT INTO `amazon_linkurl` (`url`, `keywords`) VALUES $sql_value");
  echo $pdo -> lastinsertid()+9;
  $Query = "SELECT * FROM `amazon_linkurl` ORDER BY `id` DESC LIMIT 0, 10";
  $Return = $pdo->query($Query);
  $RetLink = $Return->fetchAll(PDO::FETCH_ASSOC);
  var_dump($RetLink);
}





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
$response = Curl_Get($request,$pageurl);

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
        $New_Arr[$key]['ASIN'] = $value['ASIN'];
        $New_Arr[$key]['IMAGE'] = $value['LargeImage']['URL'];
        $New_Arr[$key]['Brand'] = $value['ItemAttributes']['Brand'];
        $New_Arr[$key]['Label'] = $value['ItemAttributes']['Label'];
        $New_Arr[$key]['ProductGroup'] = $value['ItemAttributes']['ProductGroup'];
        $New_Arr[$key]['ProductTypeName'] = $value['ItemAttributes']['ProductTypeName'];
        $New_Arr[$key]['Title'] = $value['ItemAttributes']['Title'];
        $New_Arr[$key]['FormattedPrice'] = $value['ItemAttributes']['ListPrice']['FormattedPrice'];
        $New_Arr[$key]['Content'] = $value['EditorialReviews']['EditorialReview']['Content'];
    }
    // foreach (GetReKeywords(10) as $key => $value) {
    //     $New_Arr['keywords'][$key] = $value;
    // }
}

$url = 'http://localhost/jiekou/jiekou.php';
// Curl_Get($url);
$weburl = 'http://www.baidu.com';

$associate_tag = 'chengxuequan-20';

$return = json_decode(Curl_Get($url),true);

shuffle($return['item']);//打乱数组



 ?>
<!DOCTYPE html>
<!--[if IE 8]> <html class="ie ie8" lang="en-US"> <![endif]-->
<!--[if IE 9]> <html class="ie ie9" lang="en-US"> <![endif]-->
<!--[if gt IE 9]><!-->
<html lang="en-US">
 <!--<![endif]-->
 <head> 
  <meta charset="UTF-8" /> 
  <title>  Advanced Home</title> 
  <meta name="viewport" content="width=device-width, initial-scale=1" />  
  <link rel="stylesheet" id="motive-fonts-css" href="http://fonts.googleapis.com/css?family=Source+Sans+Pro%3A400%2C600%2C700%7COpen+Sans%3A400%2C400italic%2C600%2C700%7CRoboto%3A900" type="text/css" media="all" /> 
  <link rel="stylesheet" id="motive-core-css" href="http://<?php echo $_SERVER['SERVER_NAME'].'/'?>theme/style.css?ver=1.0.0" type="text/css" media="all" /> 
  <link rel="stylesheet" id="motive-lightbox-css" href="http://<?php echo $_SERVER['SERVER_NAME'].'/'?>theme/lightbox.css?ver=1.0.0" type="text/css" media="all" /> 
  <link rel="stylesheet" id="motive-font-awesome-css" href="http://<?php echo $_SERVER['SERVER_NAME'].'/'?>theme/fontawesome/css/font-awesome.min.css?ver=1.0.0" type="text/css" media="all" /> 
  <link rel="stylesheet" id="motive-skin-css" href="http://<?php echo $_SERVER['SERVER_NAME'].'/'?>theme/layout-magazine.css?ver=1.0.0" type="text/css" media="all" /> 
  <link rel="stylesheet" id="motive-responsive-css" href="http://<?php echo $_SERVER['SERVER_NAME'].'/'?>theme/responsive.css?ver=1.0.0" type="text/css" media="all" /> 
  <script type="text/javascript" src="http://motive.theme-sphere.com/wp-includes/js/jquery/jquery.js?ver=1.11.1"></script> 
  <script type="text/javascript" src="http://motive.theme-sphere.com/wp-includes/js/jquery/jquery-migrate.min.js?ver=1.2.1"></script> 
  <meta name="generator" content="WordPress 4.0.9" /> 
  <link rel="canonical" href="http://motive.theme-sphere.com/advanced-home/" /> 
  <link rel="shortlink" href="http://motive.theme-sphere.com/?p=270" /> 
  <script>
(function($) {
	var c = (document.cookie.match('(^|; )'+ 'demo-data' +'=([^;]*)')||0)[2];
	if (c && c.match(/non-boxed/)) {
		$('<style id="demo-layout-pre">body { visibility: hidden; }</style>').appendTo('head');
	}
})(jQuery);
</script> 
  <noscript>
   <style> .wpb_animate_when_almost_visible { opacity: 1; }</style>
  </noscript> 
  <!--[if lt IE 9]>
<script src="http://motive.theme-sphere.com/wp-content/themes/motive/js/html5.js" type="text/javascript"></script>
<script src="http://motive.theme-sphere.com/wp-content/themes/motive/js/selectivizr.js" type="text/javascript"></script>
<![endif]--> 
 </head> 
 <body class="page page-id-270 page-template page-template-page-blocks-php boxed no-sidebar"> 
  <div class="main-wrap"> 
   <div class="top-bar"> 
    <div class="wrap"> 
     <section class="top-bar-content cf"> 
      <div class="trending-ticker"> 
       <span class="heading">Breaking</span> 
       <ul> 
        <li><a href="http://motive.theme-sphere.com/2014/10/review-of-the-5-best-pizza-places/" title="Review of the 5 Best Pizza Places">Review of the 5 Best Pizza Places</a></li> 
        <li><a href="http://motive.theme-sphere.com/2014/09/titanic%e2%80%99s-most-memorable-scenes/" title="Titanic’s Most Memorable Scenes">Titanic’s Most Memorable Scenes</a></li> 
        <li><a href="http://motive.theme-sphere.com/2014/09/jennifer-takes-on-a-modern-office/" title="Jennifer takes on a modern office">Jennifer takes on a modern office</a></li> 
        <li><a href="http://motive.theme-sphere.com/2014/09/evernote-coming-to-apple-watch/" title="Evernote coming to Apple Watch">Evernote coming to Apple Watch</a></li> 
        <li><a href="http://motive.theme-sphere.com/2014/09/league-of-legends-coming-to-ps4/" title="League of Legends Coming to PS4">League of Legends Coming to PS4</a></li> 
        <li><a href="http://motive.theme-sphere.com/2014/09/baby-on-the-way-for-beyonce/" title="Baby on the way for Beyonce?">Baby on the way for Beyonce?</a></li> 
        <li><a href="http://motive.theme-sphere.com/2014/09/dota-2-reclaiming-inactive-names/" title="Dota 2 Reclaiming Inactive Names">Dota 2 Reclaiming Inactive Names</a></li> 
        <li><a href="http://motive.theme-sphere.com/2014/09/eas-priorities-include-fps-and-console/" title="EA’s Priorities Include FPS and Console">EA’s Priorities Include FPS and Console</a></li> 
        <li><a href="http://motive.theme-sphere.com/2014/09/a-day-inside-need-for-speed/" title="A Day Inside Need for Speed">A Day Inside Need for Speed</a></li> 
        <li><a href="http://motive.theme-sphere.com/2014/09/your-guide-to-holiday-tv/" title="Your Guide to Holiday TV">Your Guide to Holiday TV</a></li> 
       </ul> 
      </div> 
      <div class="search-box"> 
       <a href="#" class="top-icon fa fa-search"><span class="visuallyhidden">Search</span></a> 
       <div class="search"> 
        <form action="http://motive.theme-sphere.com/" method="get"> 
         <input type="text" name="s" class="query" value="" placeholder="To search, type and press enter." /> 
        </form> 
       </div> 
       <!-- .search --> 
      </div> 
      <div class="textwidget">
       <ul class="social-icons cf"> 
        <li><a href="#" class="icon fa fa-twitter" title="Twitter"><span class="visuallyhidden">Twitter</span></a></li> 
        <li><a href="#" class="icon fa fa-facebook" title="Facebook"><span class="visuallyhidden">Facebook</span></a></li> 
        <li><a href="#" class="icon fa fa-linkedin" title="LinkedIn"><span class="visuallyhidden">LinkedIn</span></a></li> 
        <li><a href="#" class="icon fa fa-pinterest" title="Pinterest"><span class="visuallyhidden">Pinterest</span></a></li> 
        <li><a href="#" class="icon fa fa-rss" title="RSS"><span class="visuallyhidden">RSS</span></a></li> 
       </ul>
      </div> 
     </section> 
    </div> 
   </div> 
   <div id="main-head" class="main-head"> 
    <div class="wrap"> 
     <header> 
      <div class="title"> 
       <a href="http://motive.theme-sphere.com/" title="Motive" rel="home"> <span class="text"><span class="main-color">M</span>otive</span> 
        <div class="slogan">
         .. clean magazine theme
        </div> </a> 
      </div> 
      <div class="right"> 
       <div class="adwrap-widget"> 
        <div class="visible-lg"> 
         <img src="http://motive.theme-sphere.com/wp-content/uploads/2014/10/demo-720-ad.jpg" alt="Leaderboard ad" /> 
        </div> 
        <div class="visible-md"> 
         <img src="http://motive.theme-sphere.com/wp-content/uploads/2014/10/demo-468-ad.jpg" alt="Ad" /> 
        </div> 
        <div class="visible-sm visible-xs"> 
         <img src="http://motive.theme-sphere.com/wp-content/uploads/2014/10/demo-320-ad.jpg" alt="Ad" /> 
        </div> 
       </div> 
      </div> 
     </header> 
    </div> 
    <div class="wrap nav-wrap"> 
     <nav class="navigation cf"> 
      <div class="mobile" data-search="1"> 
       <a href="#" class="selected"> <span class="text">Navigate</span><span class="current"></span> <i class="hamburger fa fa-bars"></i> </a> 
      </div> 
      <div class="menu-main-menu-container">
       <ul id="menu-main-menu" class="menu">
        <li id="menu-item-522" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-home current-menu-ancestor current-menu-parent menu-item-has-children menu-item-522"><a href="http://motive.theme-sphere.com/">Home</a></li>
       </ul>
      </div> 
     </nav> 
    </div> 
   </div>
   <div class="main wrap"> 
    <div class="ts-row cf"> 
     <div class="col-8 main-content cf"> 
      <div id="post-270" class="post-270 page type-page status-publish page-content"> 
       <div class="ts-row block cf wpb_row"> 
        <div class="col-8 content-column wpb_column vc_column_container"> 
         <div class="ts-row block cf wpb_row vc_inner">
          <div class="wpb_column vc_column_container col-12">
           <div class="wpb_wrapper">
            <div class="block-wrap">
             <h4 class="section-head cf cat-border-"> <span class="title">Recent Stories</span> <a href="" class="more">View all <i class="fa fa-caret-right"></i></a> </h4> 
             <div class="posts-list listing-blog ">
             <?php foreach ($New_Arr as $value) {
                if ($value['Title'] == null & strlen($value['Content'])<1) {
                  continue;
                }
                if (strlen($value['Content']) > 220) {
                  $value['Content'] = substr($value['Content'],0,220);
                }
              ?>
              <article class="post-<?php echo mt_rand(1,999999);?> post type-post status-publish format-standard has-post-thumbnail category-reviews tag-coffee tag-food-2 cf" itemscope="" itemtype="http://schema.org/Article"> 
               <div class="post-thumb">
                <a href="http://www.amazon.com/dp/<?php echo $value['ASIN']; ?>?tag=<?php echo $associate_tag; ?>" itemprop="url" class="image-link"><img width="359" height="201" class="attachment-motive-highlight-block wp-post-image" alt="<?php echo $value['Title']; ?>" title="<?php echo $value['Title']; ?>" itemprop="image" srcset="<?php echo $value['IMAGE']; ?>._AC_UL200_SR200,200_.jpg" /> <span class="image-overlay"></span> <span class="meta-overlay"> <span class="meta"> <span class="post-format "><i class="fa fa-file-text-o"></i></span> </span> </span> </a><span class="comment-count"><?php echo mt_rand(66,666); ?></span>
               </div> 
               <div class="content"> 
                <time datetime="<?php echo date ( DATE_ATOM ,  mktime ( 0 ,  0 ,  0 ,  7 ,  1 ,  2000 )); ?>" itemprop="datePublished"><?php echo date ( "F j, Y," );?></time> 
                <span class="review-meta"><span class="number"><?php echo $value['FormattedPrice']; ?></span></span> 
                <h2 itemprop="name" class="post-title"><a href="http://www.amazon.com/dp/<?php echo $value['ASIN']; ?>?tag=<?php echo $associate_tag; ?>" itemprop="url" class="post-link"><?php echo $value['Title']; ?></a></h2> 
                <div class="excerpt text-font">
                 <p><?php echo $value['Content']; ?>…<span class="read-more"><a href="http://www.amazon.com/dp/<?php echo $value['ASIN']; ?>?tag=<?php echo $associate_tag; ?>">Buy to Amazon</a></span></p> 
                </div> 
               </div> 
              </article>
              <?php } ?>
              <section class="navigate-posts">
  <div class="previous"><span class="main-color title"><i class="fa fa-angle-left"></i> Previous Article</span><span class="link"><a href="http://motive.theme-sphere.com/2014/07/why-rogers-new-book-is-so-good/" rel="prev">Why Roger’s New Book is so Good</a></span>  </div> 
  <div class="next"><span class="main-color title">Next Article <i class="fa fa-angle-right"></i></span><span class="link"><a href="http://motive.theme-sphere.com/2014/09/why-children-dont-talk-to-parents/" rel="next">Why Children Don’t Talk to Parents</a></span> </div>
</section>
             </div> 
            </div> 
           </div>
          </div>
         </div> 
        </div> 
        <div class="col-4 sidebar wpb_column vc_column_container"> 
         <div class="wpb_widgetised_column wpb_content_element"> 
          <div class="wpb_wrapper"> 
           <ul>
            <li id="bunyad-tabbed-recent-widget-2" class="widget tabbed">
             <ul class="tabs-list cf">
              <li class="active"> <a href="#" data-tab="1">Popular</a> </li>
              <li class=""> <a href="#" data-tab="2">Recent</a> </li>
              <li class=""> <a href="#" data-tab="3">Comments</a> </li>
             </ul>
             <div class="tabs-data">
              <div class="tab-posts active popular" id="recent-tab-1">
               <ol class="popular">
               
               <?php foreach (GetReKeywords(10) as $value) {?>
                <li> <a href="http://<?php echo $_SERVER['SERVER_NAME'].'/'.$value['short']; ?>/" title="<?php echo $value['keywords']; ?>"><?php echo $value['keywords']; ?></a></li>
                <?php } ?>
               </ol> 
              </div> 
              <div class="tab-posts  recent" id="recent-tab-2"> 
              </div> 
              <div class="tab-posts  comments" id="recent-tab-3"> 
               <div class="latest-comments"> 
               </div> 
              </div> 
             </div> </li> 
            <li id="bunyad-latest-reviews-widget-2" class="widget latest-reviews"> <h5 class="widget-title section-head cf main-color"><span class="title">Latest Reviews</span></h5>
             <ul class="posts-list"> 
              <li> <a href="http://motive.theme-sphere.com/2014/09/titanic%e2%80%99s-most-memorable-scenes/" class="image-link small"><img width="72" height="60" class="attachment-post-thumbnail wp-post-image" alt="Sexy  woman" title="Titanic’s Most Memorable Scenes" srcset="http://motive.theme-sphere.com/wp-content/uploads/2014/09/girl-5-72x60.jpg, http://motive.theme-sphere.com/wp-content/uploads/2014/09/girl-5-72x60@2x.jpg 2x" /> <span class="image-overlay"></span> <span class="meta-overlay"> <span class="meta"> <span class="post-format "><i class="fa fa-file-text-o"></i></span> </span> </span> </a> 
               <div class="content cf"> 
                <time datetime="2014-09-20T19:30:32+00:00">September 20, 2014 </time> 
                <span class="review-meta"><span class="number">7/10</span></span> 
                <a href="http://motive.theme-sphere.com/2014/09/titanic%e2%80%99s-most-memorable-scenes/" title="Titanic’s Most Memorable Scenes"> Titanic’s Most Memorable Scenes</a> 
               </div> </li> 
              <li> <a href="http://motive.theme-sphere.com/2014/07/why-rogers-new-book-is-so-good/" class="image-link small"><img width="72" height="60" class="attachment-post-thumbnail wp-post-image" alt="modern couch and wood bookcase in a living room" title="Why Roger’s New Book is so Good" srcset="http://motive.theme-sphere.com/wp-content/uploads/2014/09/bookcase-72x60.jpg, http://motive.theme-sphere.com/wp-content/uploads/2014/09/bookcase-72x60@2x.jpg 2x" /> <span class="image-overlay"></span> <span class="meta-overlay"> <span class="meta"> <span class="post-format "><i class="fa fa-file-text-o"></i></span> </span> </span> </a> 
               <div class="content cf"> 
                <time datetime="2014-07-20T19:12:18+00:00">July 20, 2014 </time> 
                <span class="review-meta"><span class="number">9/10</span></span> 
                <a href="http://motive.theme-sphere.com/2014/07/why-rogers-new-book-is-so-good/" title="Why Roger’s New Book is so Good"> Why Roger’s New Book is so Good</a> 
               </div> </li> 
              <li> <a href="http://motive.theme-sphere.com/2014/06/acer-aspire-with-retina-display/" class="image-link small"><img width="72" height="60" class="attachment-post-thumbnail wp-post-image" alt="Startup Stock Photos" title="Acer Aspire With Retina Display" srcset="http://motive.theme-sphere.com/wp-content/uploads/2014/09/gadgets-laptop1-72x60.jpg, http://motive.theme-sphere.com/wp-content/uploads/2014/09/gadgets-laptop1-72x60@2x.jpg 2x" /> <span class="image-overlay"></span> <span class="meta-overlay"> <span class="meta"> <span class="post-format "><i class="fa fa-file-text-o"></i></span> </span> </span> </a> 
               <div class="content cf"> 
                <time datetime="2014-06-20T18:35:36+00:00">June 20, 2014 </time> 
                <span class="review-meta"><span class="number">4/10</span></span> 
                <a href="http://motive.theme-sphere.com/2014/06/acer-aspire-with-retina-display/" title="Acer Aspire With Retina Display"> Acer Aspire With Retina Display</a> 
               </div> </li> 
              <li> <a href="http://motive.theme-sphere.com/2014/05/snap-photos-like-a-pro-with-canon-powershot/" class="image-link small"><img width="72" height="60" class="attachment-post-thumbnail wp-post-image" alt="Leaves in autumn forest" title="Snap Photos Like a Pro With Canon PowerShot" srcset="http://motive.theme-sphere.com/wp-content/uploads/2014/05/leaves-72x60.jpg, http://motive.theme-sphere.com/wp-content/uploads/2014/05/leaves-72x60@2x.jpg 2x" /> <span class="image-overlay"></span> <span class="meta-overlay"> <span class="meta"> <span class="post-format "><i class="fa fa-file-text-o"></i></span> </span> </span> </a> 
               <div class="content cf"> 
                <time datetime="2014-05-14T18:21:30+00:00">May 14, 2014 </time> 
                <span class="review-meta"><span class="number">8/10</span></span> 
                <a href="http://motive.theme-sphere.com/2014/05/snap-photos-like-a-pro-with-canon-powershot/" title="Snap Photos Like a Pro With Canon PowerShot"> Snap Photos Like a Pro With Canon PowerShot</a> 
               </div> </li> 
             </ul> </li> 
            <li id="bunyad-latest-comments-widget-2" class="widget latest-comments"> <h5 class="widget-title section-head cf main-color"><span class="title">Recent Comments</span></h5>
              <ul class="posts-list">
                <?php foreach ($return['keywords'] as $value) {?>
                <li> <a href="http://www.baidu.com/<?php echo $value; ?>" title="<?php echo $value; ?>"><?php echo $value; ?></a></li>
                <?php } ?>
              </ul>
             </li>
           </ul> 
          </div> 
         </div> 
        </div> 
        <div class="col-8 wpb_column vc_column_container"> 
        </div> 
       </div>
       <div class="ts-row block cf wpb_row"> 
        <div class="col-4 wpb_column vc_column_container"> 
        </div> 
        <div class="col-4 wpb_column vc_column_container"> 
        </div> 
        <div class="col-4 wpb_column vc_column_container"> 
        </div> 
       </div> 
      </div> 
     </div> 
    </div> 
    <!-- .ts-row --> 
   </div> 
   <!-- .main --> 
   <footer class="main-footer dark"> 
    <section class="upper-footer"> 
     <div class="wrap"> 
      <ul class="widgets ts-row cf"> 
       <li class="widget widget_nav_menu"><h3 class="widget-title">Random Links</h3>
        <div class="menu-top-footer-links-container">
         <ul id="menu-top-footer-links" class="menu">
         <?php foreach ($RetLink as $value) {?>
          <li class="menu-item menu-item-type-taxonomy menu-item-object-category"><a href="<?php echo $value['url']; ?>"><?php echo $value['keywords']; ?></a></li>
          <?php } ?>
         </ul>
        </div></li>
      </ul> 
     </div> 
    </section> 
    <section class="lower-footer"> 
     <div class="wrap"> 
      <div class="widgets"> 
       <div class="textwidget">
        Copyright &copy; 2014 ThemeSphere
       </div> 
       <div class="textwidget">
        <ul class="social-icons cf"> 
         <li><a href="#" class="icon fa fa-twitter" title="Twitter"><span class="visuallyhidden">Twitter</span></a></li> 
         <li><a href="#" class="icon fa fa-facebook" title="Facebook"><span class="visuallyhidden">Facebook</span></a></li> 
         <li><a href="#" class="icon fa fa-linkedin" title="LinkedIn"><span class="visuallyhidden">LinkedIn</span></a></li> 
         <li><a href="#" class="icon fa fa-pinterest" title="Pinterest"><span class="visuallyhidden">Pinterest</span></a></li> 
         <li><a href="#" class="icon fa fa-rss" title="RSS"><span class="visuallyhidden">RSS</span></a></li> 
        </ul>
       </div> 
      </div> 
     </div> 
    </section> 
   </footer> 
  </div> 
  <!-- .main-wrap --> 
  <script type="text/javascript" src="http://motive.theme-sphere.com/wp-includes/js/comment-reply.min.js?ver=4.0.9"></script> 
  <script type="text/javascript" src="http://motive.theme-sphere.com/wp-content/themes/motive/js/bunyad-theme.js?ver=4.0.9"></script> 
  <script type="text/javascript" src="http://motive.theme-sphere.com/wp-content/themes/motive/js/lightbox.js?ver=4.0.9"></script> 
  <script type="text/javascript" src="http://motive.theme-sphere.com/wp-content/themes/motive/js/owl.carousel.min.js?ver=4.0.9"></script> 
  <script type="text/javascript" src="http://s0.wp.com/wp-content/js/devicepx-jetpack.js?ver=201601"></script> 
 </body>
</html>