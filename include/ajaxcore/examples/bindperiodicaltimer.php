<?php
/**
 *						AjaxCore 1.1.0
 *  AjaxCore is a PHP framework that aims the ease development of rich 
 *  AJAX applications, using Prototype's JavaScript standard library.
 *  
 *  Copyright 2006 Mauro Niewolski (niewolski@users.sourceforge.net)
 *
 *     This a minimun test file to check out what the framework does
 *     in this case we'll use bind function to associate an HTML ID to
 *		a Periodical Timer that when expires will call a PHP function 
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
?>
<html>
<head>
    <title>Ajax bindPeriodicalTimert Test Page</title>
    <script type="text/javascript" src="../prototype.js"></script> <!-- include stantard prototype library -->
     <script type="text/javascript" src="../AjaxCore.js"></script> <!-- include AjaxCore library -->
    <?php echo $ajax->getJSCode(); /* print some header content to handle the results from the request */ ?>
</head>
<body>
This is a simple test file to check out the <a href="http://sourceforge.net/projects/ajaxcore/">AjaxCore</a> framework. <br />
When the button is pressed,a <b>Timer</b> starts with the milliseconds specified on the bindPeriodicalTimer call, and when it get to zero, an AJAX request is being made to return a random number. 
<br />
<br /> After the AJAX request is made, the timer starts all over again.
<br />

<center>
	<input type="button" id="mybutton" name="mybutton" value="press me!">
	<?php echo $ajax->bindPeriodicalTimer("mybutton","onclick","getRandomNumber","mytimer","5000"); /* Bind a PeriodicalTimer to an HTML object to an JavaScript event to call PHP function */ ?>
	<div id="results" name="results" >  <!-- div where results will be placed -->
	</div>
</center>
Have fun!	 <br />
for more information please visit <a href="http://sourceforge.net/projects/ajaxcore/">AjaxCore</a> project page

</body>
</html>
