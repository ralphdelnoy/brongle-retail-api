<?php

//curl method
function curl($url){
	$ch = curl_init();// create curl resource
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout in seconds
	curl_setopt($ch, CURLOPT_URL, $url);//set url
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//return the transfer as a string
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);//follow a location redirect
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);//follow a header redirect
	$output = curl_exec($ch);//output contains the output string
	curl_close($ch);// close curl resource to free up system resources	
	return $output;
}

$api_domain='https://demo.brongle.com';//fill in the domain of the end-point to be used
$api_key='YOUR-API-KEY';//your api key
$api_page=1;//page to start with
$api_fields='id,saved';//article fields to request - a star '*' will result in requesting all avaialable fields

$act=(isset($_GET['act'])) ? $_GET['act'] : 'ini';

if($act=='ini'){
	$data=curl($api_domain.'/brongle/API_RET_V1/articles/feed.php?key='.$api_key.'&page='.$api_page.'&fields='.$api_fields);
	$jsonArr = json_decode($data, true);//decode json string to associative array
	if(is_array($jsonArr)){
		//BEGIN get meta data
		$meta=$jsonArr['meta'];//array with meta data of json
		//tranfer meta data to locals
		$totalPages=$meta['totalPages'];
		$page=$meta['page'];//the current page number
		//END get meta data		
		$act='loadArts';
	}
	else{
		print "no data available";	
	}
}


if($act=='loadArts'){
	//catch up required get parameters
	$totalPages=(isset($_GET['totalPages'])) ? $_GET['totalPages'] : $totalPages;
	$page=(isset($_GET['page'])) ? $_GET['page'] : $page;
	$api_fields='id,se_brand,se_serie,price';//limit requesting fields to ID, SE_BRAND, SE_SERIE and PRICE
	$data=curl($api_domain.'/brongle/API_RET_V1/articles/feed.php?key='.$api_key.'&page='.$page.'&fields='.$api_fields);
	$jsonArr = json_decode($data, true);
	//BEGIN get meta data
	$meta=$jsonArr['meta'];//array with meta data of json
	$rows=$meta['rows'];//the number of articles that exsists in the current page
	//END get meta data
	//BEGIN get article data
	$articles=$jsonArr['DATA'];//array with articles data
		for($a=0; $a<$rows; $a++){
			//output for testing
			print 'id='.$articles[$a]['id'].' se_brand='.$articles[$a]['se_brand'].' se_serie='.$articles[$a]['se_serie'].' price='.$articles[$a]['price'].'<br>';
			//here your database insert/update handling
			//..
		}
	//END get article data	
	if($page<$totalPages){
		$page++;
		//relaunche this file to load next page
		print '<meta http-equiv="refresh" content="0 url=apiExample_php.php?totalPages='.$totalPages.'&page='.$page.'">';
	}
	else{
		print "All pages where succesfully loaded<br>";	
	}
	
}

?>
