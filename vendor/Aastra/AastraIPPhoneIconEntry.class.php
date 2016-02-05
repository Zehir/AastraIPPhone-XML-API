<?php
################################################################################
# Aastra XML API Classes - AastraIPIconEntry
# Firmware 2.0 or better
# Copyright Aastra Telecom 2007-2010
#
# Internal class for AastraIPPhone object.
################################################################################

class AastraIPPhoneIconEntry {
	var $_index;
	var $_icon;

	function AastraIPPhoneIconEntry($index, $icon)
	{
		$this->_index=$index;
		$this->_icon=$icon;
	}

	function render()
	{
		$index = $this->_index;
		$icon = $this->_icon;
		$xml = "<Icon index=\"{$index}\">{$icon}</Icon>\n";
		return($xml);
	}
}
?>
