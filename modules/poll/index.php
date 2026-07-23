<?php
	if (stripos($_SERVER['PHP_SELF'], "module.php") === false) {
		die ("You can't access this file directly...");
	}

	$module_name = basename(dirname(__FILE__));
	$modfunction = "modules/$module_name/module.php";
	include_once($modfunction);

	$poll = new Poll();
	$rsPoll = $poll->getPoll();
?>



<script type="text/javascript">
function MM_jumpMenu(targ, selObj, restore) {
	eval(targ + ".location='" + selObj.options[selObj.selectedIndex].value + "'");
	if (restore) selObj.selectedIndex = 0;
}
</script>

<div class="container my-4 p-4 border rounded shadow-sm bg-light">
	<h4 class="text-primary mb-3"><?=_POLL; ?></h4>

	<form name="form1" id="form1" class="row g-3 align-items-center">
		<div class="col-auto">
			<label for="menu1" class="col-form-label fw-semibold"><?=_POLL_SELECT; ?></label>
		</div>
		<div class="col-auto">
			<select class="form-select" name="menu1" id="menu1" onchange="MM_jumpMenu('parent',this,0)">
				<option selected disabled><?=_POLL_SELECT_TOPIC; ?></option>
				<?php while(!$rsPoll->EOF){ ?>
					<option value="<?=$_SERVER['PHP_SELF']; ?>?modname=<?=$module_name; ?>&mid=<?=$rsPoll->fields['pllId']; ?>">
						<?=$rsPoll->fields['pllTitle']; ?>
					</option>
				<?php $rsPoll->moveNext(); } ?>
			</select>
		</div>
	</form>

	<hr class="my-4">

	<?php
	if (!empty($_REQUEST['mid'])) {
		$rsPollItem = $poll->getPollItemById($_REQUEST['mid']);
	?>
		<div class="card border-primary mb-3">
			<div class="card-header bg-primary text-white">
				<strong><?=_POLL_RESULT;?> : <?=$rsPollItem->fields['pllTitle'];?></strong>
			</div>
			<div class="card-body">
				<?php
				$rsPollOptionItem = $poll->getPollOptionItemShow($_REQUEST['mid']);
				$total = $poll->getVoteTotal($_REQUEST['mid']);
				$totalx = ($total == 0) ? 1 : $total;

				while(!$rsPollOptionItem->EOF){
					$percent = ($rsPollOptionItem->fields['ppoScore'] / $totalx) * 100;
				?>
				<div class="mb-3">
					<label class="form-label fw-semibold"><?=$rsPollOptionItem->fields['ppoTitle'];?></label>
					<div class="progress" style="height: 20px;">
						<div class="progress-bar bg-success" role="progressbar" 
							style="width: <?=$percent;?>%;" 
							aria-valuenow="<?=$percent;?>" aria-valuemin="0" aria-valuemax="100">
							<?=sprintf("%0.2f", $percent);?>%
						</div>
					</div>
					<small class="text-muted">(<?=$rsPollOptionItem->fields['ppoScore'];?> votes)</small>
				</div>
				<?php
					$rsPollOptionItem->moveNext();
				} // while
				?>
				<div class="alert alert-info mt-4" role="alert">
					<strong><?=_POLL_TOTAL;?> :</strong> <?=$total;?>
				</div>
			</div>
		</div>
	<?php } ?>
</div>
