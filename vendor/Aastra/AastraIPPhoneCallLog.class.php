<?php
########################################################################################################
# Aastra XML API Classes - AastraIPPhoneCallLog
# Copyright Mitel Networks 2005-2015
#
# AastraIPPhoneCallLog object.
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
#     setAllowAnswer() to set the allowAnswer tag to 'yes' (optional only for non softkey phones)
#     setAllowDrop() to set the allowDrop tag to 'yes' (optional only for non softkey phones)
#     setAllowXfer() to set the allowXfer tag to 'yes' (optional only for non softkey phones)
#     setAllowConf() to set the allowConf tag to 'yes' (optional only for non softkey phones)
#     setTimeout(timeout) to define a specific timeout for the XML object (optional)
#          @timeout		integer (seconds)
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
#     addEntry(name,number,date,time,selection,duration,type,terminal,count,line)
#          @name		string (optional) 
#          @number		string 
#          @date		string MM-DD-YYYY
#          @time		string HH:MM (military time)
#          @selection	string (optional)
#          @duration	integer call duration in seconds (optional)
#          @type		string call type (incoming/outgoung/missed) (optional)
#          @terminal	string terminal type (office/mobile/home) (optional)
#          @count	    integer number of calls (optional)
#          @line	    integer line used (1-9) (optional)
#     setScrollConstrain() to avoid the list to wrap
#     setScrollUp(uri) to set the URI to be called when the user presses the Up arrow (optional)
#          @uri		string
#     setScrollDown(uri) to set the URI to be called when the user presses the Down arrow (optional)
#          @uri		string
#     setDeleteUri(uri) to configure the uri called by the "Delete" button (optional)
#          @uri		string
#     setDeleteAllUri(uri) to configure the uri called by the "Delete ALL" button (optional)
#          @uri		string
#     setDialUri(uri) to configure the uri called by the "Dial" button (optional)
#          @uri		string
#     setAddUri(uri) to configure the uri called by the "Add to directory" button(optional)
#          @uri		string
#
# Example 
#
########################################################################################################
require_once('AastraIPPhone.class.php');
require_once('AastraIPPhoneCallLogEntry.class.php');

class AastraIPPhoneCallLog extends AastraIPPhone {
	var $_maxitems='50';
	var $_scrollConstrain='';
	var $_scrollUp='';
	var $_scrollDown='';
	var $_delete='';
	var $_deleteAll='';
	var $_dial='';
	var $_add='';
    
	function addEntry($name, $number, $date, $time, $selection, $duration, $type, $terminal, $count='', $line='')
	{
		$this->_entries[] = new AastraIPPhoneCallLogEntry($name, $number, $date, $time, $selection, $duration, $type, $terminal, $count, $line);
	}


	function setScrollConstrain()
	{
		$this->_scrollConstrain = 'yes';
	}

	function setScrollUp($uri)
	{
		$this->_scrollUp = $uri;
	}

	function setScrollDown($uri)
	{
		$this->_scrollDown = $uri;
	}

	function setDeleteUri($uri)
	{
		$this->_delete = $uri;
	}

	function setDeleteAllUri($uri)
	{
		$this->_deleteAll = $uri;
	}

	function setDialUri($uri)
	{
		$this->_dial = $uri;
	}

	function setAddUri($uri)
	{
		$this->_add = $uri;
	}

	function render()
	{
		# Beginning of root tag		
		$out = "<AastraIPPhoneCallLog";

		# DestroyOnExit
		if ($this->_destroyOnExit=='yes') $out .= " destroyOnExit=\"yes\"";

		# CancelAction
		if($this->_cancelAction != "") { 
			$cancelAction = $this->escape($this->_cancelAction);
			$out .= " cancelAction=\"{$cancelAction}\"";
		}

		# Beep
		if ($this->_beep=='yes') $out .= " Beep=\"yes\"";

		# Lockin
		if($this->_lockin!='') {
			$out .= " LockIn=\"{$this->_lockin}\"";
   			if($this->_lockin_uri!='') $out .= " GoodbyeLockInURI=\"".$this->escape($this->_lockin_uri)."\"";
		}

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

		# Scrolls up/down
		if($this->_scrollUp!='') $out .= " scrollUp=\"".$this->escape($this->_scrollUp)."\"";
		if($this->_scrollDown!='') $out .= " scrollDown=\"".$this->escape($this->_scrollDown)."\"";

		# Misc uris
		if($this->_delete!='') $out .= " deleteUri=\"".$this->escape($this->_delete)."\"";
		if($this->_deleteAll!='') $out .= " deleteAllUri=\"".$this->escape($this->_deleteAll)."\"";
		if($this->_dial!='') $out .= " dialUri=\"".$this->escape($this->_dial)."\"";
		if($this->_add!='') $out .= " addUri=\"".$this->escape($this->_add)."\"";

		# End of root tag
		$out .= ">\n";

		# Top Title
		if ($this->_toptitle!='') {
			$toptitle = $this->escape($this->_toptitle);
		 	$out .= "<TopTitle";
		 	if ($this->_toptitle_icon!='') $out .= " icon=\"{$this->_toptitle_icon}\"";
		 	if ($this->_toptitle_color!='') $out .= " Color=\"{$this->_toptitle_color}\"";
			$out .= ">".$toptitle."</TopTitle>\n";
		}

		# Menu items
		if (isset($this->_entries) && is_array($this->_entries)) {
			$index=0;
			foreach ($this->_entries as $entry) {
				if($index<$this->_maxitems) $out .= $entry->render($this->_style,$length,$is_softkeys);
				$index++;
			}
		}

		# Icons
		if (isset($this->_icons) && is_array($this->_icons)) {
  			$IconList=False;
  			foreach ($this->_icons as $icon)  {
	  			if(!$IconList) {
	  				$out .= "<IconList>\n";
	  				$IconList=True;
	  			}
	  			$out .= $icon->render();
  			}
  			if($IconList) $out .= "</IconList>\n";
		}

		# End Tag
		$out .= "</AastraIPPhoneCallLog>\n";

		# Return XML object
		return($out);
	}
}
?>
