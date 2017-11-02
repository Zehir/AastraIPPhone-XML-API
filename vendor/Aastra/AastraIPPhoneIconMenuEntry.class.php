<?php
################################################################################
# Aastra XML API Classes - AastraIPPhoneTextMenuEntry
# Copyright Mitel Networks 2005-2015
#
# Internal class for AastraIPPhoneTextMenu object.
################################################################################

class AastraIPPhoneIconMenuEntry extends AastraIPPhone {
	var $_url;
	var $_selection;
	var $_icon;
	var $_iconScaled;
	var $_fontMono;
	var $_dial;
	var $_line;
	var $_lines=array();

	function AastraIPPhoneIconMenuEntry($url, $selection, $icon, $iconScaled, $fontMono, $dial, $line)
	{
		$this->_url=$url;
		$this->_selection=$selection;
		$this->_icon=$icon;
		$this->_iconScaled=$iconScaled;
		$this->_dial=$dial;
		$this->_line=$line;
		$this->_fontMono=$fontMono;
	}

	function addLine($text, $align, $color)
	{
		$this->_lines[]=array('text'=>$text,'align'=>$align,'color'=>$color);
	}

	function render()
	{
		# Opening
		$xml = '<MenuItem';
		if($this->_fontMono!='') $xml .= " fontMono=\"{$this->_fontMono}\"";
		$xml .= ">\n";

		# Prompt
		$icon = $this->escape($this->_icon);
		$xml .= "<iconName";
	  if($this->_iconScaled!='') $xml .= " scaled=\"{$this->_iconScaled}\"";
		$xml .= ">{$icon}</iconName>\n";

		# URI
		$url = $this->escape($this->_url);
		$xml .= "<URI>{$url}</URI>\n";

		# Selection
		$selection = $this->escape($this->_selection);
		if($selection!=NULL) $xml .= "<Selection>{$selection}</Selection>\n";

		# Dial
		if($this->_dial!=NULL) 
			{
			if($this->_line!=NULL) $xml .= "<Dial line=\"{$this->_line}\">{$this->_dial}</Dial>\n";
			else $xml .= "<Dial>{$this->_dial}</Dial>\n";
			}

	  # Lines
		foreach($this->_lines as $value) {
			$xml .= '<Line';
			if($value['align']!='') {
				$align = $this->escape($value['align']);
				$xml .= " Align=\"{$align}\"";
			}
			if($value['color']!='') {
				$color = $this->escape($value['color']);
				$xml .= " Color=\"{$color}\"";
			}
			$xml .= ">";
			$text = $this->escape($value['text']);
			if($text!='') $xml .= $text;
			$xml .= '</Line>'."\n";
		}

		# Close
		$xml .= '</MenuItem>'."\n";
	
		# Return generated vaue
		return($xml);
	}
}
?>
