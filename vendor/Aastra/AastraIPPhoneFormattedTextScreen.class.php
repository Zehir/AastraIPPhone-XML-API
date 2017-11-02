<?php
########################################################################################################
# Aastra XML API Classes - AastraIPFormattedPhoneTextScreen
# Copyright Mitel Networks 2005-2015
#
# AastraIPPhoneFormattedTextScreen object.
#
# Public methods
#
# Inherited from AastraIPPhone
#     setTopTitle(title,color,icon_index) to set the Top Title of the XML screen (6739i only)
#          @title		string
#          @color		string, "red", "blue", ... (optional)
#          @icon_index	integer, icon number
#     setCancelAction(uri) to set the cancel parameter with the URI to be called on Cancel (optional)
#          @uri		string
#     setDestroyOnExit() to set DestroyonExit parameter to 'yes', 'no' by default (optional)
#     setBeep() to enable a notification beep with the object (optional)
#     setLockIn(uri) to set the Lock-in tag to 'yes' and the GoodbyeLockInURI(optional)
#          @uri		string, GoodByeLockInURI
#     setLockInCall() to set the Lock-in tag to 'call' (optional)
#     setCallProtection(notif) to protect the XML object against incoming calls
#          @notif to enable/disable (false by default) the display of an incoming call notification (optional)
#     setAllowAnswer() to set the allowAnswer tag to 'yes' (optional only for non softkey phones)
#     setAllowDrop() to set the allowDrop tag to 'yes' (optional only for non softkey phones)
#     setAllowXfer() to set the allowXfer tag to 'yes' (optional only for non softkey phones)
#     setAllowConf() to set the allowConf tag to 'yes' (optional only for non softkey phones)
#     setTimeout(timeout) to define a specific timeout for the XML object (optional)
#          @timeout		integer (seconds)
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
#     addLine(text,size,align,color,wrap,blink) to add a formatted line
#          @text		string
#          @size		string, optional, "small", "double"  or "large"
#          @align		string, optional, "left", "right" or "center"
#          @color		string, optional, "red", "black", ...
#          @wrap    boolean, optional, "yes", "no" (default)
#          @blink   string, optional, "slow", "fast" or "no" (default)
#     addText(text,size,align,$color,wrap,blink) to add a formatted text as formatted lines
#          @text		string, can include carriage returns
#          @size		string, optional, "double"
#          @align		string, optional, "left", "right" or "center"
#          @color		string, optional, "red", "black", ...
#          @wrap    boolean, optional, "yes", "no" (default)
#          @blink   string, optional, "slow", "fast" or "no" (default)
#     setScrollStart(height) to define the beginning of the scrolling section and its height
#          @height		integer
#     setScrollEnd() to define the end of the scrolling section
#     setAllowDTMF() to allow DTMF passthrough on the object
#     setDoneAction(uri) to set the URI to be called when the user selects the default "Done" key (optional)
#          @uri		string
#     setScrollUp(uri) to set the URI to be called when the user presses the Up arrow (optional)
#          @uri		string
#     setScrollDown(uri) to set the URI to be called when the user presses the Down arrow (optional)
#          @uri		string
#     setScrollLeft(uri) to set the URI to be called when the user presses the Left arrow (optional)
#          @uri		string
#     setScrollRight(uri) to set the URI to be called when the user presses the Right arrow (optional)
#          @uri		string
#     setNoFontMono() to allow the override of the monotype font with a proportional font (6867i. 6869i and 6873i only)
#     setDial(number,line) to set the number to be dialed as well as the line to use when going off-hook or with the custom softkey Softkey::Dial2
#          @number  string
#          @line    integer (optional) 
#
# Example
#     require_once('AastraIPPhoneFormattedTextScreen.class.php');
#     $ftext = new AastraIPPhoneFormattedTextScreen();
#     $ftext->setDestroyOnExit();
#     $ftext->addLine('Formatted Screen','double','center');
#     $ftext->setScrollStart('2');
#     $ftext->addLine('Scrolled text1');
#     $ftext->addLine('Scrolled text2');
#     $ftext->addLine('Scrolled text3');
#     $ftext->addLine('Scrolled text4');
#     $ftext->addLine('Scrolled text5');
#     $ftext->setScrollEnd();
#     $ftext->addLine('Footer',NULL,'center');
#     $ftext->addSoftkey('1', 'Label', 'http://myserver.com/script.php?action=1','1');
#     $ftext->addSoftkey('6', 'Exit', 'SoftKey:Exit');
#     $ftext->addIcon('1', 'Icon:Envelope');
#     $ftext->output();   
#
########################################################################################################

require_once('AastraIPPhone.class.php');
require_once('AastraIPPhoneFormattedTextScreenEntry.class.php');

class AastraIPPhoneFormattedTextScreen extends AastraIPPhone {
   	var $_doneAction='';
	var $_allowDTMF='';
	var $_scrollUp='';
	var $_scrollDown='';
	var $_scrollLeft='';
	var $_scrollRight='';
	var $_dialNumber='';
	var $_dialLine='';
	var $_fontMono='';

	function addLine($text, $size=NULL, $align=NULL, $color=NULL, $wrap=NULL, $blink=NULL)
	{
		$this->_entries[] = new AastraIPPhoneFormattedTextScreenEntry($text, $size, $align, $color, $wrap, $blink, 'normal');
	}

	function addText($text, $size=NULL, $align=NULL, $color=NULL, $wrap=NULL, $blink=NULL)
	{
		$pieces=explode("\n",wordwrap($text,$this->_display_size,"\n",True));
		foreach($pieces as $value) $this->_entries[] = new AastraIPPhoneFormattedTextScreenEntry($value, $size, $align, $color, $wrap, $blink, 'normal');
	}

	function setScrollStart($height)
	{
		$this->_entries[] = new AastraIPPhoneFormattedTextScreenEntry(NULL, $height, NULL, NULL, NULL, NULL, 'scrollstart');
	}

	function setScrollEnd()
	{
		$this->_entries[] = new AastraIPPhoneFormattedTextScreenEntry(NULL, NULL, NULL, NULL, NULL, NULL, 'scrollend');
	}

	function setDoneAction($uri)
	{
		$this->_doneAction = $uri;
	}

	function setAllowDTMF()
	{
		$this->_allowDTMF = 'yes';
	}

	function setScrollUp($uri)
	{
		$this->_scrollUp = $uri;
	}

	function setScrollDown($uri)
	{
		$this->_scrollDown = $uri;
	}

	function setScrollLeft($uri)
	{
		$this->_scrollLeft = $uri;
	}

	function setScrollRight($uri)
	{
		$this->_scrollRight = $uri;
	}

	function setDial($dialNumber,$dialLine='')
	{
		$this->_dialNumber = $dialNumber;
		$this->_dialLine = $dialLine;
	}

	function setNoFontMono()
	{
		$this->_fontMono = 'no';
	}

	function render()
	{
		# Beginning of root tag
		$out = "<AastraIPPhoneFormattedTextScreen";

		# DestroyOnExit
		if($this->_destroyOnExit == 'yes') $out .= " destroyOnExit=\"yes\"";

		# CancelAction
		if($this->_cancelAction != '')
			{ 
			$cancelAction = $this->escape($this->_cancelAction);
			$out .= " cancelAction=\"{$cancelAction}\"";
			}

		# DoneAction
		if($this->_doneAction != '')
			{ 
			$doneAction = $this->escape($this->_doneAction);
			$out .= " doneAction=\"{$doneAction}\"";
			}

		# Beep
		if($this->_beep=='yes') $out .= " Beep=\"yes\"";

		# Lockin
		if($this->_lockin!='') {
			$out .= " LockIn=\"{$this->_lockin}\"";
   			if($this->_lockin_uri!='') $out .= " GoodbyeLockInURI=\"".$this->escape($this->_lockin_uri)."\"";
		}

		# Call Protection
		if($this->_callprotection!='') {
			$out .= " CallProtection=\"{$this->_callprotection}\"";
		}

		# AllowAnswer
		if($this->_allowAnswer == 'yes') $out .= " allowAnswer=\"yes\"";

		# AllowDrop
		if($this->_allowDrop == 'yes') $out .= " allowDrop=\"yes\"";

		# AllowXfer
		if($this->_allowXfer == 'yes') $out .= " allowXfer=\"yes\"";

		# AllowConf
		if($this->_allowConf == 'yes') $out .= " allowConf=\"yes\"";

		# TimeOut
		if($this->_timeout!=0) $out .= " Timeout=\"{$this->_timeout}\"";

		# Background color
		if ($this->_background_color!='') $out .= " bgColor=\"{$this->_background_color}\"";
	
		# AllowDTMF
		if($this->_allowDTMF=='yes') $out .= " allowDTMF=\"yes\"";

		# Scrolls up/down/left/right
		if($this->_scrollUp!='') $out .= " scrollUp=\"".$this->escape($this->_scrollUp)."\"";
		if($this->_scrollDown!='') $out .= " scrollDown=\"".$this->escape($this->_scrollDown)."\"";
		if($this->_scrollLeft!='') $out .= " scrollLeft=\"".$this->escape($this->_scrollLeft)."\"";
		if($this->_scrollRight!='') $out .= " scrollRight=\"".$this->escape($this->_scrollRight)."\"";

		# Font Monotype
		if ($this->_fontMono == 'no') $out .= " fontMono=\"no\"";
	
		# End of Root tag
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

		# Lines
		if (isset($this->_entries) && is_array($this->_entries)) 
			{
			foreach ($this->_entries as $entry) $out .= $entry->render();
			}

		# Dial
		if ($this->_dialNumber!='')
			{
			$dialNumber = $this->escape($this->_dialNumber);
		 	$out .= "<Dial";
		 	if ($this->_dialLine!='') $out .= " line=\"{$this->_dialLine}\"";
			$out .= ">".$dialNumber."</Dial>\n";
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

		# End tag
		$out .= "</AastraIPPhoneFormattedTextScreen>\n";

		# Return XML object
		return $out;
	}
}
?>
