<?php
################################################################################
# Aastra XML API Classes - AastraIPPhoneTextScreenEntry
# Copyright Aastra Telecom 2007-2011
#
# Internal class for AastraIPPhoneTextScreen object.
################################################################################

class AastraIPPhoneTextScreenEntry extends AastraIPPhone {
	var $_text;
	var $_color='';

	function AastraIPPhoneTextScreenEntry($text,$color)
	{
		$this->_text = $text;
		$this->_color = $color;
	}

	function render()
	{
		$text = $this->escape($this->_text);
		$xml = "<Text";
		if ($this->_color!='') $xml .= " Color=\"{$this->_color}\"";
		$xml .= ">{$text}</Text>\n";
		return($xml);
	}
}
?>
