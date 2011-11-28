<?php
// $feed = simplexml_load_file( "http://www.panda.org/rss/rss.cfm?EE0CAA83-0D3D-1276-648870865FBA23F9" );
$feed = simplexml_load_file( "feed.xml" );

$data = array(
    'header' => array(
        'logo' => array(
            'link' => $feed->channel->image->link,
            'url' => $feed->channel->image->url
        )
    ),
    'navigation' => array(
        array(
            'text' => 'list',
            'url' => 'large.php'
        )
    ),
    'body' => array()
);

//define list items
foreach( $feed->channel->item as $item ){
    
    //parse description so we can easily extract img tag and remove html
    $doc = new DOMDocument();
    $doc->loadHTML( $item->description );
    
    if( $doc->getElementsByTagName( 'img' )->item(0) ){
        $img_src = $doc->getElementsByTagName( 'img' )->item(0)->getAttribute('src');
    } else {
        $img_src = null;
    }
    
    $description = utf8_decode( $doc->getElementsByTagName( 'html' )->item(0)->nodeValue );
    
    $data['body'][] = array(
        'img_src' => $img_src,
        'title' => $item->title,
        'article_id' => substr( $item->link, strpos( $item->link, '=' ) + 1 ),
        'description' => substr( $description, 0, 150 ).'...',
        'pub_date' => $item->pubDate,
    );
    
    if( 5 == count($data['body']) ){
        break;
    }
}
header('Content-type: text/html; charset=utf-8');
?>


