<?php
################################################################################
# Aastra XML API Classes - AastraIPPhoneInputScreenEntry
# Firmware 2.0 or better
# Copyright Aastra Telecom 2005-2010
#
# Internal class for AastraIPPhoneInputScreen object.
################################################################################

class AastraIPPhoneInputScreenEntry {
	var $_type='';
	var $_password='';
	var $_editable='';
	var $_parameter='';
	var $_prompt='';
	var $_default='';
	var $_selection='';
	var $_softkeys;
	var $_inputlanguage='';
	var $_fieldcolor='';
	var $_promptcolor='';
	var $_choiceURL='';

	function AastraIPPhoneInputScreenEntry($type)
	{
		$this->_type = $type;
		$this->_softkeys = array();
	}
	
	function render()
	{
		$xml = "<InputField";
		if($this->_type != '') $xml .= " type=\"".$this->_type."\"";
		if($this->_password != '') $xml .= " password=\"".$this->_password."\"";
		if($this->_editable != '') $xml .= " editable=\"".$this->_editable."\"";
		$xml .= ">\n";
		if($this->_prompt != '') 
			{
			$xml .= "<Prompt";
			if($this->_promptcolor != '') $xml .= " Color=\"{$this->_promptcolor}\"";
			$xml .= ">".$this->_prompt."</Prompt>\n";
			}
		if($this->_parameter != '') 
			{
			$xml .= "<Parameter";
			if($this->_fieldcolor != '') $xml .= " Color=\"{$this->_fieldcolor}\"";
			if($this->_fieldbcolor != '') $xml .= " Bcolor=\"{$this->_fieldbcolor}\"";
			$xml .= ">".$this->_parameter."</Parameter>\n";
			}
		if($this->_selection != '') $xml .= "<Selection>".$this->_selection."</Selection>\n";
		if($this->_default != '') $xml .= "<Default>".$this->_default."</Default>\n";
		if($this->_choiceURL != '') $xml .= "<choiceURL>".$this->_choiceURL."</choiceURL>\n";
		foreach ($this->_softkeys as $softkey) $xml .= $softkey->render();
		$xml .= "</InputField>\n";
		return($xml);
	}
}
?>
