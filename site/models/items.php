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
		$reader = new GReader( $this->_getUsername() , $this->_getPassword() );
		
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
	
	private function _getUsername() {
		$params = json_decode(JFactory::getApplication()->getParams());
		if (! isset ($params->username))
			return;
		/* else */
		return $params->username;
	}
	
	private function _getPassword() {
		$params = json_decode(JFactory::getApplication()->getParams());
		if (! isset ($params->password))
			return;
		/* else */
		return $params->password;
	}

}
