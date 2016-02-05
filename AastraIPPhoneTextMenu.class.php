<?php
########################################################################################################
# Aastra XML API Classes - AastraIPPhoneTextMenu
# Copyright Aastra Telecom 2005-2010
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
#     setDefaultIndex(index) to set the default selection in the list (optional)
#          @index		index (1-30)
#     setStyle(style) to set the style of the list (optional)
#          @style		enum (numbered/none/radio)
#     setWrapList() to allow 2 lines items (optional)
#     setScrollConstrain() to avoid the list to wrap
#     setNumberLaunch() to allow number selection
#     setBase(base) to configure the menuItem base URI
#          @base		string
#     resetBase() to reset the menuItem base URI
#     addEntry(name,url,selection,icon,dial,line,color,split) to add an element in the list to be displayed
#          @name		string or array(0=>Line1,1=>Line2,2=>Offset,3=>Char,4=>Mode)
#          @url		string
#          @selection	string (optional)
#          @icon		integer (optional)
#          @dial		string, phone number to dial (optional)
#          @line		integer, SIP line to use (optional)
#          @color		string, "red", "black", ... (optional)
#          @split		integer, position of the split between line 1 and line 2 (optional)
#     setScrollUp(uri) to set the URI to be called when the user presses the Up arrow (optional)
#          @uri		string
#     setScrollDown(uri) to set the URI to be called when the user presses the Down arrow (optional)
#          @uri		string
#     setUnitScroll() to set the unitScroll tag to yes which changes the scrolling behavior on the 6739i (optional and 6739i only)
#     natsortbyname() to order the list, must not be use in conjunction with setBase or resetBase
#
# Example 1
#    require_once('AastraIPPhoneTextMenu.class.php');
#    $menu = new AastraIPPhoneTextMenu();
#    $menu->setTitle('Title');
#    $menu->setDestroyOnExit();
#    $menu->setDeFaultIndex('3');
#    $menu->addEntry('Choice 2', 'http://myserver.com/script.php?choice=2', 'Value=2');
#    $menu->addEntry('Choice 1', 'http://myserver.com/script.php?choice=1', 'Value=1');
#    $menu->addEntry('Choice 3', 'http://myserver.com/script.php?choice=3', 'Value=3');
#    $menu->natsortByName();
#    $menu->addSoftkey('1', 'My Select', 'http://myserver.com/script.php?action=1');
#    $menu->addSoftkey('6', 'Exit', 'SoftKey:Exit');
#    $menu->output();
#
# Example 2
#    require_once('AastraIPPhoneTextMenu.class.php');
#    $menu = new AastraIPPhoneTextMenu();
#    $menu->setTitle('Title');
#    $menu->setDestroyOnExit();
#    $menu->setDeFaultIndex('2');
#    $menu->addEntry('Choice 2', 'http://myserver.com/script.php?choice=2', 'Value=2','1');
#    $menu->addEntry('Choice 1', 'http://myserver.com/script.php?choice=1', 'Value=1','2');
#    $menu->addEntry('Choice 3', 'http://myserver.com/script.php?choice=3', 'Value=3','3');
#    $menu->natsortByName();
#    $menu->addSoftkey('1', 'My Select', 'http://myserver.com/script.php?action=1');
#    $menu->addSoftkey('6', 'Exit', 'SoftKey:Exit');
#    $menu->addIcon('1', 'Icon:PhoneOnHook');
#    $menu->addIcon('2', 'Icon:PhoneOffHook');
#    $menu->addIcon('3', 'Icon:PhoneRinging');
#    $menu->output();
#
########################################################################################################
require_once('AastraIPPhone.class.php');
require_once('AastraIPPhoneTextMenuEntry.class.php');

class AastraIPPhoneTextMenu extends AastraIPPhone {
	var $_defaultIndex='';
	var $_style='';
	var $_wraplist='';
	var $_maxitems='30';
	var $_scrollConstrain='';
	var $_numberLaunch='';
	var $_scrollUp='';
	var $_scrollDown='';
       var $_unitScroll='';
    
	function setDefaultIndex($defaultIndex)
	{
		$this->_defaultIndex = $defaultIndex;
	}

	function setStyle($style)
	{
		$this->_style = $style;
	}

	function addEntry($name, $url, $selection=NULL, $icon=NULL, $dial=NULL, $line=NULL, $color='', $split=NULL)
	{
		if(!Aastra_is_icons_supported()) $icon=NULL;
		$this->_entries[] = new AastraIPPhoneTextMenuEntry($name, $url, $selection, $icon, $dial, $line, $color, $split, NULL);
	}

	function setBase($base)
	{
		$this->_entries[] = new AastraIPPhoneTextMenuEntry(NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, $base);
	}

	function resetBase()
	{
		$this->_entries[] = new AastraIPPhoneTextMenuEntry(NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'AASTRA_RESETBASE');
	}

	function setWrapList()
	{
		$this->_wraplist = 'yes';
	}

	function setScrollConstrain()
	{
		$this->_scrollConstrain = 'yes';
	}

	function setUnitScroll()
	{
		$this->_unitScroll = 'yes';
	}

	function setNumberLaunch()
	{
		$this->_numberLaunch = 'yes';
	}

	function setScrollUp($uri)
	{
		$this->_scrollUp = $uri;
	}

	function setScrollDown($uri)
	{
		$this->_scrollDown = $uri;
	}

	function natsortByName()
	{
		$tmpary = array();
		foreach ($this->_entries as $id => $entry) $tmpary[$id] = $entry->getName();
		natsort($tmpary);
		foreach ($tmpary as $id => $name) $newele[] = $this->_entries[$id];
		$this->_entries = $newele;
	}

	function render()
	{
		# Beginning of root tag		
		$out = "<AastraIPPhoneTextMenu";

		# DestroyOnExit
		if ($this->_destroyOnExit=='yes') $out .= " destroyOnExit=\"yes\"";

		# CancelAction
		if($this->_cancelAction != "")
			{ 
			$cancelAction = $this->escape($this->_cancelAction);
			$out .= " cancelAction=\"{$cancelAction}\"";
			}

		# DefaultIndex
		if ($this->_defaultIndex!="") $out .= " defaultIndex=\"{$this->_defaultIndex}\"";

		# Style
		if ($this->_style!='') $out .= " style=\"{$this->_style}\"";

		# Beep
		if ($this->_beep=='yes') $out .= " Beep=\"yes\"";

		# Lockin
		if($this->_lockin!='') {
			$out .= " LockIn=\"{$this->_lockin}\"";
   			if($this->_lockin_uri!='') $out .= " GoodbyeLockInURI=\"".$this->escape($this->_lockin_uri)."\"";
		}

		# WrapList
		if ($this->_wraplist=='yes') $out .= " wrapList=\"yes\"";

		# AllowAnswer
		if ($this->_allowAnswer == 'yes') $out .= " allowAnswer=\"yes\"";

		# AllowDrop
		if ($this->_allowDrop == 'yes') $out .= " allowDrop=\"yes\"";

		# AllowXfer
		if ($this->_allowXfer == 'yes') $out .= " allowXfer=\"yes\"";

		# AllowConf
		if ($this->_allowConf == 'yes') $out .= " allowConf=\"yes\"";

		# Timeout
		if ($this->_timeout!=0) $out .= " Timeout=\"{$this->_timeout}\"";

		# Prevent list wrap
		if ($this->_scrollConstrain == 'yes') $out .= " scrollConstrain=\"yes\"";

		# Number selection
		if ($this->_numberLaunch == 'yes') $out .= " numberLaunch=\"yes\"";

		# Scrolls up/down
		if($this->_scrollUp!='') $out .= " scrollUp=\"".$this->escape($this->_scrollUp)."\"";
		if($this->_scrollDown!='') $out .= " scrollDown=\"".$this->escape($this->_scrollDown)."\"";

		# Unit scroll (6739i)
		if ($this->_unitScroll == 'yes') $out .= " unitScroll=\"yes\"";

		# Background color(6739i)
		if ($this->_bcolor != '') $out .= " Bcolor=\"{$this->_bcolor}\"";

		# End of root tag
		$out .= ">\n";

		# Title
		if ($this->_title!='')
			{
			$title = $this->escape($this->_title);
		 	$out .= "<Title";
		 	if ($this->_title_wrap=='yes') $out .= " wrap=\"yes\"";
		 	if ($this->_title_color!='') $out .= " Color=\"{$this->_title_color}\"";
			$out .= ">".$title."</Title>\n";
			}

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
			$base=NULL;
			$length=Aastra_size_display_line();
			$is_softkeys=Aastra_is_softkeys_supported();
			foreach ($this->_entries as $entry) 
				{
				if($entry->getBase()!=NULL) $base=$entry->getBase();
				else
					{
					if($base!=NULL)
						{
						if($index<$this->_maxitems) $out .= $entry->render($this->_style,$length,$is_softkeys,$base);
						$base=NULL;
						}
					else if($index<$this->_maxitems) $out .= $entry->render($this->_style,$length,$is_softkeys);
					$index++;
					}
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
		$out .= "</AastraIPPhoneTextMenu>\n";

		# Return XML object
		return($out);
	}
}
?>
