<?php
################################################################################
# Aastra XML API Classes - AastraIPPhoneTextMenuEntry
# Copyright Mitel Networks 2005-2015
#
# Internal class for AastraIPPhoneTextMenu object.
################################################################################

class AastraIPPhoneTextMenuEntry extends AastraIPPhone {
	var $_name;
	var $_url;
	var $_selection;
	var $_icon;
	var $_dial;
	var $_line;
	var $_base;
	var $_split;
	var $_color;
	var $_iconRight1;
	var $_iconRight2;
	var $_iconRight3;
	var $_iconRight4;

	function AastraIPPhoneTextMenuEntry($name, $url, $selection, $icon, $dial, $line, $color, $split, $base, $iconRight1=NULL, $iconRight2=NULL, $iconRight3=NULL, $iconRight4=NULL)
	{
		$this->_name=$name;
		$this->_url=$url;
		$this->_selection=$selection;
		$this->_icon=$icon;
		$this->_dial=$dial;
		$this->_line=$line;
		$this->_split=$split;
		$this->_color=$color;
		$this->_base=$base;
		$this->_iconRight1=$iconRight1;
		$this->_iconRight2=$iconRight2;
		$this->_iconRight3=$iconRight3;
		$this->_iconRight4=$iconRight4;
	}

	function getName()
	{
		return($this->_name);
	}

	function getBase()
	{
		return($this->_base);
	}

	function format_line($array_name,$style,$length,$is_softkeys)
	{
		# Retrieve parameters
		$line1=$array_name[0];
		$line2=$array_name[1];
		$offset=$array_name[2];
		$char=$array_name[3];
		if($char==' ') $char=chr(0xa0);
		$mode=$array_name[4];
		if($mode=='') $mode='left';

		# Adjust with the style if softkey phone
		if($is_softkeys)
			{
			switch($style)
				{
				case 'none':
				case 'radio':
					$length--;
					break;
				default:
					$length-=4;
					break;
				}
			}

		# Unbreakable space
		$nbsp=chr(0xa0);

		# Pad the the first line with regular spaces
		switch($mode)
			{
			case 'center':
				$line=str_pad($line1,$length-1-$offset,$char,STR_PAD_BOTH);
				break;
			case 'right':
				$line=str_pad($line1,$length-1-$offset,$char,STR_PAD_LEFT);
				break;
			default:
				$line=str_pad($line1,$length-1-$offset,$char,STR_PAD_RIGHT);
				break;
			}

		# Crop the line to the correct length (-1 for wrap-space)
		$line=substr($line,0,($length-1-$offset));

		# Append a space so it can wrap to line two, and two non-break spaces to pad below the icon
		if($line2!='') 
			{
			$line.=' '.str_repeat($nbsp,$offset);
			switch($mode)
				{
				case 'center':
					if($char==chr(0xa0)) $line.=str_repeat($char,($length-$offset-strlen($line2))/2).$line2;
					else $line.=str_pad($line2,$length-$offset-1,$char,STR_PAD_BOTH);
					break;
				case 'right':
					$line.=str_repeat($char,$length-$offset-strlen($line2)+1).$line2;
					break;
				default:
					$line.=$line2;
					break;
				}
			}

		# Return formatted prompt
		return($line);
	}


	function render($style,$length,$is_softkeys,$base=NULL)
	{
		# Opening
		$base = $this->escape($base);
		$xml = '<MenuItem';
		if($base!=NULL)
			{ 
			if($base!='AASTRA_RESETBASE') $xml .= " base=\"{$base}\"";
			else $xml .= " base=\"\"";
			}
		if($this->_icon!=NULL) $xml .= " icon=\"{$this->_icon}\"";
		if($this->_iconRight1!=NULL) $xml .= " iconr1=\"{$this->_iconRight1}\"";
		if($this->_iconRight2!=NULL) $xml .= " iconr2=\"{$this->_iconRight2}\"";
		if($this->_iconRight3!=NULL) $xml .= " iconr3=\"{$this->_iconRight3}\"";
		if($this->_iconRight4!=NULL) $xml .= " iconr4=\"{$this->_iconRight4}\"";
		$xml .= ">\n";

		# Prompt
		if(is_array($this->_name)) $name = $this->format_line($this->_name,$style,$length,$is_softkeys);
		else $name = $this->_name;
		$name = $this->escape($name);
		$xml .= "<Prompt";
		if($this->_split!=NULL) $xml .= " split=\"{$this->_split}\"";
		if($this->_color!='') $xml .= " Color=\"{$this->_color}\"";
		$xml .= ">{$name}</Prompt>\n";

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

		# Close
		$xml .= '</MenuItem>'."\n";
	
		# Return generated vaue
		return($xml);
	}
}
?>
