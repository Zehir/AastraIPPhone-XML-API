<?php
################################################################################
# Aastra XML API Classes - AastraIPPhoneExecuteEntry
# Firmware 2.0 or better
# Copyright Aastra Telecom 2005-2010
#
# Internal class for AastraIPPhoneExecute object.
################################################################################

class AastraIPPhoneExecuteEntry extends AastraIPPhone {
	var $_url;
	var $_interruptCall;

	function AastraIPPhoneExecuteEntry($url,$interruptCall)
	{
		$this->_url = $url;
		$this->_interruptCall = $interruptCall;
	}

	function render()
	{
		$url = $this->escape($this->_url);
		$xml = "<ExecuteItem URI=\"".$url."\"";
		if ($this->_interruptCall=='no') $xml .= " interruptCall=\"no\"";
		$xml .= "/>\n";
		return($xml);
	}
}
?>
