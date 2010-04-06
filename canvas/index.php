<?php
$article_id = filter_var( $_GET['id'], FILTER_SANITIZESTRING );
if( $article_id ){
    $yml_include_url = "canvas/article.php?id=$article_id";
} else {
    $yml_include_url = "canvas/list.php";    
}
?>

<div class="wrapper">
    <div class="header">
    </div>
    <div id="body">
        <yml:include params="<?= $yml_include_url ?>" insert="body"></yml:include>
    </div>
    <ul class="footer">
    </ul>
</div>