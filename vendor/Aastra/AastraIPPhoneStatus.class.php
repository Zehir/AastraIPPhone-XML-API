<?php
########################################################################################################
# Aastra XML API Classes - AastraIPPhoneStatus
# Copyright Mitel Networks 2005-2015
#
# AastraIPPhoneStatus object.
#
# Public methods
#
# Inherited from AastraIPPhone
#     setBeep() to enable a notification beep with the object (optional)
#     setEncodingUTF8() to change encoding from default ISO-8859-1 to UTF-8 (optional)
#     generate() to return the generated XML for the object
#     output(flush) to display the object
#          @flush		boolean optional, output buffer to be flushed out or not.
#
# Specific to the object
#     setSession(session) to setup the session ID
#          @session		string
#     setTriggerDestroyOnExit() to set the triggerDestroyOnExit tag to "yes" (optional)
#     addEntry(index,message,type,timeout,uri,icon,color) to add a message to be displayed on the idle screen.
#          @index		integer
#          @message		string
#	         @type		enum ("alert","icon") optional
#          @timeout		integer (seconds) optional
#          @uri		    uri to call when message/alert pressed optional, only for 6739i
#          @icon		icon index 6739i only, optional
#          @color		label color, 6739i only, optional
#
# Example
#     require_once('AastraIPPhoneStatus.class.php');
#     $status = new AastraIPPhoneStatus();
#     $status->setSession('Session');
#     $status->setBeep();
#     $status->addEntry('1','Message 1','',0);
#     $status->addEntry('2','Message 2','alert',5);
#     $status->output();
#
########################################################################################################

require_once('AastraIPPhone.class.php');
require_once('AastraIPPhoneStatusEntry.class.php');

class AastraIPPhoneStatus extends AastraIPPhone {
	var $_session;
	var $_triggerDestroyOnExit="";

	function setSession($session) 
	{
		$this->_session=$session;
	}

	function setTriggerDestroyOnExit() 
	{
		$this->_triggerDestroyOnExit="yes";
	}

	function addEntry($index, $message, $type='', $timeout=NULL, $uri='', $icon=0, $color='')
	{
		$this->_entries[] = new AastraIPPhoneStatusEntry($index, $message, $type, $timeout, $uri, $icon, $color);
	}

	function addMessage($index, $message, $timeout=NULL, $uri='', $icon=0, $color='')
	{
		$this->_entries[] = new AastraIPPhoneStatusEntry($index, $message, '', $timeout, $uri, $icon, $color);
	}

	function addToaster($message, $uri='', $icon=0)
	{
		$this->_entries[] = new AastraIPPhoneStatusEntry('0', $message, 'toaster', NULL, $uri, $icon, '');
	}

	function render()
	{
		# Beginning of root tag
		$out = "<AastraIPPhoneStatus";

		# Beep
		if($this->_beep=='yes') $out .= " Beep=\"yes\"";

		# TriggerDestroyOnExit
		if($this->_triggerDestroyOnExit=='yes') $out .= " triggerDestroyOnExit=\"yes\"";

		# End of root tag
		$out .= ">\n";

		# Session
		$session = $this->escape($this->_session);
		$out .= "<Session>".$session."</Session>\n";

		# Status Items		
		if (isset($this->_entries) && is_array($this->_entries)) 
			{
			foreach ($this->_entries as $entry) $out .= $entry->render();
			}

		# Icons
		if (isset($this->_icons) && is_array($this->_icons)) 
			{
  			$IconList=False;
  			foreach ($this->_icons as $icon) 
  				{
	  			if(!$IconList) 
  					{
	  				$out .= "<IconList>\n";
	  				$IconList=True;
	  				}
	  			$out .= $icon->render();
  				}
  			if($IconList) $out .= "</IconList>\n";
			}

		# End tag
		$out .= "</AastraIPPhoneStatus>\n";

		# Return XML object
		return($out);
	}
}
?>
