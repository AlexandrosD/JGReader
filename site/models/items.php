<?php
/*------------------------------------------------------------------------
# com_jgreader - J! GoogleReader
# ------------------------------------------------------------------------
# @author    Alexandros D
# @copyright Copyright (C) 2011 Alexandros D. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @Website: http://alexd.mplofa.com
-------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelitem library
jimport('joomla.application.component.modellist');

require_once 'components/com_jgreader/gReader-Library/greader.class.php';

class JGReaderModelItems extends JModelList {

	public function getItems() {
		$items = array();
		
		//create a new google reader instance
		$reader = new GReader( $this->_getOAuthSignatures() );
		
		/* Starred items */
		if ( $this->_getDisplayOption() == "starred" )
			$items = $reader->listStarred( $this->_getLimit() );
		
		/* Shared items */
		if ( $this->_getDisplayOption() == "shared" )
			$items = $reader->listShared( $this->_getLimit() );
		
		/* Tagged items */
		if ( $this->_getDisplayOption() == "tagged" )
			$items = $reader->listTagged( $this->_getTag() , $this->_getLimit() );
		
		return $items;
	}
	
	private function _getLimit() {
		return JRequest::getInt("limit" , 10);
	}
	
	private function _getDisplayOption() {
		return JRequest::getVar("display" , "starred");
	}
	
	private function _getTag() {
		return JRequest::getVar("tag" , "N/A");
	}
	
	private function _getOAuthSignatures() {
		$params = json_decode(JFactory::getApplication()->getParams());
		if ( isset ($params->consumer_key) && isset ($params->shared_secret) && isset ($params->oauth_secret) && isset ($params->oauth_token)) {
			$signatures = array (
				"consumer_key" => $params->consumer_key,
				"shared_secret" => $params->shared_secret,
				"oauth_secret" => $params->oauth_secret,
				"oauth_token" => $params->oauth_token				
			);
			return $signatures;
		}
		else {
			return;
		}		
	}

}
