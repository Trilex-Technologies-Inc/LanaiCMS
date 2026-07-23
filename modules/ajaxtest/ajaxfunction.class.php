<?php

if ((stripos($_SERVER['PHP_SELF'], "module.php") === false) AND  (stripos($_SERVER['PHP_SELF'], "setting.php") === false)) {
	require_once("../../include/ajaxcore/AjaxCore.class.php");  
} else {
	require_once("include/ajaxcore/AjaxCore.class.php");  // first we include the AjaxCore class
}


class AjaxFunctions extends AjaxCore { 
	
	 function __construct() {
	    $this->setup();
	    parent::__construct();
	 } 
	 
	 function setup()	 {
	    $this->setCurrentFile("modules/ajaxtest/ajaxfunction.class.php");
	    $this->setPlaceHolder("results");
	    $this->setUpdating("loading...");
	 } 
	 
	function getRandomNumber(){
		$user=$this->request['name'];
		sleep(1); // don't use this on a production environment
		echo "Hello ".$user.". My random number is ".rand(0,999) ;
	}
	
}

new AjaxFunctions(); // do not forget this!

?>
