<?php
########################################################################################################
# Aastra XML API Classes - AastraIPPhoneConfiguration
# Copyright Mitel Networks 2005-2016
#
# AastraIPPhoneConfiguration object.
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
#     addEntry(parameter,value,type) to add a configuration change
#          @parameter	string, parameter name
#          @value		string, parameter value
#          @type		string, conmfiguration change type (optional)
#     setTriggerDestroyOnExit() to set the triggerDestroyOnExit tag to 
#     "yes" (optional)
#
# Example
#     require_once('AastraIPPhoneConfiguration.class.php');
#     $configuration = new AastraIPPhoneConfiguration();
#     $configuration->addEntry('topsoftkey1 label','Test');
#     $configuration->addEntry('topsoftkey1 background color','blue');
#     $configuration->output();
#
########################################################################################################

require_once('AastraIPPhone.class.php');
require_once('AastraIPPhoneSoftkeyParam.class.php');

class AastraIPPhoneSoftkey extends AastraIPPhone {
	var $_triggerDestroyOnExit='';
	
	function addEntry($parameter, $value)
	{
		$this->_entries[] = new AastraIPPhoneSoftkeyParam($parameter, $value);
	}

	function setTriggerDestroyOnExit() 
	{
		$this->_triggerDestroyOnExit="yes";
	}

	function render()
	{
		# Beginning of root tag
		$out = "<AastraIPPhoneSoftkey";

		# Beep
		if($this->_beep=='yes') $out .= " Beep=\"yes\"";

		# TriggerDestroyOnExit
		if($this->_triggerDestroyOnExit=='yes') $out .= " triggerDestroyOnExit=\"yes\"";

		# End of root tag
		$out .= ">\n";

		# Configuration Items
		if (isset($this->_entries) && is_array($this->_entries)) 
			{
			foreach ($this->_entries as $entry) $out .= $entry->render();
			}

		# End tag
		$out .= "</AastraIPPhoneSoftkey>\n";

		# Return XML object
		return($out);
	}
}
?>