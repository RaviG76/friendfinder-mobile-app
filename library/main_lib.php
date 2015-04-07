<?php

/**
 * inlcude main core library file base.php
 * to the main library to support framework
 * functionality
 * */
$f3=require_once('./lib/base.php');
include_once('./dbConfig.php');



/** Extending the database class and manage all
 *  manupulation in this class
 **/
class main_lib extends database {
   
    /**
     *	Extedning the parent class contructor 
     *	to get database connection
     **/
    public function fetch(){
	return (parent::__construct());
    }
    
    
    /**
     *	This method is used to store the lat and long
     *	info to the database along with username
     *
     * */
    public function saveData($user,$lat,$long,$gcmId){
	if(!empty($user) && !empty($lat) && !empty($long) && !empty($gcmId)){
	    try{
		
		$check = $this->fetch()->exec("select * from info where gcmId ='".$gcmId."' ");
		if(count($check) > 0 ){
		    $updation = $this->fetch()->exec("UPDATE `info` SET `users`='".$user."',`lat`='".$lat."',`longi`='".$long."' WHERE `gcmId`= '".$gcmId."'");
		    if($updation){
			$check1 = $this->fetch()->exec("select * from info where gcmId ='".$gcmId."' ");
			foreach($check1 as $data){
			    return json_encode(
				array("status"=>"DeviceId already registred",
				     "userInfo"=>array("user"=>$data['users'],
						       "lat"=>$data['lat'],
						       "long"=>$data['longi'],
						       "uniqueId"=>$data['uniqueKey'],
						       "gcmId"=>$gcmId
						     )
				     )
				 );
			}
		    }else{
			foreach($check as $data){
			    return json_encode(
				array("status"=>"DeviceId already registred",
				     "userInfo"=>array("user"=>$data['users'],
						       "lat"=>$data['lat'],
						       "long"=>$data['longi'],
						       "uniqueId"=>$data['uniqueKey'],
						       "gcmId"=>$gcmId
						     )
				     )
				 );
			}
		    }
		}else{
			$rand = rand(1000,9999);
			$data = $this->fetch()->exec("INSERT INTO `info`(`id`, `users`, `lat`, `longi`, `uniqueKey`, `gcmId`)
						      VALUES ('','".$user."','".$lat."','".$long."','".$rand."', '".$gcmId."')");
			if(!$data){
			    return json_encode(array("Error code"=>"405 Bad Request","msg"=>"Database error occured"));
			}else{
			    return json_encode(
					       array("status"=>"200 Ok Successfull",
						    "userInfo"=>array("user"=>$user,
								      "lat"=>$lat,
								      "long"=>$long,
								      "uniqueId"=>$rand,
								      "gcmId"=>$gcmId
								    )
						    )
						);
			    
			}
		}
	    }catch(PDOException $e){
		return json_encode(array("Error code"=>"405 Error Occured","msg"=>$e->getMessage()));
	    }
	    
	}else{
	    return json_encode(array("Error code"=>"400 Bad Request","msg"=>"Empty fields are not treated"));
	}
	
    }
    
    /**
     *	This method is used to fetch the all
     *	stored info in the database
     *
     * */
    public function init(){
	try{
	    $data = $this->fetch()->exec("select * from info");
	    return json_encode($data);
	}catch(PDOException $e){
	    return json_encode(array("Error code"=>"405 Error Occured","msg"=>$e->getMessage()));
	}
    }
    
    
    /**
     *	This method is used to fetch all info on
     *	behalf of the unique code send by user
     *	
     * */   
    public function getLocation($ownKey,$friendsKey){
	try{
	    if(!empty($ownKey) && !empty($friendsKey)){
		$result = $this->fetch()->exec("select * from info where uniqueKey = '".$ownKey."' limit 1 ");
		if(isset($result)&& isset($result[0]) ){
		    $friendsResult = $this->fetch()->exec("select * from info where uniqueKey = '".$friendsKey."' limit 1 ");
		    if(isset($friendsResult) && isset($friendsResult[0])){
			if($this->sendNotification($result[0]['users'],$result[0]['lat'],$result[0]['longi'],$result[0]['uniqueKey'],$friendsResult[0]['gcmId'])){
			    return json_encode(array("Error code"=>"200 Ok Sucessfully ","msg"=>"Push Notification sends to friend sucessfully"));
			}else{
			    return json_encode(array("Error code"=>"500 Internal server Error ","msg"=>"Error Occured during sending Push notification"));
			}
		    }else{
			return json_encode(array("Error code"=>"500 Internal server Error ","msg"=>"SomeThing happned Wrong here"));
		    }
		}else{
		    return json_encode(array("Error code"=>"404 Bad Request","msg"=>"Nothing found"));
		}
		
	    }else{
		return json_encode(array("Error code"=>"400 Bad Request","msg"=>"Empty fields are not treated"));
	    }
	}catch(PDOException $e){
	    return json_encode(array("Error code"=>"405 Error Occured","msg"=>$e->getMessage()));
	}
    }
    
    
    /**
     *	Function to show error in case of  404 error occured
     **/
    public function showError(){
	return json_encode(array("Error code"=>"400 Bad Request","msg"=>"Please pass all querystrings"));
    }
    
    
    /**
     *	Creating function which is used
     *	to send the puch notifications on
     *	mobile device using GCM Api
     *
     **/
    public function sendNotification($userName,$lat,$long,$uniquerKey,$gcmId) {

	$message = "Your friend send you a request";
    	$url = 'https://android.googleapis.com/gcm/send';
	$fields = array(
	    'registration_ids' => array($gcmId),
	    'data' => array("data"=>$message,"username"=>$userName,"lat"=>$lat,"long"=>$long,"uniqueKey"=>$uniquerKey),
	);

	$headers = array(
	    'Authorization: key= '.$this->googleKey ,
	    'Content-Type: application/json'
	);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
	$result = curl_exec($ch);
	if ($result === FALSE) {
	    return json_encode(array("Status"=>"sending request to GCm server failed failed" ,"msg"=>curl_error($ch)));
	}else{
	    // Close connection
	    curl_close($ch);
	}
	$data = json_decode($result);
	if($data->success == 1 ){
	     return json_encode(array("Status"=>"200 Ok SuccessFull " ,"msg"=>"Push Notification send successfully"));
	}else{
	    return json_encode(array("Status"=>"500 Error Occured" ,"msg"=>"some thing happend wrong ."));
	}
	
	
    }
    
    
    /**
     *	Save unique Key to Database send by Iphone Team
     *
     **/
    public function getUniqueKey($uniqueKey){
	if(!empty($uniqueKey)){
	    try{
		$data = $this->fetch()->exec("INSERT INTO `iPhone`(`id`,`uniqueKey`) VALUES ('','".$uniqueKey."')");
		if(!$data){
		    return json_encode(array("Error code"=>"405 Bad Request","msg"=>"Database error occured"));
		}else{
		    return json_encode( array("status"=>"200 Ok Successfull","userInfo"=>array("uniqueId"=>$uniqueKey)));
		    
		}
	    }catch(PDOException $e){
		return json_encode(array("Error code"=>"Database Exception Occured","msg"=>$e->getMessage()));
	    }
	}else{
	    return json_encode(array("Error code"=>"400 Bad Request","msg"=>"Empty field not treated"));
	}
    }
    
    
    /**
     * Save Data send by Iphone team 
     * 
     * */
    public function iPhoneData($user,$lat,$long,$deviceId){
	if(!empty($user) && !empty($lat) && !empty($long) && !empty($deviceId)){
	    try{
		$check = $this->fetch()->exec("select * from iphoneInfo where deviceId ='".$deviceId."' ");
		if(count($check) > 0 ){
		    //echo ("UPDATE `iphoneInfo` SET `users`='".$user."',`lat`='".$lat."',`longi`='".$long."' WHERE `deviceId`= '".$deviceId."'");
		   // die('reached here');
		    $updation = $this->fetch()->exec("UPDATE `iphoneInfo` SET `users`='".$user."',`lat`='".$lat."',`longi`='".$long."' WHERE `deviceId`= '".$deviceId."'");
		    if($updation){
			$check1 = $this->fetch()->exec("select * from iphoneInfo where deviceId ='".$deviceId."' ");
			foreach($check1 as $data){
			    return json_encode(
				array("status"=>"DeviceId already registred",
				     "userInfo"=>array("user"=>$data['users'],
						       "lat"=>$data['lat'],
						       "long"=>$data['longi'],
						       "uniqueId"=>$data['uniqueKey'],
						       "deviceId"=>$deviceId
						     )
				     )
				 );
			}
		    }else{
			foreach($check as $data){
			    return json_encode(
				array("status"=>"DeviceId already registred",
				     "userInfo"=>array("user"=>$data['users'],
						       "lat"=>$data['lat'],
						       "long"=>$data['longi'],
						       "uniqueId"=>$data['uniqueKey'],
						       "deviceId"=>$deviceId
						     )
				     )
				 );
			}
		    }
		}else{
			$rand = rand(1000,9999);
			$data = $this->fetch()->exec("INSERT INTO `iphoneInfo`(`id`, `users`, `lat`, `longi`, `uniqueKey`, `deviceId`)
						      VALUES ('','".$user."','".$lat."','".$long."','".$rand."', '".$deviceId."')");
			if(!$data){
			    return json_encode(array("Error code"=>"405 Bad Request","msg"=>"Database error occured"));
			}else{
			    return json_encode(
					       array("status"=>"200 Ok Successfull",
						    "userInfo"=>array("user"=>$user,
								      "lat"=>$lat,
								      "long"=>$long,
								      "uniqueId"=>$rand,
								      "deviceId"=>$deviceId
								    )
						    )
						);
			    
			}
		}
	    }catch(PDOException $e){
		return json_encode(array("Error code"=>"405 Error Occured","msg"=>$e->getMessage()));
	    }
	    
	}else{
	    return json_encode(array("Error code"=>"400 Bad Request","msg"=>"Empty fields are not treated"));
	}
	
    }
    
    /**
     *	getLocation Method for Iphone Team
     *  devolped in this section with
     *  push notification
     **/
    public function friendLocation($ownKey, $friendsKey){
	try{
	    if(!empty($ownKey) && !empty($friendsKey)){ 
		$result = $this->fetch()->exec("select * from iphoneInfo where uniqueKey = '".$ownKey."' limit 1 ");
		if(isset($result)&& isset($result[0]) ){
		    $friendsResult = $this->fetch()->exec("select * from iphoneInfo where uniqueKey = '".$friendsKey."' limit 1 ");
		    if(isset($friendsResult) && isset($friendsResult[0])){
			$data = $this->appleNotification($result[0]['users'],$result[0]['lat'],$result[0]['longi'],$friendsResult[0]['deviceId']);
			return $data ;
			//if($this->appleNotification($result[0]['users'],$result[0]['lat'],$result[0]['longi'],$friendsResult[0]['deviceId'])){
			//    return json_encode(array("Status"=>"200 Ok Sucessfully ","msg"=>"Push Notification sends to friend sucessfully"));
			//}else{
			//    return json_encode(array("Status"=>"500 Internal server Error ","msg"=>"Error Occured during sending Push notification"));
			//}
		    }else{
			return json_encode(array("Status"=>"400 Bad Request","msg"=>"Entred Friends Key is not in database"));
		    }
		}else{
		    return json_encode(array("Status"=>"400 Bad Request","msg"=>"OwnKey is not in database"));
		}
		
	    }else{
		return json_encode(array("Status"=>"400 Bad Request","msg"=>"Empty fields are not treated"));
	    }
	}catch(PDOException $e){
	    return json_encode(array("Status"=>"405 Error Occured","msg"=>$e->getMessage()));
	}
    }
    
    /**
     *	send Push notification to
     *	Apple Notification server
     *  using socket stream
     **/
    
    public function appleNotification($userName,$lat,$long, $deviceId){
	////////////////////////////////////////////////////////////////////////////////

	try {
		//$path = '/var/www/html/mobileApi/library/FinalCertDev.pem';
		$path = '/var/www/html/mobileApi/library/ck.pem';
		$deviceToken = $deviceId;
		$passphrase = 'Admin123#';
		$message = array("data"=>"Your friend send you a request","username"=>$userName,"lat"=>$lat,"long"=>$long);
	    	// Put your alert message here:
		@$ctx = stream_context_create();
		@stream_context_set_option($ctx, 'ssl', 'local_cert', $path);
		//stream_context_set_option($ctx, 'ssl', 'local_cert', 'ck.pem');
		@stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
	
		// Open a connection to the APNS server
		$fp = stream_socket_client(
			'ssl://gateway.sandbox.push.apple.com:2195', $err,
			$errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
	
		if (!$fp) {
			exit("Failed to connect: $err $errstr" . PHP_EOL);
		}

	    // Create the payload body
	    $body['aps'] = array(
		    'alert' => $message,
		    'sound' => 'default'
		    );
	    
	    // Encode the payload as JSON
	    $payload = json_encode($body);
	    // Build the binary notification
	    
	    $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
	    
	    // Send it to the server
	    $result = fwrite($fp, $msg, strlen($msg));
	    if (!$result) {
		return json_encode(array("Status"=>"500 Internal server Error ","msg"=>"Error Occured during sending Push notification"));
	    }else {
		    return json_encode(array("Status"=>"200 Ok Sucessfully ","msg"=>"Push Notification sends to friend sucessfully"));
	    }
	}catch(EXCEPTION $e){
	     return json_encode(array("Status"=>"405 Error Occured","msg"=>$e->getMessage()));
	}
	
	
	
	// Close the connection to the server
	fclose($fp);
    }
    /**
     *	This method is used to fetch the all
     *	stored info in the database
     *
     * */
    public function iphoneInit(){
	try{
	    $data = $this->fetch()->exec("select * from iphoneInfo");
	    return json_encode($data);
	}catch(PDOException $e){
	    return json_encode(array("Error code"=>"405 Error Occured","msg"=>$e->getMessage()));
	}
    }
    
    
}//closing the main_lib class

?>

