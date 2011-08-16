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
 
// import Joomla view library
jimport('joomla.application.component.view');

class JGReaderViewItems extends JView
{
        // Overwriting JView display method
        function display($tpl = null) 
        {
        	$this->items = $this->get("Items");
        	
        	if ($this->items) {
	        	$doc = JFactory::getDocument();
	        	$doc->addStyleSheet("components/com_jgreader/media/stylesheet.css");
	        	$doc->addScript("components/com_jgreader/media/script.js");
	        	
	        	$params->linkedTitle = (bool) JRequest::getInt( "linkableTitle" , 1 );
	        	$params->displayDate = (bool) JRequest::getInt( "displayDate" , 1 );
	        	$params->displaySummary = (bool) JRequest::getInt( "displaySummary" , 1 );
	        	$this->assignRef ( "params" , $params );
	        	
				parent::display($tpl);
        	}
        	else {
        		echo "<h2>No results!</h2>";
        		echo "<h3>Please ensure that your credentials have been provided correctly and that you have shared and/or starred items in your reading list</h3>";
        	}
        }
}
