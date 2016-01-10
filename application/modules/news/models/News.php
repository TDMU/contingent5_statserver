<?php

function cmp($a, $b) {
	if ($a['NEWSDATE'] == $b['NEWSDATE']) return 0;
	return ($a['NEWSDATE'] > $b['NEWSDATE']) ? -1 : 1;
}
	

class News_Model_News {
/*	protected $_cfg = null;
	
	public function __construct() {
		$this->_cfg = Zend_Registry::get ( 'cfg' );
	}*/
	
	public function getChannelInfo($channel, $ctxid, $newscount = 5) {
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		
		$field = @is_numeric($channel) ? 'NODEID' : 'NODE_KEY';
		$sql = "select CT.NODEID, CT.TITLE, CT.CREATEDATE, IC.LANGUAGE, IC.DESCRIPTION, CT.NODE_KEY as CHANNEL 
from CONTENTTREE CT
inner join INFO_CHANNELS IC
on (IC.NODEID = CT.NODEID)
where CT.{$field} = ?";
		$channelInfo = $db->fetchRow($sql, $channel);
		
		$channelInfo['CREATEDATE'] = strtotime($channelInfo['CREATEDATE']);
		
		$sql = 
'select VN.NEWSDATE, VN.NEWSID, VN.TITLE, VN.DESCRIPTION, VN.URL
from V_NEWS VN
where VN.CHANNELID = ?
and VN.VISIBLE = 1
and VN.NEWSDATE <= current_date
and VN.EXPIREDATE >= current_date';

		$news = $db->fetchAll($sql, $channelInfo['NODEID']);
		
		$sql = 		
"select first {$newscount} VN.NEWSDATE, VN.NEWSID, VN.TITLE, VN.DESCRIPTION, VN.URL
from V_NEWS VN
where VN.CHANNELID = ?
and VN.VISIBLE = 1
and VN.EXPIREDATE is null
order by VN.NEWSDATE desc";
		
		
		$news = array_merge($news, $db->fetchAll($sql, $channelInfo['NODEID']));
		
		usort($news, "cmp");
		
		$urlHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('url');
		
		foreach ($news as &$item) {
			$item['NEWSDATE'] = strtotime($item['NEWSDATE']);
			$item['NEWSDATE_STR'] = date('d.m.Y', $item['NEWSDATE']);
//			$item['NEWSDATE'] = date('Y/m/d', strtotime($item['NEWSDATE']));
			
			$detailsFileExists = file_exists(DOCS_PATH . $item['NEWSID'] . '.xml');
			if(!$detailsFileExists) {
				if($item['URL']) $item['URL'] = $urlHelper->simple('show', 'page', 'site', array('docid' => $item['URL'])); 
			} else {
				$item['URL'] = $urlHelper->simple('show', 'page', 'site', array('docid' => $item['NEWSID'])) . '?ctxid=' . $ctxid;
			}
		}
		
		$res = new stdClass();
//		$res->TITLE = $channelInfo['TITLE'];
//		$res->CREATEDATE = $channelInfo['CREATEDATE'];
		$res->info = $channelInfo;
		$res->news = $news;
//		$channelInfo['news'] = $news;
		
		return $res;
	}
	
}