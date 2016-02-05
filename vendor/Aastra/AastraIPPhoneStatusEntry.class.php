<?php
################################################################################
# Aastra XML API Classes - AastraIPPhoneStatusEntry
# Copyright Aastra Telecom 2007-2010
#
# Internal class for AastraIPPhoneStatus object.
################################################################################

class AastraIPPhoneStatusEntry extends AastraIPPhone {
	var $_index;
	var $_message;
    var $_type='';
    var $_timeout=0;
	var $_uri='';
	var $_color='';
	var $_icon=0;

	function AastraIPPhoneStatusEntry($index, $message, $type='', $timeout=NULL, $uri='', $icon=0, $color)
	{
		$this->_index = $index;
		$this->_message = $this->convert_high_ascii($message);
		$this->_type = $type;
		$this->_timeout = $timeout;
		$this->_uri = $uri;
		$this->_icon = $icon;
		$this->_color = $color;
	}

	function render()
	{
		$index = $this->escape($this->_index);
		$message = $this->escape($this->_message);
		$type = $this->escape($this->_type);
		$timeout = $this->_timeout;
		$uri = $this->escape($this->_uri);
		$xml = "<Message index=\"{$index}\"";
		if ($this->_color!='') $xml .= " Color=\"{$this->_color}\"";
		if ($type!='') 
			{
			$xml .= " type=\"{$type}\"";
			if ($timeout!=NULL) $xml .= " Timeout=\"{$timeout}\"";
			}
		if ($uri!='') $xml .= " URI=\"{$uri}\"";
		if($this->_icon!=0) $xml .= " icon=\"{$this->_icon}\"";
		$xml .= ">{$message}</Message>\n";
		return($xml);
	}
}
?>
