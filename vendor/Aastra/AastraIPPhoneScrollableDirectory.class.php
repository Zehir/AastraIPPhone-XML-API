<?php
########################################################################################################
# Aastra XML API Classes - AastraIPPhoneScrollableDirectory
#
# Aastra SIP Phones 1.4.2 or better
#
# Copyright 2009-2011 Aastra Telecom Ltd
#
# Supported Aastra Phones
#   All IP phones except Aastra9112i and Aastra9133i
#
# AastraIPPhoneScrollableDirectory object.
#
# Public methods
#	setDialKeyPosition(position) Set position of Dial Softkey in list view. Default is 2.
#	setNameDisplayFormat(format) Set name display format. 0="Firstname Lastname", 1="Lastname, Firstname"
#	natsortByLastname() Sort by lastname (same as natsortByName in case firstname is not provided)
#	natsortByFirstname() Sort by firstname (same as natsortByName in case firstname is not provided)
#		
# Overwritten methods from AastraIPPhoneScrollableTextMenu
#	setEntries(records) Set directory entries by 2 dim array. Inner array fields: See addEntry(record)
#	addEntry(record) Add directory entry
#		Array fields: (name is mandatory, rest optional)
#			name: Lastname or name
#			firstname: Firstname (optional)
#			title: Title
#			department: Department
#			company: Company
#			icon: Icon
#			office: Office number display format
#			officeDigits: Office number digits / extension to be dialed. Optional
#			mobile: Cell number display format
#			mobileDigits: Cell number digits / extension to be dialed. Optional
#			home: Home number display format
#			homeDigits: Home number digits / extension to be dialed. Optional 
#			office2: Alternative / 2nd office number display format
#			office2Digits: 2nd office number digits / extension to be dialed. Optional
#			speedURL: If this field is present, a "+Speed" Softkey will be shown in zoom mode. Selected number will be passed in $selection variable. 
#			Ex: speedURL=http://xmlserver/xml/speed/speed.php?action=add&name=Peter
#
# Inherited from AastraIPPhoneScrollableTextMenu
#	setEntries(entries) Set entries of the list by an 2 dim array. Inner array field names: 'name', 'url', 'selection', 'icon', 'dial'
#	verifyCookie(cookie) Verifies if the cookie of the HTTP requests matches the cookie of the saved context.
#	setBackURI(URI) Set the cancel parameter with the URI to be called on Cancel or Back Softkey (optional)
#	setBackKeyPosition(position) Set position of Back Softkey. Default is 3.
#	setNextKeyPosition(position) Set position of Back Softkey. Default is 4.
#	setPreviousKeyPosition(position) Set position of Back Softkey. Default is 5.
#	setExitKeyPosition(position) Set position of Back Softkey. Default is 6.
#	setSelectKeyPosition(position) Set position of Back Softkey. Default is 1.
#	setNextKeyIcon(icon) Set icon of Next Softkey. Default is Icon:TailArrowDown. Set NULL to disable icon.
#	setPreviousKeyIcon(icon) Set icon of Previous Softkey. Default is Icon:TailArrowUp. Set NULL to disable icon.
#	disableExitKey() Disable the Exit Softkey
#	setSelectKeyLabel(label) Set the label of the Select Softkey. Default is 'Select'. Make sure the string is in language.ini.
#	setCancelAction(uri) to set the cancel parameter with the URI to be called on Cancel or Back Softkey (optional)
#	addIcon(index,icon) to add custom icons to the object (optional)
#	output() to display the object
#      addEntry(name,url,selection,icon,dial) to add an element in the list to be displayed
#      natsortbyname() to order the list
#
#	Example:
#			$records[0]['name'] = "Smith";
#			$records[0]['firstname'] = "Lisa";
#			$records[0]['office'] = "+1 (0) 555-123-4321";
#			$records[0]['officeDigits'] = "4321";
#			$records[0]['mobile'] = "079 555 12 34";
#			# ... add as many entries you want
#			$records[99]['name'] = "Miller";
#			$records[99]['firstname'] = "Bob";
#			$records[99]['office'] = "+1 (0) 555-123-1234";
#			$records[99]['officeDigits'] = "1234";
#			$records[99]['home'] = "044 555 22 33";
#			$records[99]['company'] = "Aastra Telecom Inc.";
#			$directory = new AastraIPPhoneScrollableDirectory();
#			$directory->setTitle('Directory');
#			$directory->setBackURI($XML_SERVER."?action=start");
#			$directory->setEntries($records):
#			$directory->output(); # Page scrolling and record zooming will be handled by AastraIPPhoneScrollableTextMenu
########################################################################################################

require_once('AastraIPPhoneScrollableTextMenu.class.php');

class AastraIPPhoneScrollableDirectory extends AastraIPPhoneScrollableTextMenu 
{
	# Variables
	var $_dialKeyPosition = 2;
	var $_selectKeyLabel = 'Details';
	var $_index = 0;
	var $_nameDisplayFormat = 0; # 0="Firstname Lastname", 1="Lastname, Firstname"
		
	# Constructor
	function AastraIPPhoneScrollableDirectory() 
	{
	# Set default style to "none"
	$this->setStyle('none');

	# Set default title
	$this->setTitle(Aastra_get_label('Directory',$this->_language));

	# Call parent constructor
	parent::AastraIPPhoneScrollableTextMenu();
	}
		
	function addEntry($record) 
	{
	# If no name or fistname provided, skip record
	if (empty($record['name']) && empty($record['firstname'])) return;
		
	# Save lastname (needed if we want to sort by lastname)
	$record['lastname'] = $record['name']; 

	# Set display name
	if ($this->_nameDisplayFormat==0) $record['name'] = trim((isset($record['firstname'])) ? $record['firstname'] . ' ' . $record['name'] : $record['name']);
	else $record['name'] = trim((isset($record['firstname'])) ? $record['name'] . ', ' . $record['firstname'] : $record['name']);
		
	# If actual digits are not provided, use display format number.
	if (empty($record['officeDigits'])) $record['officeDigits'] = $record['office'];
	if (empty($record['mobileDigits'])) $record['mobileDigits'] = $record['mobile'];
	if (empty($record['homeDigits'])) $record['homeDigits'] = $record['home'];
	if (empty($record['office2Digits'])) $record['office2Digits'] = $record['office2'];

	# Remove non-numeric digits
	$record['officeDigits'] = preg_replace('/[^0-9]/','',$record['officeDigits']);
	$record['mobileDigits'] = preg_replace('/[^0-9]/','',$record['mobileDigits']);
	$record['homeDigits'] = preg_replace('/[^0-9]/','',$record['homeDigits']);
	$record['office2Digits'] = preg_replace('/[^0-9]/','',$record['office2Digits']);
	
	# Set instant dial number (to be dialed when hook is lifted in list view / when dial key is pressed in list view)
	if (!empty($record['officeDigits'])) $record['dial'] = $record['officeDigits'];
	else if (!empty($record['office2Digits'])) $record['dial'] = $record['office2Digits'];
	else if (!empty($record['mobileDigits'])) $record['dial'] = $record['mobileDigits'];
	else if (!empty($record['homeDigits'])) $record['dial'] = $record['homeDigits'];
	$record['url'] = $this->_scrollHandlerReference.'&zoomIndex='.$this->_index;
		
	# We need to save the index as a field as the order of $this->_list can change after sorting
	$record['index'] = $this->_index;
	
	# Add record to text menu list entries
	$this->_list[$this->_index] = $record;
	$this->_index++;
	}
	
	function setEntries($records) 
	{
	# Empty list?
	if (empty($records)) return;

	# Clear list
	$this->_list = array();
	foreach ($records as $record) $this->addEntry($record);
	}
	
	function zoom($index, $recentPage, $recentSelection) 
	{
	# Find record matching the given index
	foreach ($this->_list as $record) 
		{
		if ($record['index']==$index) 
			{
			$myrecord = $record;
			break;
			}
		}

	# Textmenu for the zoom
	$menu = new AastraIPPhoneTextMenu();			
	$menu->setDestroyOnExit();	
	if (Aastra_is_style_textmenu_supported()) $menu->setStyle('none');			
	if (Aastra_is_wrap_title_supported())$menu->setTitleWrap();			
	if (Aastra_is_textmenu_wrapitem_supported()) $menu->setWrapList();	
	$menu->setTitle($myrecord['name']);

	# Default Index
	$defaultIndex = 1;
	if(!empty($myrecord['title'])) 
		{
		$menu->addEntry($myrecord['title'], NULL, NULL);
		$defaultIndex++;
		}
	if(!empty($myrecord['department'])) 
		{
		$menu->addEntry($myrecord['department'], NULL, NULL);
		$defaultIndex++;
		}	
	if(!empty($myrecord['company'])) 
		{
		$menu->addEntry($myrecord['company'], NULL, NULL);
		$defaultIndex++;
		}		
	$menu->setDefaultIndex($defaultIndex);
		
	# If Dial2 softkey is supported, add 'Dial:' Prefix to URL (so number can be dialed by pressing right navigation key)
	if (!Aastra_test_phone_version('2.0.1.',1)) $URLprefix = 'Dial:'; else $URLprefix = '';

	# Office Number		
	if(!empty($myrecord['office'])) 
		{
		if (Aastra_is_icons_supported()) 
			{
			$iconIndex = 10; 
			$prompt='';
			}
		else 
			{
			$iconIndex = NULL;
			$prompt=Aastra_get_label('(W)',$this->_language).' ';
			}
		if (!Aastra_test_phone_version('2.0.1.',1)) $menu->addEntry($prompt.$myrecord['office'], $URLprefix.$myrecord['officeDigits'], $myrecord['officeDigits'], $iconIndex, $myrecord['officeDigits']);
		else $menu->addEntry($prompt.$myrecord['office'], $URLprefix.$myrecord['officeDigits'], $myrecord['officeDigits']);
		}	
	
	# Office 2 number
	if(!empty($myrecord['office2'])) 
		{
		if (Aastra_is_icons_supported()) 
			{
			$iconIndex = 10;
			$prompt='';
			}
		else 
			{
			$iconIndex = NULL;
			$prompt=Aastra_get_label('(W)',$this->_language).' ';
			}
		if (!Aastra_test_phone_version('2.0.1.',1)) $menu->addEntry($prompt.$myrecord['office2'], $URLprefix.$myrecord['office2Digits'], $myrecord['officeDigits'], $iconIndex, $myrecord['office2Digits']);
		else $menu->addEntry($prompt.$myrecord['office2'], $URLprefix.$myrecord['office2Digits'], $myrecord['officeDigits']);
		}

	# Mobile number		
	if(!empty($myrecord['mobile'])) 
		{
		if (Aastra_is_icons_supported()) 
			{
			$iconIndex = 11;
			$prompt='';
			}
		else
			{
			$iconIndex = NULL;
			$prompt=Aastra_get_label('(C)',$this->_language).' ';
			}
		if (!Aastra_test_phone_version('2.0.1.',1)) $menu->addEntry($prompt.$myrecord['mobile'], $URLprefix.$myrecord['mobileDigits'], $myrecord['officeDigits'], $iconIndex, $myrecord['mobileDigits']);
		else $menu->addEntry($prompt.$myrecord['mobile'], $URLprefix.$myrecord['mobileDigits'], $myrecord['officeDigits']);
		}

	# Home number	
	if(!empty($myrecord['home'])) 
		{
		if (Aastra_is_icons_supported()) 
			{
			$iconIndex = 12; 
			$prompt='';
			}
		else 
			{
			$iconIndex = NULL;
			$prompt=Aastra_get_label('(H)',$this->_language).' ';
			}
		if (!Aastra_test_phone_version('2.0.1.',1)) $menu->addEntry($prompt.$myrecord['home'], $URLprefix.$myrecord['homeDigits'], $myrecord['officeDigits'], $iconIndex, $myrecord['homeDigits']);
		else $menu->addEntry($prompt.$myrecord['home'], $URLprefix.$myrecord['homeDigits'], $myrecord['officeDigits']);
		}	
	
	# Softkeys	
	if (Aastra_is_softkeys_supported()) 
		{
		if (Aastra_number_softkeys_supported()!=10) 
			{
			# Regular phone with 6 softkeys
			if (!Aastra_test_phone_version('2.0.1.',1)) $dialKeyType = 'SoftKey:Dial2'; 
			else $dialKeyType = 'SoftKey:Dial';
			$menu->addSoftkey(1, Aastra_get_label('Dial',$this->_language), $dialKeyType);
			$menu->addSoftkey(3, Aastra_get_label('Back',$this->_language), $this->_scrollHandlerReference.'&listPage='.$recentPage.'&recentSelection='.$recentSelection);
			$menu->addSoftkey(6, Aastra_get_label('Exit',$this->_language), 'SoftKey:Exit');
			
			# Check if speed dial URL is set
			if (isset($myrecord['speedURL'])) $menu->addSoftkey(4, Aastra_get_label('Add to Speed Dial',$this->_language), $myrecord['speedURL']);
			}
		else
			{
			# 6739i
			$menu->addSoftkey(9, Aastra_get_label('Back',$this->_language), $this->_scrollHandlerReference.'&listPage='.$recentPage.'&recentSelection='.$recentSelection,8);
			$menu->addSoftkey(10, Aastra_get_label('Exit',$this->_language), 'SoftKey:Exit',9);

			# Check if speed dial URL is set
			if (isset($myrecord['speedURL'])) $menu->addSoftkey(6, Aastra_get_label('+Speed',$this->_language), $myrecord['speedURL']);
			}
		}

	# Icons		
	if(Aastra_is_icons_supported()) 
		{
		if(Aastra_phone_type()!=5)
			{
			$menu->addIcon(10,Aastra_get_custom_icon('Office'));
			$menu->addIcon(11,Aastra_get_custom_icon('Cellphone'));
			$menu->addIcon(12,Aastra_get_custom_icon('Home'));
			}
		else
			{
			$menu->addIcon(8,'Icon:ArrowLeft');
			$menu->addIcon(9,'Icon:CircleRed');
			$menu->addIcon(10,'Icon:Office');
			$menu->addIcon(11,'Icon:CellPhone');
			$menu->addIcon(12,'Icon:Home');
			}
		}			
	
	# Cancel action	
	$menu->setCancelAction($this->_scrollHandlerReference.'&listPage='.$recentPage.'&recentSelection='.$recentSelection);
		
	# Display XML object
	$menu->output();
	}
	
	function _setupSoftKeys() 
	{
	# Check if softkeys and the Dial2 softkey is supported. Don't put dial key on 6739i
	if (Aastra_is_softkeys_supported() && !Aastra_test_phone_version('2.0.1.',1) && Aastra_phone_type()!=5) $this->addSoftkey($this->_dialKeyPosition, Aastra_get_label('Dial',$this->_language), 'SoftKey:Dial2');
	parent::_setupSoftKeys();
	}
	
	function setDialKeyPosition($position) 
	{
	$this->_dialKeyPosition = $position;
	}	
	
	function setNameDisplayFormat($format) 
	{
	$this->_nameDisplayFormat = $format;
	}
	
	function natsortByLastname() 
	{
	if (empty($this->_list)) return;
	$tmpary = array();
	foreach ($this->_list as $id => $entry) $tmpary[$id] = $entry['lastname'];
	natsort($tmpary);
	foreach ($tmpary as $key => $valude) $new[] = $this->_list[$key];
	$this->_list = $new;
	}
	
	function natsortByFirstname() 
	{
	if (empty($this->_list)) return;
	$tmpary = array();
	foreach ($this->_list as $id => $entry) $tmpary[$id] = $entry['firstname'];
	natsort($tmpary);
	foreach ($tmpary as $key => $valude) $new[] = $this->_list[$key];
	$this->_list = $new;
	}
}
?>