<?php
########################################################################################################
# Aastra XML API Classes - AastraIPPhoneScrollableTextMenu
#
# Aastra SIP Phones 1.4.2 or better
#
# Copyright Mitel Networks 2005-2015
#
# Supported Aastra Phones
#   All IP phones except Aastra9112i and Aastra9133i
#
# AastraIPPhoneScrollableTextMenu object.
#
# Public methods
#     setEntries(entries) Set entries of the list by 2 dim array. Inner array field names: 'name', 'url', 'selection', 'icon', 'dial'
#     verifyCookie(cookie) Verifies if the cookie of the HTTP requests matches the cookie of the saved context.
#     setBackURI(URI) Set the cancel parameter with the URI to be called on Cancel or Back Softkey (optional)
#     setBackKeyPosition(position) Set position of Back Softkey. Default is 3.
#     setNextKeyPosition(position) Set position of Next Softkey. Default is 4.
#     setPreviousKeyPosition(position) Set position of Previous Softkey. Default is 5.
#     setExitKeyPosition(position) Set position of Back Softkey. Default is 6.
#     setSelectKeyPosition(position) Set position of Back Softkey. Default is 1.
#     setNextKeyIcon(icon) Set icon of Next Softkey. Default is Icon:TailArrowDown. Set NULL to disable icon.
#     setPreviousKeyIcon(icon) Set icon of Previous Softkey. Default is Icon:TailArrowUp. Set NULL to disable icon.
#     disableExitKey() Disable the Exit Softkey
#     setSelectKeyLabel(label) Set the label of the Select Softkey. Default is 'Select'. Make sure the string is in language.ini.
#
# Overwritten methods from AastraIPPhoneTextMenu
#     setCancelAction(uri) to set the cancel parameter with the URI to be called on Cancel or Back Softkey (optional)
#     output() to display the object
#     addEntry(name,url,selection,icon,dial) to add an element in the list to be displayed
#     natsortbyname() to order the list
#
# Inherited from AastraIPPhoneTextMenu
#     setTitle(Title) to setup the title of an object (optional)
#     setTitleWrap() to set the title to be wrapped on 2 lines (optional)
#     setTopTitle(title,color,icon_index) to set the Top Title of the XML screen (6739i only)
#     setDestroyOnExit() to set DestroyOnExit parameter to "yes" (optional)
#     setBeep() to enable a notification beep with the object (optional)
#     setLockIn(uri) to set the Lock-in tag to 'yes' and the GoodbyeLockInURI (optional)
#     setAllowAnswer() to set the allowAnswer tag to 'yes' (optional)
#     setTimeout(timeout) to define a specific timeout for the XML object (optional)
#     addSoftkey(index,label,uri,icon_index) to add custom softkeys to the object (optional)     
#     setRefresh(timeout,URL) to add Refresh parameters to the object (optional)
#     generate() to return the object content
#     setDefaultIndex(index) to set the default selection in the list (optional)
#     setStyle(style) to set the style of the list numbered/none/radio (optional)
#     setWrapList() to allow 2 lines items (optional)
#
#	Example 1:
#		$menu = new AastraIPPhoneScrollableTextMenu();
#		$menu->setTitle('My Menu');
#		$menu->addEntry('Choice 1', $XML_SERVER."?choice=1", '1');
#		# ... add as many entries you want
#		$menu->addEntry('Choice 100', $XML_SERVER."?choice=100", '100');
#		$menu->output(); # Page scrolling will be handled by AastraIPPhoneScrollableTextMenu
#			
#	Example 2:
#		$entries[0]['name'] = "Choice 1";
#		$entries[0]['url'] = $XML_SERVER."?choice=1";
#		$entries[0]['selection'] = "1";
#		# ... add as many entries you want
#		$entries[99]['name'] = "Choice 100";
#		$entries[99]['url'] = $XML_SERVER."?choice=100";
#		$entries[99]['selection'] = "100";
#		$menu = new AastraIPPhoneScrollableTextMenu();
#		$menu->setTitle('My Menu');
#		$menu->setEntries($entries):
#		$menu->output(); # Page scrolling will be handled by AastraIPPhoneScrollableTextMenu
#
########################################################################################################
require_once('AastraCommon.php');
require_once('AastraIPPhoneTextMenu.class.php');

class AastraIPPhoneScrollableTextMenu extends AastraIPPhoneTextMenu {
	
	var $_language;
	var $_list = array(); # All entries of the list 
	var $_count = 0; # Total number of entries
	var $_cookie; # Cookie - unique per AastraIPPhoneScrollableTextMenu instance
	var $_header;
	var $_scrollHandlerReference;
	var $_backCancelURL;
	var $_maxLines;
	var $_exitKeyDisabled;
	var $_selectKeyLabel = 'Select';
	var $_backKeyPosition = 3;
	var $_exitKeyPosition = 6;
	var $_selectKeyPosition = 1;
	var $_nextKeyPosition = 4;
	var $_previousKeyPosition = 5;
	var $_backKeyIconIndex = 8;
	var $_selectKeyIconIndex = 9;
	var $_exitKeyIconIndex = 10;
	var $_nextKeyIconIndex = 11;
	var $_previousKeyIconIndex = 12;
	var $_nextKeyIcon = 'Icon:TailArrowDown';
	var $_previousKeyIcon = 'Icon:TailArrowUp';
	
	# Constructor	
	function AastraIPPhoneScrollableTextMenu() 
	{	
		# Get Language
		$this->_language = Aastra_get_language();

		# Decode HTTP header
		$this->_header = Aastra_decode_HTTP_header();

		# Generate new cookie
		$this->_cookie = $this->_generateCookie();

		# Generate Scroll Handler reference
		global $XML_HTTP, $AA_XML_SERVER, $AA_XMLDIRECTORY;
		$this->_scrollHandlerReference = $XML_HTTP.$AA_XML_SERVER."/".$AA_XMLDIRECTORY."/include/AastraIPPhoneScrollHandler.php?listCookie=".$this->_cookie;

		# Calculate max linex
		$this->_calculateMaxLines();

		# Modify some values for 6739i
		if(Aastra_number_softkeys_supported()==10)
			{
			$this->_exitKeyPosition = 10;
			$this->_nextKeyPosition = 8;
			$this->_previousKeyPosition = 7;
			$this->_selectKeyPosition = 3;
			}
	}	
		
	function addEntry($name, $url, $selection=NULL, $icon=NULL, $dial=NULL) 
	{
		$entry['name'] = $name;
		$entry['url'] = $url;
		$entry['selection'] = $selection;
		$entry['icon'] = $icon;
		$entry['dial'] = $dial;
		$this->_list[] = $entry;
	}
	
	function setEntries($entries) 
	{
		$this->_list = $entries;
	}

	function output($page=NULL) 
	{
		# Test phone firmware / model
		Aastra_test_phone_version('1.4.2.',0);
		Aastra_test_phone_model(array('Aastra9112i','Aastra9133i'),False,0);

		# Force destroyOnExit
		$this->_destroyOnExit = 'yes';

		# Initial call?
		if (!isset($page)) 
			{
			# Count number of entries in list
			$this->_count = count($this->_list);
			
			# Setup icons
			$this->_setupIcons();

			# Setup Softkeys
			$this->_setupSoftKeys();
			
			# Set Cancel URI
			if (!empty($this->_backCancelURL)) parent::setCancelAction($this->_backCancelURL);
			
			# Do some security / compliancy checks
			# Protect against wrap list bug in FW < R2.4.0
			if (Aastra_test_phone_version('2.4.0.',1)) $this->_wraplist = 'no';
			if (!Aastra_is_wrap_title_supported())$this->_wraplist = 'no';			
			if (!Aastra_is_textmenu_wrapitem_supported()) $this->_title_wrap = 'no';
			if (!Aastra_is_style_textmenu_supported()) $this->_style = '';
			if (!Aastra_is_lockin_supported()) $this->_lockin = 'no';
			
			# Save object in user context (context = mac address)
			Aastra_save_user_context($this->_header['mac'],'scrollableTextMenuData',$this);
			} 
		else 
			{
			# If beep is set, only beep during initial call
			$this->_beep='no';
			}

		# Generate the actual items of the menu for the given page
		$this->_generatePage($page);
		parent::output();
	}
	
	function _generatePage($page) 
	{
		# Empty List protection (to avoid 'cannot display')
		if ($this->_count==0) 
			{
			$tmpEntry['name'] = '[NO ENTRIES]';
			$this->_list[] = $tmpEntry;
			$this->_count++;
			}
			
		# Calculate total number of pages
		$last = ceil($this->_count / $this->_maxLines);
		
		# Invalid page protection
		if (empty($page) or $page < 1) $page = 1;
		if ($page > $last) $page = $last;
		
		# On phones without softkeys: Add dummy entry that allows to jump to previous page
		if (!Aastra_is_softkeys_supported() && ($page > 1 && $last > 1)) $this->_entries[] = new AastraIPPhoneTextMenuEntry('['.Aastra_get_label('Previous Page',$this->_language).']', $this->_scrollHandlerReference.'&listPage='.($page-1), NULL, NULL, NULL, NULL,NULL);
		
		# Populate list for current page
		for ($i = 0; ($i < $this->_maxLines and ($i + (($page - 1) * $this->_maxLines)) <  $this->_count); $i++) 
			{
			# Get entry from list
			$tmpEntry = $this->_list[$i + (($page - 1) * $this->_maxLines)];
			
			# Add 'recentSelection' and 'recentPage' attribute to URL of the entry.
			# Allows to jump back to same page / selection
			$tmpURI = $tmpEntry['url'];

			# Check if URI is an HTTP(S) URL (could also be an URI like Dial:1234)
			if (preg_match('/^http/i',$tmpURI)) 
				{
				# Check if URL already contains parameters
				if (preg_match('/\?/',$tmpURI)) $tmpEntry['url'] = $tmpEntry['url'].'&recentSelection='.($i+1).'&recentPage='.$page;
				else $tmpEntry['url'] = $tmpEntry['url'].'?recentSelection='.($i+1).'&recentPage='.$page;
				}
			
			# Make sure we don't add menu items the phone firmware cannot handle
			if (!Aastra_is_icons_supported()) $tmpEntry['icon'] = NULL; 
			if (Aastra_test_phone_version('2.0.1.',1)) $tmpEntry['dial'] = NULL; 
			$this->_entries[] = new AastraIPPhoneTextMenuEntry($tmpEntry['name'], $tmpEntry['url'], $tmpEntry['selection'], $tmpEntry['icon'], $tmpEntry['dial'], NULL, NULL, NULL, NULL);
			}
		
		# On phones without softkeys: Add dummy entry that allows to jump to next page
		if (!Aastra_is_softkeys_supported() && ($page < $last)) $this->_entries[] = new AastraIPPhoneTextMenuEntry('['.Aastra_get_label('Next Page',$this->_language).']', $this->_scrollHandlerReference.'&listPage='.($page+1), NULL, NULL, NULL, NULL,NULL);
		
		# On phones with softkeys:Add Next/Previous Softkeys 
		if(Aastra_is_softkeys_supported()) 
			{
			if ($page < $last) 
				{
				# Add Next key
				if (Aastra_is_icons_supported() && !empty($this->_nextKeyIcon)) $this->addSoftkey($this->_nextKeyPosition, Aastra_get_label('Next',$this->_language), $this->_scrollHandlerReference.'&listPage='.($page+1), $this->_nextKeyIconIndex);
				else $this->addSoftkey($this->_nextKeyPosition, Aastra_get_label('Next',$this->_language), $this->_scrollHandlerReference.'&listPage='.($page+1));					
				}
			if ($page > 1 && $last > 1) 
				{
				# Add Previous key
				if (Aastra_is_icons_supported() && !empty($this->_previousKeyIcon)) $this->addSoftkey($this->_previousKeyPosition, Aastra_get_label('Previous',$this->_language), $this->_scrollHandlerReference.'&listPage='.($page-1), $this->_previousKeyIconIndex);
				else $this->addSoftkey($this->_previousKeyPosition, Aastra_get_label('Previous',$this->_language), $this->_scrollHandlerReference.'&listPage='.($page-1));					
				}
			}
		
		# Add page info to title
		if ($last > 1) 
			{
			# For phones with big screen
			if (Aastra_is_softkeys_supported()) $this->_title = $this->_title.' ('.Aastra_get_label('Pg.',$this->_language).' '.$page.'/'.$last.')';
			# For 3 line phones
			else $this->_title = $this->_title.' ('.$page.'/'.$last.')';
			}
	}
	
	function _calculateMaxLines() 
	{
		$this->_maxLines = Aastra_max_items_textmenu();
		if(!Aastra_is_softkeys_supported()) $this->_maxLines = $this->_maxLines - 2;
	}
	
	function _setupIcons() 
	{
		if (Aastra_is_icons_supported()) 
			{
			$this->addIcon($this->_nextKeyIconIndex, $this->_nextKeyIcon);
			$this->addIcon($this->_previousKeyIconIndex, $this->_previousKeyIcon);
			if(Aastra_phone_type()==5) 
				{
				# 6739i
				$this->addIcon($this->_backKeyIconIndex,'Icon:ArrowLeft');
				$this->addIcon($this->_selectKeyIconIndex,'Icon:Information');
				$this->addIcon($this->_exitKeyIconIndex,'Icon:CircleRed');
				}
			}
	}
	
	function _setupSoftKeys() 
	{
		if (Aastra_is_softkeys_supported()) 
			{
			if(Aastra_phone_type()!=5)
				{
				# non 6739i
				$this->addSoftkey($this->_selectKeyPosition, Aastra_get_label($this->_selectKeyLabel,$this->_language), 'SoftKey:Select');
				if (!$this->_exitKeyDisabled) $this->addSoftkey($this->_exitKeyPosition, Aastra_get_label('Exit',$this->_language), 'SoftKey:Exit');
				if (!empty($this->_backCancelURL)) $this->addSoftkey($this->_backKeyPosition, Aastra_get_label('Back',$this->_language), $this->_backCancelURL);
				}
			else 
				{
				# 6739i SoftKey:Select not yet supported
				#$this->addSoftkey($this->_selectKeyPosition, Aastra_get_label($this->_selectKeyLabel,$this->_language), 'SoftKey:Select',$this->_selectKeyIconIndex);
				if (!$this->_exitKeyDisabled) $this->addSoftkey($this->_exitKeyPosition, Aastra_get_label('Exit',$this->_language), 'SoftKey:Exit',$this->_exitKeyIconIndex);
				if (!empty($this->_backCancelURL)) $this->addSoftkey($this->_backKeyPosition, Aastra_get_label('Back',$this->_language), $this->_backCancelURL,$this->_backKeyIconIndex);
				}
			}
	}
	
	function _generateCookie() 
	{
		return md5(time().rand());
	}

	function verifyCookie($cookie) 
	{
		return ($this->_cookie == $cookie); 
	}
	
	function setBackURI($URI) 
	{
		$this->_backCancelURL = $URI;
	}
	
	function setBackKeyPosition($position) 
	{
		$this->_backKeyPosition = $position;
	}
	
	function setNextKeyPosition($position) 
	{
		$this->_nextKeyPosition = $position;
	}
	
	function setPreviousKeyPosition($position) 
	{
		$this->_previousKeyPosition = $position;
	}
	
	function setExitKeyPosition($position) 
	{
		$this->_exitKeyPosition = $position;
	}
	
	function setSelectKeyPosition($position) 
	{
		$this->_selectKeyPosition = $position;
	}
	
	function setNextKeyIcon($icon=NULL) 
	{
		$this->_nextKeyIcon = $icon;
	}
	
	function setPreviousKeyIcon($icon=NULL) 
	{
		$this->_previousKeyIcon = $icon;
	}	
	
	function disableExitKey() 
	{
		$this->_exitKeyDisabled = 1;
	}	
	
	function setSelectKeyLabel($label) 
	{
		$this->_selectKeyLabel = $label;
	}		
		
	function setCancelAction($cancelAction) 	
	{
		$this->_backCancelURL = $cancelAction;
	}
	
	function natsortByName() 
	{
		if (empty($this->_list)) return;
		$tmpary = array();
		foreach ($this->_list as $id => $entry) $tmpary[$id] = $entry['name'];
		natsort($tmpary);
		foreach ($tmpary as $key => $value) $new[] = $this->_list[$key];
		$this->_list = $new;
	}
}

?>
