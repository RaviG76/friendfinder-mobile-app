<?php
class Connection {

    /**
     * All private connection variable are declared 
     * in this section
     */
    private static $host;
    private static $username;
    private static $password;
    private static $database;

    /**
     *	settings credentails to all respective variables
     */
    public function __construct() {
        self::$host = 'localhost'; 
        self::$username = 'root'; 
        self::$password = 'root'; 
        self::$database = 'bojioFinal'; 
    }

    /**
     *	creating connection to database 
     *  by creating PDO object and
     *  return back to function calling
     */
    private function connect() {
        return new PDO('mysql:host=' . Connection::$host . ';dbname=' . Connection::$database . '', Connection::$username, Connection::$password);
    }

    
    /**
     *	insert data to database using for
     *	for bulk insertion in users tables
     */
    public function executeQuery(){
	try{
	    $db = $this->connect();
	    for($i=0; $i < 1001 ; $i++){
		$colours = array('Harman','Simran','Karan','Raman','Sakshi','Anu','Harpreet','Neha','Megha');
		$user_colour = array_rand($colours);
		$email = array('harman@gmail.com','simran@gmail.com','karan@gmail.com','raman@gmail.com','sakshi@gmail.com','anu@gmai.com','harpreet@gmai.com','neha@gmail.com','megha@gmail.com');
		$userEmail = array_rand($email);
		$phoneNumber = array('123456','654321','256314','541236','642351','325416','541263','432651','254163');
		$phone = array_rand($phoneNumber);
		$insertData = $db->query("
					    INSERT INTO `users`(`id`, `name`, `email`, `password`, `phoneNumber`, `longitude`, `loginType`, `socialLoginId`, `activationString`, `verified`, `createdAt`, `friendsListing`, `intrestListing`, `isActive`)
					    VALUES
						('','".$colours[$user_colour]."','".$email[$userEmail]."','".$colours[$user_colour]."','".$phoneNumber[$phone]."','','','','','','','','','')
					 ");
		$exec = $db->exec($insertData);
	    }
	    
	}catch(EXCEPTION $e){
	    echo "Error Occureed".$e->getMessage();
	}
	echo "Data inserted Sucessfully";
    }
    
    /**
     *	Inserting data to friendsRequest Table
     *	in bulk 
     */
    public function friendsRequest(){
	try{
	    for($i=2523; $i < 3023 ; $i++ ){
		$db = $this->connect();
		$get = $db->query("select * from users where id = '".$i."'");
		$exec = $db->exec($get);
		$get->setFetchMode(PDO::FETCH_ASSOC);
		$data = $get->fetch();
		    $insertData = $db->query("
					    INSERT INTO `friendRequest`(`id`, `senderId`, `sendUser`, `sendToUserID`, `sendToUser`, `isAccepted`)
								VALUES ('','2049','Sakshi','".$i."','".$data['name']."','1')
					");
		    $exec = $db->exec($insertData);    
	    }
	echo "Query executed sucessfully";
	}catch(PDOException $e){
	    echo "Error Occured".$e->getMessage();
	}
    }
    
    /**
     *	getting friends list for desired user
     */
    public function friendList(){
	try{
	    $friendsList = array();
	    $db = $this->connect();
	    $get = $db->query("select id , sendToUser from friendRequest where ( senderId = 2022 OR sendToUserID = 2022 ) AND isAccepted = 1");
	    $run = $db->exec($get);
	    $get->setFetchMode(PDO::FETCH_ASSOC);
	    while($list = $get->fetch()){
		array_push($friendsList , array('id'=>$list['id'],'name'=>$list['sendToUser']));
    	    }
	echo "<pre>";
	print_r($friendsList);	    
	}catch(PDOException $e){
	    echo "error occured".$e->getMessage();
	}
    }
    /**
     *	getting list of friends of friends
     *	of desired users
     */
    public function fof(){
	try{
	    $fofList = array();
	    $db = $this->connect();
	    $get = $db->query("select sendToUserID , sendToUser from friendRequest
				    where ( senderId  in ( select sendToUserID from friendRequest where ( senderId = 2022 OR sendToUserID = 2022 ) AND isAccepted = 1 )
			        OR
				    sendToUserID in ( select sendToUserID from friendRequest where ( senderId = 2022 OR sendToUserID = 2022 ) AND isAccepted = 1 )
				
				    ) AND isAccepted = 1" );
	    $run = $db->exec($get);
	    $get->setFetchMode(PDO::FETCH_ASSOC);
	    while($list = $get->fetch()){
		array_push($fofList, array('id'=>$list['sendToUserID'],'name'=>$list['sendToUser']));
    	    }
	    echo "<pre>";
	    print_r($fofList);
	}catch(PDOException $e){
	    echo "Error Occured".$e->getMessage();
	}
    
    }
    
    /**
     *	friends Count function
     *	
     */
    public function countFriends(){
	try{
	    $friendsList = array();
	    $db = $this->connect();
	    $get = $db->query("select id , sendToUser from friendRequest where ( senderId = 2022 OR sendToUserID = 2022 ) AND isAccepted = 1");
	    $run = $db->exec($get);
	    $get->setFetchMode(PDO::FETCH_ASSOC);
	    while($list = $get->fetch()){
		array_push($friendsList , array('id'=>$list['id'],'name'=>$list['sendToUser']));
    	    }
	echo "<pre>";
	echo count($friendsList);	    
	}catch(PDOException $e){
	    echo "error occured".$e->getMessage();
	}
    }
}

$data = new Connection();
//echo $data->executeQuery();
//echo $data->friendsRequest();
//echo $data->friendList();
//echo $data->countFriends();
echo $data->fof();
?>

