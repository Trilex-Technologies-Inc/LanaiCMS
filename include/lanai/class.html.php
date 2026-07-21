<?php

class Html {

    function openForm($name,$method="post",$action="",$class="",$other="") {
        ?><form id="<?=$name?>" name="<?=$name?>" method="<?=$method?>" action="<?=$action?>" class="<?=$class; ?>" <?=$other?>><?php
    }

    function closeForm() {
        ?></form><?php
    }

    function textLabel($text){
        ?><?=$text; ?> : <?php
    }

    function textBox($name,$size=30,$maxlenght=30,$value="",$class="",$other=""){
        ?><input type="text" id="<?=$name;?>" name="<?=$name;?>" size="<?=$size; ?>" maxlength="<?=$maxlenght; ?>" value="<?=$value; ?>" class="<?=$class; ?>" <?=$other; ?>/><br /><?php
    }

    function passwordBox($name,$size=30,$maxlenght=30,$value="",$class="",$other=""){
        ?><input type="password" id="<?=$name;?>" name="<?=$name;?>" size="<?=$size; ?>" maxlength="<?=$maxlenght; ?>" value="<?=$value; ?>" class="<?=$class; ?>" <?=$other; ?>/><br /><?php
    }

    function textArea($name,$row=10,$col=40,$value="",$class="",$other="") {
        ?><textarea id="<?=$name;?>" name="<?=$name;?>" rows="<?=$row;?>" cols="<?=$col;?>" class="<?=$class; ?>" <?=$other; ?>><?=$value;?></textarea><br /><?php
    }

    function submitButton($name,$value="Sumbit",$class="",$other="") {
        ?><input type="submit" id="<?=$name;?>" name="<?=$name; ?>" value="<?=$value; ?>" class="<?=$class; ?>" <?=$other; ?>/><?php
    }

    function resetButton($name,$value="Reset",$class="",$other="") {
        ?><input type="reset" id="<?=$name;?>" name="<?=$name; ?>" value="<?=$value; ?>" class="<?=$class; ?>" <?=$other; ?>/><?php
    }

    function buttonButton($name,$value="Button",$class="",$other="") {
        ?><input type="button" id="<?=$name;?>" name="<?=$name; ?>" value="<?=$value; ?>" class="<?=$class; ?>" <?=$other; ?>/><?php
    }

}

?>
