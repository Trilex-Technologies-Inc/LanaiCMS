<?php
class LanaiPollVoting
{
    private $db; private $prefix;
    public function __construct($db,$prefix) { $this->db=$db; $this->prefix=$prefix; }
    private function query($sql) {
        $result=$this->db->Execute($sql);
        if ($result===false) throw new RuntimeException('Vote could not be saved.');
        return $result;
    }
    public function vote($pollId,$optionId,$ip,$now=null) {
        $pollId=(int)$pollId; $optionId=(int)$optionId; $now=$now??time();
        if ($pollId<1 || $optionId<1 || !filter_var($ip,FILTER_VALIDATE_IP)) return false;
        // Serialize votes for a poll, including its existing IP-based cooldown.
        $lock='lanai_poll_'.substr(hash('sha256',$this->prefix.':'.$pollId),0,48);
        if ((int)$this->db->GetOne('SELECT GET_LOCK('.$this->db->qstr($lock).', 3)')!==1) return false;
        try {
            $poll=$this->query('SELECT pllLag FROM '.$this->prefix.'poll WHERE pllId='.$pollId." AND pllActive='y'");
            $option=$this->query('SELECT ppoId FROM '.$this->prefix.'poll_option WHERE pllId='.$pollId.' AND ppoId='.$optionId." AND ppoTitle<>''");
            if ($poll->EOF || $option->EOF) return false;
            $last=$this->db->GetOne('SELECT MAX(pstTime) FROM '.$this->prefix.'poll_stat WHERE pllId='.$pollId.' AND pstIP='.$this->db->qstr($ip));
            if ($last===false) throw new RuntimeException('Voting history unavailable.');
            if ($last!==null && $now-(int)$last<max(1,(int)$poll->fields['pllLag'])) return false;
            $this->query('INSERT INTO '.$this->prefix.'poll_stat (pllId,pstIP,pstTime) VALUES ('.$pollId.','.$this->db->qstr($ip).','.$now.')');
            $this->query('UPDATE '.$this->prefix.'poll_option SET ppoScore=ppoScore+1 WHERE pllId='.$pollId.' AND ppoId='.$optionId);
            return $this->db->Affected_Rows()===1;
        } finally { $this->db->GetOne('SELECT RELEASE_LOCK('.$this->db->qstr($lock).')'); }
    }
}
