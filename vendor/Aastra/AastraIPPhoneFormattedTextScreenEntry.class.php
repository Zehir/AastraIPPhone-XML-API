<?php
################################################################################
# Aastra XML API Classes - AastraIPPhoneFormattedTextScreenEntry
# Firmware 2.0 or better
# Copyright Mitel Networks 2005-2015
#
# Internal class for AastraIPPhoneFormattedTextScreen object.
################################################################################

class AastraIPPhoneFormattedTextScreenEntry extends AastraIPPhone {
	var $_text;
	var $_size;
	var $_align;
	var $_color;
	var $_wrap;
	var $_blink;
	var $_type;

	function AastraIPPhoneFormattedTextScreenEntry($text, $size, $align, $color, $wrap, $blink, $type)
	{
		if($size=='double')$this->_text=$this->convert_high_ascii($text);
		else $this->_text=$text;
		$this->_size=$size;
		$this->_align=$align;
 		$this->_color=$color;
		$this->_wrap=$wrap;
		$this->_blink=$blink;
		$this->_type=$type;
	}

	function render()
	{
		switch($this->_type)
			{
			case "normal":
				$xml = "<Line";
				if($this->_size!=NULL) $xml .= " Size=\"{$this->_size}\"";
				if($this->_align!=NULL) $xml .= " Align=\"{$this->_align}\"";
				if($this->_color!=NULL) $xml .= " Color=\"{$this->_color}\"";
				if($this->_wrap!=NULL) $xml .= " wrap=\"{$this->_wrap}\"";
				if($this->_blink!=NULL) $xml .= " blink=\"{$this->_blink}\"";
				$xml .= ">";
				$xml .= $this->escape($this->_text)."</Line>\n";
				break;
			case "scrollstart":
				if($this->_size!='') $xml = "<Scroll Height=\"{$this->_size}\">\n";
				else $xml = "<Scroll>\n";
				break;
			case "scrollend":
				$xml = "</Scroll>\n";
				break;
			}
		return($xml);
	}
}
?>
