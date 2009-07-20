<?php
if(isset($_GET['publish'])){//ping 2nd party to publish update
	$url = '{reseller domain}/docs_oauth_reseller/service/publish.php';
	$data = http_build_query($_POST);
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_exec($ch);
}elseif(isset($_GET['guid'])){//redirection back from reseller. guid check is just for sanity.
	$guid = $_GET['guid'];
	setcookie("yahoo_guid", $guid, time()+3600);
}
?>