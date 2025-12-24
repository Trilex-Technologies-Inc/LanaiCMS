<?php
  
  	include_once("class.NewsPager.php");
	include_once("class.GroupPager.php");
	include_once("class.NewsListPager.php");
	/**
	 * News
	 * 
	 * @package 
	 * @author Administrator
	 * @copyright Copyright (c) 2006
	 * @version $Id: module.php,v 1.3 2007/05/09 08:18:27 redlinesoft Exp $
	 * @access public
	 **/
	class News {
		var $uid;
		var $db;
		var $cfg;
		var $_sql;
		
		function News() {
			global $db,$cfg;
			$this->db=$db;
			$this->cfg=$cfg;
            if (!empty($_SESSION['uid']))
                $this->uid=$_SESSION['uid'];
			//$this->db->debug=true;		
		}
		
		function getNews($num=0){
			if ($num==0) {
			   $sql="SELECT * FROM ".$this->cfg['tablepre']."news ORDER BY nwsCreate DESC";
			} else {
				$sql="SELECT * FROM ".$this->cfg['tablepre']."news ORDER BY nwsCreate DESC LIMIT $num";
			}
			
			$this->_sql=$sql;
			$rs=$this->db->execute($sql);	
			return $rs;
		}
		
		function getShowNews($num=0){
			if ($num==0) {
			   $sql="SELECT * FROM ".$this->cfg['tablepre']."news  WHERE nwsActive='y' ORDER BY nwsCreate DESC";
			} else {
				$sql="SELECT * FROM ".$this->cfg['tablepre']."news  WHERE nwsActive='y' ORDER BY nwsCreate DESC LIMIT $num";
			}
			
			$this->_sql=$sql;
			$rs=$this->db->execute($sql);	
			return $rs;
		}
		
		function getShowNewsByGroup($rows=30,$gid){
			$sql="SELECT * FROM ".$this->cfg['tablepre']."news  WHERE nwsActive='y' AND chnId=$gid ORDER BY nwsCreate DESC";
			$this->_sql=$sql;
			$pager=new NewsListPager($this->db,$this->_sql,true);
			$pager->Render($rows);
		}
		
		function getNumGroup($cid=0){
			$sql="SELECT COUNT(*) FROM ".$this->cfg['tablepre']."news WHERE chnId=$cid AND nwsActive='y' ";
			$this->_sql=$sql;
			$rs=$this->db->execute($sql);	
			return ($rs->fields[0]);
		}
		
		function getGroup($num=0){
			$sql="SELECT * FROM ".$this->cfg['tablepre']."news_channel ORDER BY chnTitle ASC";
			$this->_sql=$sql;
			$rs=$this->db->execute($sql);	
			return $rs;
		}
		
		function getShowGroup($num=0){
			$sql="SELECT * FROM ".$this->cfg['tablepre']."news_channel WHERE chnActive='y' ORDER BY chnTitle ASC";
			$this->_sql=$sql;
			$rs=$this->db->execute($sql);	
			return $rs;
		}
		
		function getGroupList(){
			$sql="SELECT * FROM ".$this->cfg['tablepre']."news_channel WHERE chnActive='y' ORDER BY chnTitle ASC";
			$this->_sql=$sql;
			$rs=$this->db->execute($sql);	
			return $rs;
		}
		
		function getNewsVisible($num=0){
			if ($num==0) {
			   $sql="SELECT * FROM ".$this->cfg['tablepre']."news WHERE nwsActive='y' ORDER BY nwsCreate DESC";
			} else {
				$sql="SELECT * FROM ".$this->cfg['tablepre']."news WHERE nwsActive='y' ORDER BY nwsCreate DESC LIMIT $num";
			}
			
			$this->_sql=$sql;
			$rs=$this->db->execute($sql);	
			return $rs;
		}
		
		function getNewsById($nid){
			$sql="SELECT * FROM ".$this->cfg['tablepre']."news 
					WHERE nwsId=$nid";
			$this->_sql=$sql;
			$rs=$this->db->execute($sql);	
			return $rs;
		}  
		
		function getGroupById($nid){
			$sql="SELECT * FROM ".$this->cfg['tablepre']."news_channel 
					WHERE chnId=$nid";
			$this->_sql=$sql;
			$rs=$this->db->execute($sql);	
			return $rs;
		}  
		
		//nwsId  chnId  userId  nwsTitle  nwsPreface  nwsBody  nwsActive  nwsCreate  nwsModified  

        function setNewNews($chnId, $nwsTitle, $nwsPreface, $nwsBody)
        {
            $sql = "
        INSERT INTO {$this->cfg['tablepre']}news
        (chnId, userId, nwsTitle, nwsPreface, nwsBody, nwsCreate, nwsModified, nwsActive)
        VALUES (?, ?, ?, ?, ?, NOW(), NOW(), 'y')
    ";

            $rs = $this->db->Execute($sql, [
                (int)$chnId,
                (int)$this->uid,
                $nwsTitle,     
                $nwsPreface,   
                $nwsBody       
            ]);

            if (!$rs) {
                echo 'DB Error: ' . $this->db->ErrorMsg();
                return false;
            }

            return true;
        }

        function setNewGroup($chnTitle,$chnDescription){
			$sql="INSERT INTO ".$this->cfg['tablepre']."news_channel 
					(chnTitle,chnDescription,chnActive,chnModified) 
					VALUES ('".$chnTitle."','".$chnDescription."','y',NOW())";
			$rs=$this->db->execute($sql);	
			return $rs;	
		}

        function setEditNews($nwsId, $chnId, $nwsTitle, $nwsPreface, $nwsBody)
        {
            $sql = "
        UPDATE {$this->cfg['tablepre']}news
        SET
            chnId        = ?,
            userId       = ?,
            nwsTitle     = ?,
            nwsPreface   = ?,
            nwsBody      = ?,
            nwsModified  = NOW()
        WHERE nwsId = ?
    ";

            $rs = $this->db->Execute($sql, [
                (int)$chnId,
                (int)$this->uid,
                $nwsTitle,     
                $nwsPreface,   
                $nwsBody,      
                (int)$nwsId
            ]);

            if (!$rs) {
                echo 'DB Error: ' . $this->db->ErrorMsg();
                return false;
            }

            return true;
        }


        function setEditGroup($chnId,$chnTitle,$chnDescription){
			$sql="UPDATE ".$this->cfg['tablepre']."news_channel 
					SET chnTitle='".$chnTitle."',chnDescription='".$chnDescription."',
						chnModified=NOW()
					WHERE chnId=$chnId";
			$rs=$this->db->execute($sql);	
			return $rs;
		}
		
		function setDeleteNews($mid){
			$sql="DELETE FROM ".$this->cfg['tablepre']."news 
					WHERE nwsId=".$mid;
			$rs=$this->db->execute($sql);	
			return $rs;	
		}
		
		function setDeleteNewsGroup($mid){
			$sql="DELETE FROM ".$this->cfg['tablepre']."news_channel 
					WHERE chnId=".$mid;
			$rs=$this->db->execute($sql);	
			return $rs;	
		}
		
		function setNewsActive($mid,$value){
			$sql="UPDATE ".$this->cfg['tablepre']."news  
					SET nwsActive='".$value."'
					WHERE nwsId=".$mid;
			$rs=$this->db->execute($sql);	
			return $rs;		
		}
		
		function setGroupActive($mid,$value){
			$sql="UPDATE ".$this->cfg['tablepre']."news_channel   
					SET chnActive='".$value."'
					WHERE chnId=".$mid;
			$rs=$this->db->execute($sql);	
			return $rs;		
		}
		
		function getNewsGroupTitleById($cid) {
			$sql="SELECT * FROM ".$this->cfg['tablepre']."news_channel 
					WHERE chnId=$cid";
			$rs=$this->db->execute($sql);
			return ($rs->fields['chnTitle']);
		}
		
		function getNewsGroupCombo($name,$value){
			$sql="SELECT * FROM ".$this->cfg['tablepre']."news_channel 
					ORDER BY chnTitle ASC";
			$rs=$this->db->execute($sql);	
			?><select name="<?=$name; ?>" style="width:200px; " ><?
			while(!$rs->EOF){
				if ($value==$rs->fields['chnId']) {
				    $selected="selected";
				} else {
					$selected="";
				}
			?>
				<option <?=$selected; ?> value="<?=$rs->fields['chnId']; ?>" ><?=$rs->fields['chnTitle']; ?></option>
			<?
				$rs->movenext();
			} // while
			?></select><?
		}
				
		
		function getNewsList($rows=30){
			$this->getNews();
			$pager=new NewsPager($this->db,$this->_sql,true);
			$pager->Render($rows);
		}
		
		function getNewsListPager($row=20){
			$this->getShowNews();
			$pager=new NewsListPager($this->db,$this->_sql,true);
			$pager->Render($row);
		}
		
		function getNewsGroupList($rows=20){
			$this->getGroup();
			$pager=new GroupPager($this->db,$this->_sql,true);
			$pager->Render($rows);
		}
		
		  function getLinkBlogPost($title,$link){
		  	$link=urlencode($link);
		  	$title=urlencode($title);
        ?>
       <a href="http://del.icio.us/post?url=<?=$link;?>&title=<?=$title;?>" target="_blank">
       <img src="modules/news/images/delicious.png" border="0">
       </a>
       <a href="http://digg.com/submit?phase=2&url=<?=$link;?>&title=<?=$title;?>" target="_blank">
       <img src="modules/news/images/digg.png" border="0">
       </a>
       <a href="http://reddit.com/submit?url=<?=$link;?>&title=<?=$title;?>" target="_blank">
       <img src="modules/news/images/reddit.png" border="0">
       </a>
       <a href="http://ma.gnolia.com/bookmarklet/add?url=<?=$link;?>&title=<?=$title;?>" target="_blank">
       <img src="modules/news/images/magnoliacom.png" border="0">
       </a>
       <a href="http://www.newsvine.com/_tools/seed&save?u=<?=$link;?>&h=<?=$title;?>" target="_blank">
       <img src="modules/news/images/newsvine.png" border="0">
       </a>
       <a href="http://www.furl.net/storeIt.jsp?u=<?=$link;?>&t=<?=$title;?>" target="_blank">
       <img src="modules/news/images/furl.png" border="0">
       </a>
       <a href="http://www.google.com/bookmarks/mark?op=add&bkmk=<?=$link;?>&title=<?=$title;?>" target="_blank">
       <img src="modules/news/images/google.png" border="0">
       </a>
       <a href="http://myweb2.search.yahoo.com/myresults/bookmarklet?u=<?=$link;?>&t=<?=$title;?>" target="_blank">
       <img src="modules/news/images/yahoo.png" border="0">
       </a>
       <a href="http://technorati.com/cosmos/search.html?url=<?=$link;?>" target="_blank">
       <img src="modules/news/images/technorati.png" border="0">
       </a>
       <a href="http://blogs.icerocket.com/search?q=<?=$link;?>" target="_blank">
       <img src="modules/news/images/icerocket.png" border="0">
       </a>&nbsp;       
       
        <?

        }
		
	}
		
?>