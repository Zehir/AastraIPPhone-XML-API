<?php
########################################################################################################
# Aastra XML API Classes - AastraIPPhoneConfiguration
# Copyright Aastra Telecom 2007-2010
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
#     setType(type) to set the type of configuration object (optional)
#          @type		string, configuration change type
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
#     $configuration->addEntry('softkey1 label','Test');
#     $configuration->addEntry('softkey1 type','xml');
#     $configuration->setTriggerDestroyOnExit();
#     $configuration->setBeep();
#     $configuration->output();
#
########################################################################################################

require_once('AastraIPPhone.class.php');
require_once('AastraIPPhoneConfigurationEntry.class.php');

class AastraIPPhoneConfiguration extends AastraIPPhone {
	var $_type='';
	var $_triggerDestroyOnExit='';
	
	function addEntry($parameter, $value, $type='')
	{
		$this->_entries[] = new AastraIPPhoneConfigurationEntry($parameter, $value, $type);
	}

	function setTriggerDestroyOnExit() 
	{
		$this->_triggerDestroyOnExit="yes";
	}

	function setType($type) 
	{
		$this->_type=$type;
	}

	function render()
	{
		# Beginning of root tag
		$out = "<AastraIPPhoneConfiguration";

		# Beep
		if($this->_beep=='yes') $out .= " Beep=\"yes\"";

		# TriggerDestroyOnExit
		if($this->_triggerDestroyOnExit=='yes') $out .= " triggerDestroyOnExit=\"yes\"";

		# Type
		if($this->_type!='') $out .= " setType=\"{$this->_type}\"";

		# End of root tag
		$out .= ">\n";

		# Configuration Items
		if (isset($this->_entries) && is_array($this->_entries)) 
			{
			foreach ($this->_entries as $entry) $out .= $entry->render();
			}

		# End tag
		$out .= "</AastraIPPhoneConfiguration>\n";

		# Return XML object
		return($out);
	}
}
?>