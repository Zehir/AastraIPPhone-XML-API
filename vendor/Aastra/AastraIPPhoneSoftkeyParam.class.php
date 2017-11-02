<?php
################################################################################
# Aastra XML API Classes - AastraIPPhoneSoftkeyParam
# Firmware 2.0 or better
# Copyright Mitel Networks 2005-2015
#
# Internal class for AastraIPPhoneConfiguration object.
################################################################################

class AastraIPPhoneSoftkeyParam extends AastraIPPhone {
	var $_parameter;
	var $_value;

	function AastraIPPhoneSoftkeyParam($parameter, $value)
	{
		$this->setParameter($parameter);
		$this->setValue($value);
	}

	function setParameter($parameter)
	{
		$this->_parameter = $parameter;
	}

	function setValue($value)
	{
		$this->_value = $value;
	}


	function render()
	{
		$parameter = $this->escape($this->_parameter);
		$value = $this->escape($this->_value);
		$xml = "<SoftkeyItem>\n";
		$xml .= "<Parameter>".$parameter."</Parameter>\n";
		$xml .= "<Value>".$value."</Value>\n";
		$xml .= "</SoftkeyItem>\n";
		return($xml);
	}
}
?>
