<?php

/**
 * create database class to make connection 
 * and set db credentails globally
 *
 * */
class database {
    
    protected $googleKey = "AIzaSyBglyANH2DVt4ttLssMmc8gNkd5VUDHvNg";
    protected $appleDeviceKey = "C262659D-79BC-6024-11DA-C1709BCB7D2C";
    
    function __construct(){
         $db=new DB\SQL(
		    'mysql:host=localhost;port=3306;dbname=mapAPi_new',
		    'birisingh',
		    'PSmwmpfnHGYtKUaw'
	);
      return ($db) ;
        
    }
    
    public function googleKey(){
	return($this->googleKey);
    }

}

?>