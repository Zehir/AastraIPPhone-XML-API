<?php
################################################################################
# Aastra XML API Classes - AastraIPPhoneImageMenuEntry
# Firmware 2.0 or better
# Copyright Aastra Telecom 2007-2010
#
# Internal class for AastraIPPhoneImageMenu object.
################################################################################

class AastraIPPhoneImageMenuEntry extends AastraIPPhone {
	var $_key;
	var $_uri;

	function AastraIPPhoneImageMenuEntry($key, $uri)
	{
		$this->_key=$key;
		$this->_uri=$uri;
	}

	function render()
	{
		$key = $this->_key;
		$uri = $this->escape($this->_uri);
		$xml = "<URI key=\"{$key}\">{$uri}</URI>\n";
		return($xml);
	}
}
?>
