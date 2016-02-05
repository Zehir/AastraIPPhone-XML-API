<?php
################################################################################
# Aastra XML API Classes - AastraIPPhoneConfigurationEntry
# Firmware 2.0 or better
# Copyright Aastra Telecom 2007-2010
#
# Internal class for AastraIPPhoneConfiguration object.
################################################################################

class AastraIPPhoneConfigurationEntry extends AastraIPPhone {
	var $_parameter;
	var $_value;
	var $_type;

	function AastraIPPhoneConfigurationEntry($parameter, $value, $type)
	{
		$this->setParameter($parameter);
		$this->setValue($value);
		$this->setType($type);
	}

	function setParameter($parameter)
	{
		$this->_parameter = $parameter;
	}

	function setValue($value)
	{
		$this->_value = $value;
	}

	function setType($type)
	{
		$this->_type = $type;
	}


	function render()
	{
		$parameter = $this->escape($this->_parameter);
		$value = $this->escape($this->_value);
		$type = $this->escape($this->_type);
		$xml = "<ConfigurationItem";
		if($type!='') $xml.=" setType=\"".$type."\"";
		$xml .=">\n";
		$xml .= "<Parameter>".$parameter."</Parameter>\n";
		$xml .= "<Value>".$value."</Value>\n";
		$xml .= "</ConfigurationItem>\n";
		return($xml);
	}
}
?>
