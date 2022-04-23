<?php
// define new rss
header("Content-Type: application/rss+xml; charset=UTF-8");
$rss = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom"></rss>',LIBXML_NOERROR|LIBXML_ERR_NONE|LIBXML_ERR_FATAL);
$rss->addChild('channel');
$rss->channel->addChild('title','rss title');
$rss->channel->addChild('description','rss description');
$rss->channel->addChild('link','rss link');
$rss->channel->addChild('language','fa-ir');

// import feeds
$xml[0] = simpleXML_load_file('http://www.yjc.ir/fa/rss/10',"SimpleXMLElement");
$xml[1] = simpleXML_load_file('http://www.cinemakhabar.ir/Rss.aspx',"SimpleXMLElement");
$xml[2] = simpleXML_load_file('http://www.varzesh3.com/rss/all',"SimpleXMLElement");
$xml[3] = simpleXML_load_file('http://www.zoomit.ir/feed/',"SimpleXMLElement");


function sortbydate($a, $b)
{
    if ($a['pubDate'] == $b['pubDate']) {
        return 0;
    }
    return ($a['pubDate'] > $b['pubDate']) ? -1 : 1;
}



$t=0;
foreach($xml as $key=>$feed) {

// create new array from feeds
$k=0;	
foreach($feed->channel[0]->item as $post) {
	
	if($k<5) {
  
   $url=urlencode($post->link);
 $link=str_replace('%2F','/',$url);
  $link=str_replace('%3A',':',$link);
  $link=str_replace('%3F','?',$link);
  $link=str_replace('%26','&',$link);
  $link=str_replace('%3D','=',$link); 
  $glink=shortenUrl($link);
  
  $content=htmlspecialchars($post->description);
  $content=$content.$glink;
  $content=strip_tags($content,'<h4><img>');
  

  $mrss[]=array();
  $mrss[$t]['title']=$post->title;
  $mrss[$t]['description']=$content;
  $mrss[$t]['link']=$glink;
  $mrss[$t]['guid']=$link;
  
  $time=strtotime($post->pubDate);
  if($key==0){
	  
	  $time=$time+16200;
  }elseif($key==2) {
	  
	$time=$time-16200;  
	  
  }
  $mrss[$t]['pubDate']=$time;
  
   $t=$t+1;
 
 
	}
	$k=$k+1;
	
	
}

}

// sort array by date

usort($mrss,"sortbydate");

//create new feed

foreach($mrss as $k=>$v) {
	
	 $item = $rss->channel->addChild('item');
	  $item->addChild('title',htmlspecialchars($v['title']));
       $item->addChild('description', $v['description']);
        $item->addChild('link', 'https://telegram.me/berooz_tarin');
           $item->addChild('guid', $v['guid']);
            $item->addChild('pubDate',date(DATE_RSS,$v['pubDate'])) ;
	
}

//save feed
$r=$rss->saveXML();
echo $r;
?>