<?php
/**
 *						AjaxCore 1.1.2
 *				http://ajaxcore.sourceforge.net/
 *
 *  AjaxCore is a PHP framework that aims the ease development of rich 
 *  AJAX applications, using Prototype's JavaScript standard library.
 *  
 *  Copyright 2007 Mauro Niewolski (niewolski@users.sourceforge.net)
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

$ajax->setJSCode("mybutton",$ajax->htmlDisable("mybutton"),$ajax->htmlEnable("mybutton")); 
/*  Here we add some JavaScript to execute before and after the AJAX request, notice that we may type manually 
	JavaScript code, ir call the build in functions that generates the appropiate JavaScript code, in this case
	to disable and enable buttons.
*/
?>
<html>
	<head>
		<title>AjaxCore 1.1.2 » Bind Example</title>
		<link rel="stylesheet" type="text/css" media="all" href="css/style.css" />
		<script type="text/javascript" src="../prototype.js"></script> <!-- include stantard prototype library -->
		<script type="text/javascript" src="../AjaxCore.js"></script> <!-- include AjaxCore library -->
		<?php echo $ajax->getJSCode(); /* print some header content to handle the results from the request */ ?>
	</head>
	<body>
		<table align="center" width="740">
			<tr>
				<td>
					<img src="images/ajaxcore.png" alt="AjaxCore" border="0">
				</td>
			</tr>
			<tr>
				<td class="main">
					<h2>Bind Example</h2>
					This is a simple example file to check out the <a href="http://ajaxcore.sourceforge.net">AjaxCore</a> framework. <br /><br />
					When the button is pressed, an AJAX request is being made to return a random number. <br />
					<br />
					In this case we'll use a simple <b>bind</b> between an HTML object and a PHP function.  <br /> <br />
					
					
					<input type="button" id="mybutton" name="mybutton" value="press me!" />
					<?php echo $ajax->bind("mybutton","onclick","getRandomNumber"); /* Bind an HTML object to a JavaScript event to call a PHP function  */ ?>
					
					<div id="results" name="results" >  
						<!-- div where results will be placed -->
					</div>
					<br />
					
					<font color="red">Please note that this is a testing page for common bindings, they all require the AjaxCore extended class to 
					<br /> be defined, this set of examples uses AjaxTest.class as the extended class.</font> <br /> <br />
					
					Press <a href="bind.phps">here</a> to download this page source file, also check the stuff below for more information.<br />
					<ul> <b>AjaxCore Examples</b>
						<li>AjaxTest Class (<a href="AjaxTest.class.phps">source</a>)</li>
						<li><a href="bind.php">Bind</a> (<a href="bind.phps">source</a>)</li>
						<li><a href="bindTimer.php">Bind timer</a> (<a href="bindTimer.phps">source</a>)</li>
						<li><a href="bindPeriodicalTimer.php">Bind periodical timer</a> (<a href="bindPeriodicalTimer.phps">source</a>)</li>
						<li><a href="onLoadBind.php">onLoad bind</a> (<a href="onLoadBind.phps">source</a>)</li>
						<li><a href="onLoadBindTimer.php">onLoad bind timer</a> (<a href="onLoadBindTimer.phps">source</a>)</li>
						<li><a href="onLoadBindPeriodicalTimer.php">onLoad bind periodical timer</a> (<a href="onLoadBindPeriodicalTimer.phps">source</a>)</li>
					</ul>
					<ul> <b>Other stuff</b>
						<li><a href="http://sourceforge.net/projects/ajaxcore/">AjaxCore Project Page</a></li>
						<li><a href="AjaxCore.class.html">AjaxCore Documentation</a></li>
						<li><a href="AjaxCore.class.phps">AjaxCore Source file</a></li>
					<li><a href="http://ajaxcore.sourceforge.net/">AjaxCore Support Forum</a></li>
					</ul>
				</td>
			</tr>
		</table>
		
	</body>
</html>

