<?php

include_once('library/main_lib.php');

$f3->set('DEBUG',3);

/**
 *	Index route for the api 
 * */
$f3->route('GET /',function(){

	echo json_encode(array("Error code"=>"404 Bad Request","message"=>"Nothing Found"));
});


/**
 *	This routes fetch all information from the database
 **/
$f3->route('GET /init',
	function($f3){
		$response = new main_lib();
		echo $response->init();
	}
);

/**
 *	This routes saves the database to the database
 **/
$f3->route('GET|POST /send',
	function($f3){
		$data = new main_lib();
		if($f3->get('POST')){
			$param = $f3->get('POST');
			echo $data->saveData($param['user'],$param['lat'],$param['long'],$param['gcmId']);
		}else{
			echo json_encode(array("Error Code"=>"405 Method Not Allowed","Msg"=>"Acceppts only Post request"));
		}
	}
);

/**
 *	This routes fetch the data from database as
 *	per unique key send by user
 **/
$f3->route('GET|POST /getLocation',
	function($f3,$param){
		$data = new main_lib();
		if($f3->get('POST')){
			$param = $f3->get('POST');
			echo $data->getLocation($param['ownKey'],$param['friendsKey']);
		}else{
			echo json_encode(array("Error Code"=>"405 Method Not Allowed","Msg"=>"Acceppts only Post request"));
		}
	}
);

/**
 *	This routes used to send Push notification 
 **/
$f3->route('GET|POST /sendNotification',
	function($f3){
		$data = new main_lib();
		if($f3->get('POST')){
			$param = $f3->get('POST');
			echo $data->sendNotification($param['userName'],$param['lat'],$param['long'],$param['gcmId']);
			
		}else{
			echo json_encode(array("Error Code"=>"405 Method Not Allowed","Msg"=>"Acceppts only Post request"));
		}
	}
);


/**
 *	This routes saves the unique Key to database
 *	sending by Iphone Team.
 **/
$f3->route('GET|POST /getUniqueKey',
	function($f3){
		$data = new main_lib();
		if($f3->get('POST')){
			$param = $f3->get('POST');
			echo $data->getUniqueKey($param['uniqueKey']);
		}else{
			echo json_encode(array("Error Code"=>"405 Method Not Allowed","Msg"=>"Acceppts only Post request"));
		}
	}
);

/**
 *	get all data send by IPhone Team
 *	and saves into Database
 *
 **/
$f3->route('GET|POST /saveData',
	function($f3){
		$data = new main_lib();
		if($f3->get('POST')){
			$param = $f3->get('POST');
			echo $data->iPhoneData($param['user'],$param['lat'],$param['long'],$param['deviceId']);
		}else{
			echo json_encode(array("Error Code"=>"405 Method Not Allowed","Msg"=>"Acceppts only Post request"));
		}
	}
);

/**
 *	This routes fetch the data from database as
 *	per unique key send by user
 **/
$f3->route('GET|POST /friendLocation',
	function($f3,$param){
		$data = new main_lib();
		if($f3->get('POST')){
			$param = $f3->get('POST');
			echo $data->friendLocation($param['ownKey'],$param['friendsKey']);
		}else{
			echo json_encode(array("Error Code"=>"405 Method Not Allowed","Msg"=>"Acceppts only Post request"));
		}
	}
);
/**
 *	Method to Show all the Device Id 
 *	That are saved to database 
 *
 **/
$f3->route('GET /iphoneInit',
	function($f3){
		$response = new main_lib();
		echo $response->iphoneInit();
	}
);
 

$f3->run();

?>
