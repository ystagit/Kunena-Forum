<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Layout.Search
 *
 * @copyright   (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * KunenaLayoutSearchResults
 *
 * @since  3.1
 *
 */
class KunenaLayoutSearchResults extends KunenaLayout
{
	public function displayRows() {
		// Run events
		$params = new JRegistry();
		$params->set('ksource', 'kunena');
		$params->set('kunena_view', 'search');
		$params->set('kunena_layout', 'default');

		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('kunena');

		$dispatcher->trigger('onKunenaPrepare', array ('kunena.messages', &$this->results, &$params, 0));

		foreach ($this->results as $this->message) {
			$this->topic = $this->message->getTopic();
			$this->category = $this->message->getCategory();
			$this->categoryLink = $this->getCategoryLink($this->category->getParent()) . ' / ' . $this->getCategoryLink($this->category);
			$ressubject = KunenaHtmlParser::parseText ($this->message->subject);
			$resmessage = KunenaHtmlParser::parseBBCode($this->message->message, 500);

			$profile = KunenaFactory::getUser((int)$this->message->userid);
			$this->useravatar = $profile->getAvatarImage('kavatar', 'post');

			foreach ( $this->searchwords as $searchword ) {
				if (empty ( $searchword )) continue;
				$ressubject = preg_replace ( "/" . preg_quote ( $searchword, '/' ) . "/iu", '<span  class="searchword" >' . $searchword . '</span>', $ressubject );
				// FIXME: enable highlighting, but only after we can be sure that we do not break html
				//$resmessage = preg_replace ( "/" . preg_quote ( $searchword, '/' ) . "/iu", '<span  class="searchword" >' . $searchword . '</span>', $resmessage );
			}
			$this->author = $this->message->getAuthor();
			$this->topicAuthor = $this->topic->getAuthor();
			$this->topicTime = $this->topic->first_post_time;
			$this->subjectHtml = $ressubject;
			$this->messageHtml = $resmessage;

			$contents = $this->subLayout('Search/Results/Row')->setProperties($this->getProperties());
			echo $contents;
		}
	}
}
