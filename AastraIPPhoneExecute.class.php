<?php
########################################################################################################
# Aastra XML API Classes - AastraIPPhoneExecute
# Copyright Aastra Telecom 2006-2010
#
# AastraIPPhoneExecute object.
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
#     setTriggerDestroyOnExit() to set the triggerDestroyOnExit tag to "yes" (optional)
#     addEntry(url,interruptCall) to add an action to be executed.
#          @url		string
#          @interruptCall	string, optional, "yes" or "no"	
#
# Example
#     require_once('AastraIPPhoneExecute.class.php');
#     $execute = new AastraIPPhoneExecute();
#     $execute->addEntry('http://myserver.com/script.php?choice=2');
#     $execute->addEntry('Command: Reset');
#     $execute->output();
#
########################################################################################################

require_once('AastraIPPhone.class.php');
require_once('AastraIPPhoneExecuteEntry.class.php');

class AastraIPPhoneExecute extends AastraIPPhone {
	var $_defaultIndex='';
	var $_triggerDestroyOnExit='';

	function addEntry($url,$interruptCall=NULL)
	{
		$this->_entries[] = new AastraIPPhoneExecuteEntry($url,$interruptCall);
	}

	function setTriggerDestroyOnExit() 
	{
		$this->_triggerDestroyOnExit='yes';
	}

	function render()
	{
		# Beginning of root tag
		$out = "<AastraIPPhoneExecute";

		# Beep
		if($this->_beep=='yes') $out .= " Beep=\"yes\"";

		# TriggerDestroyOnExit
		if($this->_triggerDestroyOnExit=='yes') $out .= " triggerDestroyOnExit=\"yes\"";

		# End of root tag
		$out .= ">\n";

		# Execute Items
		if (isset($this->_entries) && is_array($this->_entries)) 
			{
			foreach ($this->_entries as $entry) $out .= $entry->render();
			}

		# End tag
		$out .= "</AastraIPPhoneExecute>\n";

		# Return XML object
		return($out);
	}
}
?>