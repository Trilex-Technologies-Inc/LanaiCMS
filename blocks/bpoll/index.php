<?php 
	include_once("modules/poll/module.php");
	include_once("modules/poll/language/lang-".$cfg['lang'].".php");
	
	$bpoll=new Poll();
	$rsBPoll=$bpoll->getPollItemShow();
	
	while(!$rsBPoll->EOF){
		?>
		<table width="150px" cellpadding="3" cellspacing="1" border="0">
		<form method="post" action="module.php">
		<input type="hidden" name="mid" value="<?=$rsBPoll->fields['pllId']; ?>">
		<input type="hidden" name="modname" value="poll">
		<input type="hidden" name="mf" value="pllvote">
		<tr>
			<td><span class="txtContentTitle"><?=$rsBPoll->fields['pllTitle']; ?></span></td>
		</tr>
		<?php 
			$rsBPollOption=$bpoll->getPollOptionItemShow($rsBPoll->fields['pllId']);
			while(!$rsBPollOption->EOF){
		?>
		<tr>
			<td>
				<input type="radio" name="voteChoice" value="<?=$rsBPollOption->fields['ppoId']; ?>" class="radioButton">
				<?=$rsBPollOption->fields['ppoTitle']; ?>
			</td>
		</tr>
		<?php 
				$rsBPollOption->moveNext();
			} // while
		?>
		<tr>
			<td align="center">
			<input type="submit" value="<?=_POLL_VOTE; ?>" class="inputButton" >
			<input type="button" value="<?=_POLL_RESULT; ?>" class="inputButton" onClick="javascript:goPage('module.php?modname=poll&mid=<?=$rsBPoll->fields['pllId']; ?>')">
			</td>
		</tr>
		</form>
		</table>
		<?php
		$rsBPoll->moveNext();
	} // while

?>
<script>
function goPage(url) {
	location.href=url;	
}
</script>