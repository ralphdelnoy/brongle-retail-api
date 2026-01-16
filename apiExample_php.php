<?php

header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');

function curl($url){
	$ch=curl_init();
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,30);
	curl_setopt($ch,CURLOPT_TIMEOUT,30);
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_AUTOREFERER,true);
	curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
	$out=curl_exec($ch);
	curl_close($ch);
	return $out;
}

$api_domain='demo.brongle.com';//domain of licence holder
$api_key='YOUR-API-KEY';//your api key
$api_page=1;//page to start with
$api_fields='id,saved';//article fields to request - a star '*' will result in requesting all avaialable fields

$act=(isset($_GET['act'])) ? $_GET['act'] : 'ini';

if($act=='ini'){
	$data=curl($api_domain.'/brongle/API_RET_V1/articles/feed.php?key='.$api_key.'&page='.$api_page.'&fields='.$api_fields);
	$jsonArr=json_decode($data,true);

	if(!isset($jsonArr['meta'],$jsonArr['DATA'])){
		die('no data available');
	}

	$totalPages=(int)$jsonArr['meta']['totalPages'];
	$act='loadArts';
}

if($act=='loadArts'){
	$totalPages=(isset($_GET['totalPages'])) ? (int)$_GET['totalPages'] : $totalPages;
	$page=(isset($_GET['page'])) ? (int)$_GET['page'] : $api_page;

	$api_fields='id,se_brand,se_serie,price';
	$data=curl($api_domain.'/brongle/API_RET_V1/articles/feed.php?key='.$api_key.'&page='.$page.'&fields='.$api_fields);
	$jsonArr=json_decode($data,true);

	if(!isset($jsonArr['DATA'])){
		die('no article data');
	}

	$articles=$jsonArr['DATA'];
	$rows=(isset($jsonArr['meta']['rows'])) ? (int)$jsonArr['meta']['rows'] : count($articles);

	for($a=0;$a<$rows;$a++){
		print 'id='.$articles[$a]['id'].
		      ' se_brand='.$articles[$a]['se_brand'].
		      ' se_serie='.$articles[$a]['se_serie'].
		      ' price='.$articles[$a]['price'].'<br>';
	}

	if($page < $totalPages){
		$page++;
        usleep(100000); //0.1 sec
		print '<meta http-equiv="refresh" content="0;url='.$_SERVER['PHP_SELF'].'?act='.$act.'&totalPages='.$totalPages.'&page='.$page.'">';$page.'">';
	}
	else{
		print 'All pages where successfully loaded<br>';
	}
}

?>

