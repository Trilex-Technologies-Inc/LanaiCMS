<center>
<?php  $sys_lanai->ajax->setJSCode("mybutton",$sys_lanai->ajax->htmlDisable("mybutton"),$sys_lanai->ajax->htmlEnable("mybutton"));  ?>
<div id="results" name="results" > </div><br><br>
<input type="text" id="name" name="name" value="Anoochit Chalothorn">
<input type="button" id="mybutton" name="mybutton" value="press me!" class="inputButton">
<?php echo $sys_lanai->ajax->bind("mybutton","onclick","getRandomNumber","name"); ?>
</center>