<?php
################################################################################
# Aastra XML API Classes - AastraIPPhoneExecuteEntry
# Firmware 2.0 or better
# Copyright Mitel Networks 2005-2015
#
# Internal class for AastraIPPhoneExecute object.
################################################################################

class AastraIPPhoneExecuteEntry extends AastraIPPhone {
	var $_url;
	var $_interruptCall;
	var $_title;

	function AastraIPPhoneExecuteEntry($url,$interruptCall,$title)
	{
		$this->_url = $url;
		$this->_interruptCall = $interruptCall;
		$this->_title = $title;
	}

	function render()
	{
		$url = $this->escape($this->_url);
		$title = $this->escape($this->_title);
		$xml = "<ExecuteItem URI=\"".$url."\"";
		if ($this->_interruptCall=='no') $xml .= " interruptCall=\"no\"";
		if ($this->_title!='') $xml .= " title=\"".$title."\"";
		$xml .= "/>\n";
		return($xml);
	}
}
?>
