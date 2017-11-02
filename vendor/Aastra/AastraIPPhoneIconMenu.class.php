<?php
########################################################################################################
# Aastra XML API Classes - AastraIPPhoneTextMenu
# Copyright Mitel Networks 2005-2015
#
# AastraIPPhoneTextMenu object.
#
# Public methods
#
# Inherited from AastraIPPhone
#     setTitle(Title) to setup the title of an object (optional)
#          @title		string
#     setTitleWrap() to set the title to be wrapped on 2 lines (optional)
#     setTopTitle(title,color,icon_index) to set the Top Title of the XML screen (6739i only)
#          @title		string
#          @color		string, "red", "blue", ... (optional)
#          @icon_index	integer, icon number
#     setDestroyOnExit() to set DestroyonExit parameter to 'yes', 'no' by default (optional)
#     setBeep() to enable a notification beep with the object (optional)
#     setLockIn(uri) to set the Lock-in tag to 'yes' and the GoodbyeLockInURI(optional)
#          @uri		string, GoodByeLockInURI
#     setLockInCall() to set the Lock-in tag to 'call' (optional)
#     setTimeout(timeout) to define a specific timeout for the XML object (optional)
#          @timeout		integer (seconds)
#     setCallProtection(notif) to protect the XML object against incoming calls
#          @notif to enable/disable (false by default) the display of an incoming call notification (optional)
#     setBackgroundColor(color) to change the XML object background color (optional)
#          @color		string, "red", "blue", ...
#     addSoftkey(index,label,uri,icon_index) to add custom soktkeys to the object (optional)
#          @index		integer, softkey number
#          @label		string
#          @uri		string
#          @icon_index	integer, icon number
#     setRefresh(timeout,URL) to add Refresh parameters to the object (optional)
#          @timeout		integer (seconds)
#          @URL		string
#     setEncodingUTF8() to change encoding from default ISO-8859-1 to UTF-8 (optional)
#     addIcon(index,icon) to add custom icons to the object (optional)
#          @index		integer, icon index
#          @icon		string, icon name or definition
#     generate() to return the generated XML for the object
#     output(flush) to display the object
#          @flush		boolean optional, output buffer to be flushed out or not.
#
# Specific to the object
#			setLayout(layout) to set the desired layout
#					 @layout		 1 or 2
#			setMode(mode) to set the screen mode
#          @mode			 'regular' or 'extended'
#     setDefaultIndex(index) to set the default selection in the list (optional)
#          @index		   index (1-24)
#     setFontMono(fontMono) to decide the use of the monotype font
#					 @fontMono   boolean ("yes"/"no")
#     addEntry(url,selection,icon,iconScaled,fontMono,dial,line) to add an element in the list to be displayed
#          @url		     string
#          @selection	 string (optional)
#          @icon		   string (optional)
#          @iconScaled boolean (optional)
#          @fontMono   boolean (optional)
#          @dial		   string, phone number to dial (optional)
#          @line		   integer, SIP line to use (optional)
#     addLine(text,align,color) to add a line in the current item
#          @text		   string
#          @align		   string, optional, "left", "right" or "center"
#          @color		   string, optional, "red", "black", ...
#
# Example
#    require_once('AastraIPPhoneIconMenu.class.php');
#    $menu = new AastraIPPhoneIconMenu();
#    $menu->setDestroyOnExit();
#    $menu->setDeFaultIndex('3');
#		 $menu->setLayout('1');
#		 $menu->setMode('regular');
#		 $menu->setFontMono('yes');
#		 $menu->addEntry('http://myserver.com/script.php?choice=1','Value=1','Icon:Alarm','yes');
#		 $menu->addLine('Line 11','left','red');
#		 $menu->addLine('Line 12','right','green');
#		 $menu->addLine('Line 13','center','blue');
#		 $menu->addEntry('http://myserver.com/script.php?choice=2','Value=2','Icon:AlarmFilled','no');
#		 $menu->addLine('Line 21','left','red');
#		 $menu->addLine('Line 22','right','green');
#		 $menu->addLine('Line 23','center','blue');
#    $menu->addSoftkey('1', 'My Select', 'http://myserver.com/script.php?action=1');
#    $menu->addSoftkey('6', 'Exit', 'SoftKey:Exit');
#    $menu->output();
#
########################################################################################################
require_once('AastraIPPhone.class.php');
require_once('AastraIPPhoneIconMenuEntry.class.php');

class AastraIPPhoneIconMenu extends AastraIPPhone {
	var $_layout='';
	var $_mode='';
	var $_defaultIndex='';
	var $_style='';
	var $_maxitems='24';
	var $_fontMono='';

	function setLayout($layout)
	{
		$this->_layout = $layout;
	}

	function setMode($mode)
	{
		$this->_mode = $mode;
	}

	function setFontMono($fontMono)
	{
		$this->_fontMono = $fontMono;
	}
    
	function setDefaultIndex($defaultIndex)
	{
		$this->_defaultIndex = $defaultIndex;
	}

	function setStyle($style)
	{
		$this->_style = $style;
	}

	function addEntry($url, $selection=NULL, $icon=NULL, $iconScaled=NULL, $fontMono=NULL, $dial=NULL, $line=NULL)
	{
		$this->_entries[] = new AastraIPPhoneIconMenuEntry( $url,
																												$selection,
																												$icon,
																												$iconScaled,
																												$fontMono,
																												$dial,
																												$line
																											);
	}

	function addLine($text, $align=NULL, $color=NULL)
	{
		$index=count($this->_entries);
		if($index>0) {
			$this->_entries[$index-1]->addLine($text, $align, $color);
		}
	}


	function render()
	{
		# Beginning of root tag		
		$out = "<AastraIPPhoneIconMenu";

		# DestroyOnExit
		if ($this->_destroyOnExit=='yes') $out .= " destroyOnExit=\"yes\"";

		# DefaultIndex
		if ($this->_defaultIndex!="") $out .= " defaultIndex=\"{$this->_defaultIndex}\"";

		# Layout
		if ($this->_layout!='') $out .= " layout=\"{$this->_layout}\"";

		# Mode
		if ($this->_mode!='') $out .= " mode=\"{$this->_mode}\"";

		# FontMono
		if ($this->_fontMono!='') $out .= " fontMono=\"{$this->_fontMono}\"";

		# Beep
		if ($this->_beep=='yes') $out .= " Beep=\"yes\"";

		# Lockin
		if($this->_lockin!='') {
			$out .= " LockIn=\"{$this->_lockin}\"";
   			if($this->_lockin_uri!='') $out .= " GoodbyeLockInURI=\"".$this->escape($this->_lockin_uri)."\"";
		}

		# Call Protection
		if($this->_callprotection!='') {
			$out .= " CallProtection=\"{$this->_callprotection}\"";
		}

		# Timeout
		if ($this->_timeout!=0) $out .= " Timeout=\"{$this->_timeout}\"";

		# Background color
		if ($this->_background_color!='') $out .= " bgColor=\"{$this->_background_color}\"";

		# Font Monotype
		if ($this->_fontMono == 'no') $out .= " fontMono=\"no\"";

		# End of root tag
		$out .= ">\n";

		# Top Title
		if ($this->_toptitle!='')
			{
			$toptitle = $this->escape($this->_toptitle);
		 	$out .= "<TopTitle";
		 	if ($this->_toptitle_icon!='') $out .= " icon=\"{$this->_toptitle_icon}\"";
		 	if ($this->_toptitle_color!='') $out .= " Color=\"{$this->_toptitle_color}\"";
			$out .= ">".$toptitle."</TopTitle>\n";
			}

		# Menu items
		if (isset($this->_entries) && is_array($this->_entries)) 
			{
			$index=0;
			$is_softkeys=Aastra_is_softkeys_supported();
			foreach ($this->_entries as $entry) 
				{
				if($index<$this->_maxitems) $out .= $entry->render();
				$index++;
				}
			}

		# Softkeys
		if (isset($this->_softkeys) && is_array($this->_softkeys)) 
			{
		  	foreach ($this->_softkeys as $softkey) $out .= $softkey->render();
			}

		# Icons
		if (isset($this->_icons) && is_array($this->_icons)) 
			{
  			$IconList=False;
  			foreach ($this->_icons as $icon) 
  				{
	  			if(!$IconList) 
  					{
	  				$out .= "<IconList>\n";
	  				$IconList=True;
	  				}
	  			$out .= $icon->render();
  				}
  			if($IconList) $out .= "</IconList>\n";
			}

		# End Tag
		$out .= "</AastraIPPhoneIconMenu>\n";

		# Return XML object
		return($out);
	}
}
?>
