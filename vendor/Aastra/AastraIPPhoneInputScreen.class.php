<?php
########################################################################################################
# Aastra XML API Classes - AastraIPPhoneInputScreen
# Copyright Aastra Telecom 2005-2011
#
# AastraIPPhoneInputScreen object.
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
# Specific to the object - Single Input
#     setURL(url) to set the URL to called after the input
#          @url		string
#     setType(type) to set type of input, 'string' by default
#          @type		enum ('IP', 'string', 'number', 'dateUS'...)
#     setDefault(default) to set default value for the input (optional)
#          @default		string
#     setParameter(param,color,bcolor) to set the parameter name to be parsed after the input
#          @param		string 
#          @color		string, field color "red", "blue" ... (optional)
#          @bcolor		string, background color "red", "blue" ... (optional)
#     setInputLanguage(language) to set the language of the input (optional)
#          @language		enum ("English", "French"....)
#     setPassword() to set the Password parameter to 'yes', 'no' by default (optional)
#     setNotEditable() to set the editable parameter to 'no', 'yes' by default (optional)
#     setEditable() is now replaced by setNotEditable but kept for compatibility reasons (optional)
#     setPrompt(prompt,color) to set the prompt to be displayed for the input.
#          @prompt		string
#          @color		string, prompt color "red", "blue" ... (optional)
#     setDefaultFocus() to set the default focus to 'yes'
#
# Specific to the object - Multiple Inputs
#     setURL(url) to set the URL to called after the input
#          @url		string
#     setType(type) to set the default type of input 'string' by default
#          @type		enum ('IP', 'string', 'number', 'dateUS'...)
#     setDefault(default) to set default default value for the input (optional)
#          @default		string
#     setParameter(param,color) to set the default parameter name to be parsed after the input
#          @param		string 
#          @color		string, field color "red", "blue" ... (optional)
#     setPassword() to set the default Password parameter to 'yes', 'no' by default (optional)
#     setNotEditable() to set the default editable parameter to 'no', 'yes' by default (optional)
#     setEditable() is now replaced by setNotEditable but kept for compatibility reasons (optional)
#     setPrompt(prompt,color) to set the default prompt to be displayed for the input.
#          @prompt		string
#          @color		string, prompt color "red", "blue" ... (optional)
#     setDefaultIndex(index) to define the field index the object will use to start (optional)
#          @index		integer, optional, default is 1
#     setDisplayMode(display) to define the aspect of the display, normal/condensed (optional) 
#          @display		enum ("normal, "condensed"),  default is "normal".
#     setInputLanguage(language) to set the language of the input (optional)
#          @language	enum ("English", "French"....)
#     addField(type,choiceURL) to add an input field and setting its type 
#          @type		(IP, string, number, dateUS, timeUS,dateInt, timeInt, empty or choice) if the type is an empty string then the type is inherited from the main object.
#          @choiceURL   escape URL for choice input
#     setFieldType(type,choiceURL) to set input field type 
#          @type		(IP, string, number, dateUS, timeUS,dateInt, timeInt, empty or choice) if the type is an empty string then the type is inherited from the main object.
#          @choiceURL   escape URL for choice input
#     setFieldPassword(password) to set the password mode for the input field, overrides the value set by setPassword for the field
#          @password		enum ("yes", "no")
#     setFieldEditable(editable) to set the input field editable mode ('yes', no'), overrides the value set by setEditable or setNotEditable for the field
#          @editable		enum ("yes", "no")
#     setDefaultFocus() to set the default focus to 'yes'
#     setFieldParameter(parameter,color) to set the parameter name to be parsed after the global input, overrides the value set by setParameter for the field
#          @parameter	string
#          @color		string, field color "red", "blue" ... (optional)
#     setFieldPrompt(prompt,color)to set the prompt to be displayed for the input field, overrides the value set by setPrompt for the field
#          @prompt		string
#          @color		string, prompt color "red", "blue" ... (optional)
#     setFieldSelection(selection) to set the Selection tag for the field
#          @selection	string
#     setFieldDefault(default) to set default value for the input field, overrides the value set by setDefault for the field
#          @default		string
#     addFieldSoftkey(index,label,uri,icon) to add custom softkeys to the input field, overrides the softkeys set by addSoftkey.
#          @index		integer, softkey number
#          @label		string
#          @uri		string
#          @icon		integer, icon number
#
# Example 1 - Single Input
#     require_once('AastraIPPhoneInputScreen.class.php');
#     $input = new AastraIPPhoneInputScreen();
#     $input->setTitle('Title');
#     $input->setPrompt('Enter your password');
#     $input->setParameter('param');
#     $input->setType('string');
#     $input->setURL('http://myserver.com/script.php');
#     $input->setPassword();
#     $input->setDestroyOnExit();
#     $input->setDefault('Default');
#     $input->output();
#
# Example 2 - Multiple Inputs
#     require_once('AastraIPPhoneInputScreen.class.php');
#     $input = new AastraIPPhoneInputScreen();
#     $input->setTitle('Example 2');
#     $input->setDisplayMode('condensed');
#     $input->setURL('http://myserver.com/script.php');
#     $input->setDestroyOnExit();
#     $input->addSoftkey('5', 'Done', 'SoftKey:Submit');
#     $input->addField('string');
#     $input->setFieldPrompt('Username:');
#     $input->setFieldParameter('user');
#     $input->addFieldSoftkey('3', 'ABC', 'SoftKey:ChangeMode');
#     $input->addField('number');
#     $input->setFieldPassword('yes');
#     $input->setFieldPrompt('Pass:');
#     $input->setFieldParameter('passwd');
#     $input->output();
#
########################################################################################################

require_once('AastraIPPhone.class.php');
require_once('AastraIPPhoneInputScreenEntry.class.php');
require_once('AastraIPPhoneSoftkeyEntry.class.php');

class AastraIPPhoneInputScreen extends AastraIPPhone {
	var $_url;
	var $_type='string';
	var $_parameter;
	var $_prompt;
	var $_editable='';
	var $_default='';
	var $_defaultfocus='';
	var $_password='';
	var $_defaultindex='';
	var $_displaymode='';
	var $_inputlanguage='';
	var $_fieldcolor='';
	var $_promptcolor='';
	
	function setURL($url) 
	{
		$this->_url=$url;
	}
	function setType($type) 
	{
		$this->_type=$type;
	}

	function setEditable() 
	{
		$this->_editable='no';
	}

	function setNotEditable() 
	{
		$this->_editable='no';
	}

	function setDefault($default) 
	{
		$this->_default=$default;
	}

	function setDefaultFocus() 
	{
		$this->_defaultfocus='yes';
	}

	function setParameter($parameter,$color='') 
	{
		$this->_parameter=$parameter;
		$this->_fieldcolor=$color;
	}

	function setPassword() 
	{
		$this->_password='yes';
	}

	function setPrompt($prompt,$color='') 
	{
		$this->_prompt=$prompt;
		$this->_promptcolor=$color;
	}

	function setDefaultIndex($index) 
	{
		$this->_defaultindex=$index;
	}

	function setDisplayMode($display) 
	{
		$this->_displaymode=$display;
	}

	function setInputLanguage($input) 
	{
		$this->_inputlanguage=$input;
	}

	function addField($type='',$choiceURL='')
	{
		$this->_entries[] = new AastraIPPhoneInputScreenEntry($type);
		end($this->_entries);
		if($type=='choice') $this->_entries[key($this->_entries)]->_choiceURL=$choiceURL;
	}
	
	function setFieldType($type,$choiceURL='')
	{
		$this->_entries[key($this->_entries)]->_type=$type;
		if($type=='choice') $this->_entries[key($this->_entries)]->_choiceURL=$choiceURL;
	}

	function setFieldPassword($password='yes')
	{
		$this->_entries[key($this->_entries)]->_password=$password;
	}

	function setFieldEditable($editable='yes')
	{
		$this->_entries[key($this->_entries)]->_editable=$editable;
	}

	function setFieldParameter($parameter,$color='')
	{
		$this->_entries[key($this->_entries)]->_parameter=$parameter;
		$this->_entries[key($this->_entries)]->_fieldcolor=$color;
	}

	function setFieldPrompt($prompt,$color='')
	{
		$this->_entries[key($this->_entries)]->_prompt=$this->escape($prompt);
		$this->_entries[key($this->_entries)]->_promptcolor=$color;
	}

	function setFieldDefault($default)
	{
		$this->_entries[key($this->_entries)]->_default=$default;
	}

	function setFieldSelection($selection)
	{
		$this->_entries[key($this->_entries)]->_selection=$selection;
	}

	function addFieldSoftkey($index, $label, $uri, $icon=NULL)
	{
		$this->_entries[key($this->_entries)]->_softkeys[] = new AastraIPPhoneSoftkeyEntry($index, $this->escape($label), $this->escape($uri), $icon);
	}

	function render()
	{
		# Beginning of root tag
		$out = "<AastraIPPhoneInputScreen type=\"$this->_type\"";

		# Password
		if($this->_password == 'yes') $out .= " password=\"yes\"";

		# DestroyOnExit
		if($this->_destroyOnExit == 'yes') $out .= " destroyOnExit=\"yes\"";

		# CancelAction
		if($this->_cancelAction != "")
			{ 
			$cancelAction = $this->escape($this->_cancelAction);
			$out .= " cancelAction=\"{$cancelAction}\"";
			}

		# Editable
		if($this->_editable=='no') $out .= " editable=\"no\"";
	
		# Beep
		if($this->_beep=='yes') $out .= " Beep=\"yes\"";

		# DefaultIndex
		if($this->_defaultindex!='') $out .= " defaultIndex=\"".$this->_defaultindex."\"";

		# DefaultFocus
		if($this->_defaultfocus!='') $out .= " defaultFocus=\"yes\"";

		# InputLanguage
		if($this->_inputlanguage!='') $out .= " inputLanguage=\"".$this->_inputlanguage."\"";

		# Display Mode
		if($this->_displaymode!='') $out .= " displayMode=\"".$this->_displaymode."\"";

		# Lockin
		if($this->_lockin!='') {
			$out .= " LockIn=\"{$this->_lockin}\"";
   			if($this->_lockin_uri!='') $out .= " GoodbyeLockInURI=\"".$this->escape($this->_lockin_uri)."\"";
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

		# End of the root tag
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

		# Prompt
		if($this->_prompt != '') 
			{
			$prompt = $this->escape($this->_prompt);
			$out .= "<Prompt";
			if($this->_promptcolor != '') $out .= " Color=\"{$this->_promptcolor}\"";
			$out .= ">{$prompt}</Prompt>\n";
			}

		# URL
		$url = $this->escape($this->_url);
		$out .= "<URL>{$url}</URL>\n";

		# Parameter
		if($this->_parameter != '') 
			{
			$out .= "<Parameter";
			if($this->_fieldcolor != '') $out .= " Color=\"{$this->_fieldcolor}\"";
			if($this->_fieldbcolor != '') $out .= " Bcolor=\"{$this->_fieldbcolor}\"";
			$out .= ">{$this->_parameter}</Parameter>\n";
			}

		# Default
		$out .= "<Default>{$this->_default}</Default>\n";

		# Multiple input fields
		if (isset($this->_entries) && is_array($this->_entries)) 
			{
			foreach ($this->_entries as $entry) $out .= $entry->render();
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
		$out .= "</AastraIPPhoneInputScreen>\n";
		return $out;
	}
}
?>
