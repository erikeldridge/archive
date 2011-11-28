<?php
$feed = simplexml_load_file( "http://www.panda.org/rss/rss.cfm?EE0CAA83-0D3D-1276-648870865FBA23F9" );
// $feed = simplexml_load_file( "feed.xml" );

$data = array(
    'header' => array(
        'logo' => array(
            'link' => $feed->channel->image->link,
            'url' => $feed->channel->image->url
        )
    ),
    'navigation' => array(),
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

<pre>
    <? //var_dump($data);  ?>
</pre>

<style>
/* wrap app to limit style bleeding from parent */
.wrapper {
    
    /* define app-global settings */
    font-family: arial;
    font-size: 12pt;
}
.wrapper a {
    
    /* we don't ever want links underlined */
    text-decoration: none;
}

/* styles for area above nav */
.wrapper .header .logo {
   background-color: #ccc;
   padding: 10px;
}
.wrapper .header .logo .label {
    /* align label and image right in header (see img styling below) */
    float: right;
    
    /* put some space btwn label and img */
    margin-right: 10px;
}
.wrapper .header .logo img {
    float: right;
    
    /* img is linked, but we don't want the border around it */
    border: none;
}

/* navigation styles */
.wrapper .header .navigation {
   background-color: #000;
   color: #fff;
   padding: 10px;
}
.wrapper .header .navigation .label {
    margin-right: 10px;
    
    /* align label next to ul (see below) */
    float: left;
}
.wrapper .header .navigation ul {
    
    /* remove li bullet points*/
    list-style: none;
    
    /* align ul next to label */
    display: inline;
}
.wrapper .header .navigation li {
    font-weight: bold;
    
    /* add a bit of white space btwn nav items */
    margin-right: 10px;
    
    /* display items horizontally */
    float: left;
}

/* main list styles */
.wrapper .body {
    
    /* remove whitespace above list */
    margin-top: 0px;
    
    /* add whitespace around list */
    padding: 10px;
}
.wrapper .body li {
    margin-bottom: 10px;
    
    /* remove li bullet points*/
    list-style: none;
    
    /* kludge: position relative so we can position pubdate and morelink absolute (see below) */
    position: relative;
    
    /* kludge: if there isn't an image, account for missing space */
    min-height: 100px;

    /* display separator under each item */
    border-bottom: 1px solid #ccc;
    
    /* add whitespace above bottom separator */
    padding-bottom: 10px;
}
.wrapper .body li .image {
    
    /* align img left of text content */
    float: left;
    
    margin-right: 10px;
}
.wrapper .body li .category {
    font-weight: bold;
    margin-bottom: 10px;
}
.wrapper .body li .title {
    font-weight: bold;
    
    /* title is an a-tag.  display block so we can add a margin to it */
    display: block;
    margin-bottom: 10px;
}
.wrapper .body li .description {
    
    /* display block so more link doesn't wrap */
    display: block;
    
    color: #ccc;
}
.wrapper .body li .pubDate {
    
    /* align pub date to right of "more" link */
    float: right;
    
    /* position pub date in lower right corner */
    position: absolute;
    bottom: 10px;
    right: 10px;
    
    color: #ccc;
}

.wrapper .body li .moreLink {
    font-weight: bold;
    
    /* position link in lower left, but next to image (if there is one) */
    position: absolute;
    bottom: 10px;
}
</style>

<div class="wrapper">
    <div class="header">
        <div class="logo">
            <a href="<?= $data['header']['logo']['link'] ?>" class="image"><img src="<?= $data['header']['logo']['url'] ?>"/></a>
            <div style="clear:both"></div>
        </div>
        <div class="navigation">
            <span class="label">Visit: </span>
            <ul>
                <? foreach($data['navigation'] as $item): ?>
                    <li>
                        <a href="<?= $item['url'] ?>"><?= $item['title'] ?></a>
                    </li>
                <? endforeach ?>
            </ul>
            <div style="clear:both"></div>
        </div>
    </div>

    <ul class="body">
        <? foreach($data['body'] as $item): ?>
            <li>
                <div style="float:left">
                    <div class="image">
                        <img src="<?= $item['img_src'] ?>"/>
                    </div>
                </div>
                <div>
                    <yml:a params="canvas/large.php?id=<?= $item['article_id'] ?>" class="title"><?= $item['title'] ?></a>
                    <div class="description"><?= $item['description'] ?></div>
                    <div class="pubDate"><?= $item['pubDate'] ?></div>
                </div>
                <div style="clear:both"></div>
            </li>
        <? endforeach ?>
    </ul>
    
    <ul class="footer">
        <? foreach($data['navigation'] as $item): ?>
            <li>
                <a href="<?= $item['url'] ?>"><?= $item['title'] ?></a>
            </li>
        <? endforeach ?>
    </ul>
</div>
