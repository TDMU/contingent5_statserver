<?php

require_once 'Zend/Controller/Action.php';

class News_RssController extends Zend_Controller_Action {

	public function init() {
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
	}
	
	public function showAction() {
		$newsModel = new News_Model_News();
		$channel = $newsModel->getChannelInfo($this->_request->getParam('docid'), $this->_request->getParam('ctxid'), 20);
		$baseUrl = "http://{$_SERVER['HTTP_HOST']}";

//		$baseUrl = $this->_request->baseUrl;
//		$tempbook = $books->current();
//		$pubDate = $tempbook->date_entered;

		$feedArray = array(
			'title' => $channel->info['TITLE'],
			'link' => "{$baseUrl}{$_SERVER['REQUEST_URI']}",
			'description' => $channel->info['DESCRIPTION'],
			'language' => $channel->info['LANGUAGE'],
			'charset' => 'utf-8',
			'published' => $channel->info['CREATEDATE'],
//			'generator' => 'Zend Framework Zend_Feed',
			'entries' => array()
		);
;
		foreach ($channel->news as $item) {
			$feedArray['entries'][] = array(
				'title' => $item['TITLE'],
				'link' => $item['URL'] ? $baseUrl . $item['URL'] : '',
//				'guid' => $baseUrl . '/book/bookdetail/book_id/' . $book->book_id,
				'description' => $item['DESCRIPTION'],
				'lastUpdate' => $item['NEWSDATE']
			);
		}

		$feed = Zend_Feed::importArray($feedArray, 'rss');

		// Not needed - see comments
		foreach ($feed as $entry) {
			$element = $entry->summary->getDOM();
		}

		$feed->send();
		exit;
	}

}