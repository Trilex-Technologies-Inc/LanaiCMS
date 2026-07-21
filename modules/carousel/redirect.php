<?php
	/* load data to edit */
	$objbanner=new banner();

	$rs=$objbanner->Load("banId=".$_REQUEST['id']);
	
	if (!$rs) {
		/* no data to edit - show error message*/
		$sys_lanai->getErrorBox(_BANN_NOTFOUND);
	} else {
		$clickvar=$objbanner->banclick;
		$url=$objbanner->banurl;
		$objbanner->banclick=($clickvar+1);
		$objbanner->save();
		$sys_lanai->go2Page($url);
	}
?>