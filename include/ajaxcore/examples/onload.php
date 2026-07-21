<?php
/**
 *						AjaxCore 1.1.0
 *  AjaxCore is a PHP framework that aims the ease development of rich 
 *  AJAX applications, using Prototype's JavaScript standard library.
 *  
 *  Copyright 2006 Mauro Niewolski (niewolski@users.sourceforge.net)
 *
 *  This a minimun test file to check out what the framework does
 *    just a button that return a random number with AJAX  
 *     in this case we'll use onLoad function to call
 *			a PHP function when page loads
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */
require_once("AjaxTest.class.php"); // We include the class that inherits from AjaxCore
$ajax=new AjaxTest(); // create an instance of the inherited class
$ajax->setJSCode("mybutton","\$('mybutton').disabled=true;","\$('mybutton').disabled=false;"); // (optional) we add some JSCode to execute before and after the AJAX request
?>
<html>
<head>
    <title>Ajax onLoad Test Page</title>
    <script type="text/javascript" src="../prototype.js"></script> <!-- include stantard prototype library -->
     <script type="text/javascript" src="../AjaxCore.js"></script> <!-- include AjaxCore library -->
    <?php echo $ajax->getJSCode(); /* print some header content to handle the results from the request */ ?>
</head>
<body>
This is a simple test file to check out the <a href="http://sourceforge.net/projects/ajaxcore/">AjaxCore</a> framework. <br />
When the page is loaded an AJAX request is being made to return a random number. <br />
<br />
<center>
	<?php echo $ajax->onLoad("getRandomNumber"); /* Bind an HTML object to an JavaScript event to call PHP function  */ ?>
	<div id="results" name="results" >  <!-- div where results will be placed -->
	</div>
</center>
Have fun!	 <br />
for more information please visit <a href="http://sourceforge.net/projects/ajaxcore/">AjaxCore</a> project page

</body>
</html>
