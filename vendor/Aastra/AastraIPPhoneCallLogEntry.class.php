<?php
################################################################################
# Aastra XML API Classes - AastraIPPhoneCallLogEntry
# Copyright Mitel Networks 2005-2015
#
# Internal class for AastraIPPhoneCallLog object.
################################################################################

class AastraIPPhoneCallLogEntry extends AastraIPPhone {
	var $_name;
	var $_number;
	var $_date;
	var $_time;
	var $_selection;
	var $_duration;
	var $_type;
	var $_terminal;
	var $_count;
	var $_line;

	function AastraIPPhoneCallLogEntry($name, $number, $date, $time, $selection, $duration, $type, $terminal, $count, $line)
	{
		$this->_name=$name;
		$this->_number=$number;
		$this->_date=$date;
		$this->_time=$time;
		$this->_selection=$selection;
		$this->_duration=$duration;
		$this->_type=$type;
		$this->_terminal=$terminal;
		$this->_count=$count;
		$this->_line=$line;
	}


	function render()
	{
		# Opening
		$xml = '<LogItem';
		if($this->_duration!='') {
			$temp = $this->escape($this->_duration);
			$xml .= " duration=\"".$temp."\"";
		}
		if($this->_type!='') {
			$temp = $this->escape($this->_type);
			$xml .= " type=\"".$temp."\"";
		}
		if($this->_terminal!='') {
			$temp = $this->escape($this->_terminal);
			$xml .= " callingTerminal=\"".$temp."\"";
		}
		if($this->_count!='') {
			$temp = $this->escape($this->_count);
			$xml .= " count=\"".$temp."\"";
		}
		if($this->_line!='') {
			$temp = $this->escape($this->_line);
			$xml .= " line=\"".$temp."\"";
		}
		$xml .= ">\n";

		# Fiedls
		$temp = $this->escape($this->_name);
		$xml .= "<Name>{$temp}</Name>\n";
		$temp = $this->escape($this->_number);
		$xml .= "<Number>{$temp}</Number>\n";
		$temp = $this->escape($this->_date);
		$xml .= "<Date>{$temp}</Date>\n";
		$temp = $this->escape($this->_time);
		$xml .= "<Time>{$temp}</Time>\n";
		if($this->_selection!='') {
			$temp = $this->escape($this->_selection);
			$xml .= "<Selection>{$temp}</Selection>\n";
		}

		# Close
		$xml .= "</LogItem>\n";
	
		# Return generated vaue
		return($xml);
	}
}
?>
