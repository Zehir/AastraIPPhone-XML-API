<?php
###################################################################################################
# Aastra XML API Classes - AastraIPPhone
# Copyright Aastra Telecom 2005-2010
#
# AastraIPPhone is the root class for all the Aastra XML objects.
#
# Public methods
#     setTitle(Title,color) to setup the title of an object (optional)
#          @title		string
#          @color		string, "red", "blue", ... (optional)
#     setTitleWrap() to set the title to be wrapped on 2 lines (optional)
#     setTopTitle(title,color,icon_index) to set the Top Title of the XML screen (6739i only)
#          @title		string
#          @color		string, "red", "blue", ... (optional)
#          @icon_index	integer, icon number
#     setBackgroundColor(color) to change the XML object background color (optional 6739i only)
#          @color		string, "red", "blue", ...
#     setCancelAction(uri) to set the cancel parameter with the URI to be called on Cancel (optional)
#          @uri		string
#     setDestroyOnExit() to set DestroyonExit parameter to 'yes', 'no' by default (optional)
#     setBeep() to enable a notification beep with the object (optional)
#     setLockIn(uri) to set the Lock-in tag to 'yes' and the GoodbyeLockInURI(optional)
#          @uri		string, GoodByeLockInURI
#     setLockInCall() to set the Lock-in tag to 'call' (optional)
#     setAllowAnswer() to set the allowAnswer tag to 'yes' (optional only for non softkey phones)
#     setAllowDrop() to set the allowDrop tag to 'yes' (optional only for non softkey phones)
#     setAllowXfer() to set the allowXfer tag to 'yes' (optional only for non softkey phones)
#     setAllowConf() to set the allowConf tag to 'yes' (optional only for non softkey phones)
#     setTimeout(timeout) to define a specific timeout for the XML object (optional)
#          @timeout		integer (seconds)
#     addSoftkey(index,label,uri,icon_index,color) to add custom soktkeys to the object (optional)
#          @index		integer, softkey number
#          @label		string
#          @uri		string
#          @icon_index	integer, icon number
#          @color		string, "red", "blue", ... (optional)
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
###################################################################################################

###################################################################################################
# Includes
###################################################################################################
require_once('AastraIPPhone.php');
require_once('AastraIPPhoneSoftkeyEntry.class.php');
require_once('AastraIPPhoneIconEntry.class.php');

class AastraIPPhone {
	var $_entries;
	var $_softkeys;
	var $_icons;
	var $_title='';
	var $_title_wrap='';
	var $_title_color='';
	var $_destroyOnExit='';
	var $_cancelAction='';
	var $_refreshTimeout=0;
	var $_refreshURL='';
    var $_beep='';
	var $_lockin='';
	var $_timeout=0;
	var $_allowAnswer='';
	var $_allowDrop='';
	var $_allowXfer='';
	var $_allowConf='';
	var $_bcolor='';
	var $_toptitle='';
	var $_toptitle_icon='';
	var $_toptitle_color='';
	var $_encoding='ISO-8859-1';

	function AastraIPPhone()
	{
		# Variables for the XML object
		$this->_entries = array();
		$this->_softkeys = array();
		$this->_icons = array();
		$this->_title = '';
		$this->_destroyOnExit='';
		$this->_refreshTimeout=0;
		$this->_refreshURL='';
       	$this->_beep='';
		$this->_lockin='';
		$this->_lockin_uri='';
		$this->_timeout=0;
		$this->_allowAnswer='';
		$this->_allowDrop='';
		$this->_allowXfer='';
		$this->_allowConf='';
	}

	function setEncodingUTF8()
	{
		$this->_encoding = 'UTF-8';
	}

	function setBackgroundColor($color)
	{
		$this->_bcolor = $color;
	}

	function setTitle($title,$color='')
	{
		$this->_title = $title;
		$this->_title_color = $color;
	}

	function setTopTitle($title,$color='',$icon='')
	{
		$this->_toptitle = $title;
		$this->_toptitle_color = $color;
		$this->_toptitle_icon = $icon;
	}

	function setTitleWrap()
	{
		$this->_title_wrap = 'yes';
	}

	function setRefresh($timeout,$URL)
	{
		$this->_refreshTimeout = $timeout;
		$this->_refreshURL = $URL;
	}

	function setBeep() 
	{
		$this->_beep='yes';
	}

	function setDestroyOnExit() 
	{
		$this->_destroyOnExit='yes';
	}

	function setCancelAction($cancelAction) 
	{
		$this->_cancelAction=$cancelAction;
	}


	function setLockIn($uri='') 
	{
		$this->_lockin='yes';
		$this->_lockin_uri=$uri;
	}

	function setLockInCall() 
	{
		$this->_lockin='call';
	}

	function setTimeout($timeout) 
	{
		$this->_timeout=$timeout;
	}

	function setAllowAnswer() 
	{
		$this->_allowAnswer='yes';
	}

	function setAllowDrop() 
	{
		$this->_allowDrop='yes';
	}

	function setAllowXfer() 
	{
		$this->_allowXfer='yes';
	}

	function setAllowConf() 
	{
		$this->_allowConf='yes';
	}

	function output($flush=False)
	{
		header("Content-Type: text/xml; charset=".$this->_encoding);
		if (($this->_refreshTimeout!=0) and ($this->_refreshURL!='')) header("Refresh: ".$this->_refreshTimeout."; url=".$this->_refreshURL);
		$output="<?xml version=\"1.0\" encoding=\"".$this->_encoding."\"?>\n";
		$output.=$this->render();
		header('Content-Length: '.strlen($output));
		echo($output);
		if($flush)
			{
			ob_flush(); 
			flush(); 
			}
	}

	function generate()
	{
		return($this->render());
	}

	function addSoftkey($index, $label, $uri, $icon=NULL, $color='')
	{
		if(!Aastra_is_icons_supported()) $icon=NULL;
		if(($index>=1) and ($index<=Aastra_number_softkeys_supported())) $this->_softkeys[$index] = new AastraIPPhoneSoftkeyEntry($index, $this->escape($label), $this->escape($uri), $icon, $color);
		$this->_softkeys[$index] = new AastraIPPhoneSoftkeyEntry($index, $this->escape($label), $this->escape($uri), $icon, $color);
	}

	function addIcon($index, $icon)
	{
		if(Aastra_is_icons_supported()) $this->_icons[$index] = new AastraIPPhoneIconEntry($index, $icon);
	}

	function escape($string)
	{
 	return(str_replace(
  		array('&', '<', '>', '"', "'"),
  		array('&amp;', '&lt;', '&gt;', '&quot;', '&apos;'),
  		$string
 		));
	}
 
	function convert_high_ascii($s) 
	{
 		$HighASCII = array(
 					"!\xc0!" => 'A',    # A`
			 		"!\xe0!" => 'a',    # a`
			 		"!\xc1!" => 'A',    # A'
			 		"!\xe1!" => 'a',    # a'
			 		"!\xc2!" => 'A',    # A^
			 		"!\xe2!" => 'a',    # a^
			 		"!\xc4!" => 'Ae',   # A:
			 		"!\xe4!" => 'ae',   # a:
			 		"!\xc3!" => 'A',    # A~
			 		"!\xe3!" => 'a',    # a~
			 		"!\xc8!" => 'E',    # E`
			 		"!\xe8!" => 'e',    # e`
			 		"!\xc9!" => 'E',    # E'
			 		"!\xe9!" => 'e',    # e'
			 		"!\xca!" => 'E',    # E^
			 		"!\xea!" => 'e',    # e^
			 		"!\xcb!" => 'Ee',   # E:
			 		"!\xeb!" => 'ee',   # e:
			 		"!\xcc!" => 'I',    # I`
			 		"!\xec!" => 'i',    # i`
			 		"!\xcd!" => 'I',    # I'
			 		"!\xed!" => 'i',    # i'
			 		"!\xce!" => 'I',    # I^
			 		"!\xee!" => 'i',    # i^
			 		"!\xcf!" => 'Ie',   # I:
			 		"!\xef!" => 'ie',   # i:
			 		"!\xd2!" => 'O',    # O`
			 		"!\xf2!" => 'o',    # o`
			 		"!\xd3!" => 'O',    # O'
			 		"!\xf3!" => 'o',    # o'
			 		"!\xd4!" => 'O',    # O^
			 		"!\xf4!" => 'o',    # o^
			 		"!\xd6!" => 'Oe',   # O:
			 		"!\xf6!" => 'oe',   # o:
			 		"!\xd5!" => 'O',    # O~
			 		"!\xf5!" => 'o',    # o~
			 		"!\xd8!" => 'Oe',   # O/
			 		"!\xf8!" => 'oe',   # o/
			 		"!\xd9!" => 'U',    # U`
		 			"!\xf9!" => 'u',    # u`
					"!\xda!" => 'U',    # U'
			 		"!\xfa!" => 'u',    # u'
			 		"!\xdb!" => 'U',    # U^
					"!\xfb!" => 'u',    # u^
			 		"!\xdc!" => 'Ue',   # U:
			 		"!\xfc!" => 'ue',   # u:
			 		"!\xc7!" => 'C',    # ,C
			 		"!\xe7!" => 'c',    # ,c
			 		"!\xd1!" => 'N',    # N~
			 		"!\xf1!" => 'n',    # n~
			 		"!\xdf!" => 'ss'
 					);
	 	$find = array_keys($HighASCII);
	 	$replace = array_values($HighASCII);
 		$s = preg_replace($find,$replace,$s);
	     	return $s;
	}
}
?>
