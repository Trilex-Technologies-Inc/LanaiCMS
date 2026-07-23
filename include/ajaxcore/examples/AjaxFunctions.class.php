<?php

require_once("../AjaxCore.class.php");  // first we include the AjaxCore class
class AjaxFunctions extends AjaxCore 
{ 
 function __construct()
 {
    $this->setup();
    parent::__construct();
 } 

 function setup()
 {
    $this->setCurrentFile("AjaxFunctions.class.php");
 }

 function checkUsername()
 {
    $user=$this->request['username'];
    $code=array(); // define an array to store our JavaScript code;
    if($user=="test") 
        { // username exists
        $code[]=$this->htmlDisable("registerButton"); // disable register button
        $code[]=$this->htmlInner("resultsDiv","<font color='red'>Username already exists</font");  // sets some text on the on div "resultsDiv" 
        }
    else
        { // username not exists
        $code[]=$this->htmlEnable("registerButton"); // enables register button
        $code[]=$this->htmlInner("resultsDiv","<font color='green'>Username Ok</font");   // sets some text on the on div "resultsDiv" 
        } 

    echo $this->arrayToString($code); // converts our array into a printable JavaScript string
 }
}
new AjaxFunctions(); // do not forget this!
?>