<?php
########################################################################################################
# Aastra XML API Classes - AastraIPPhoneImageMenu
# Copyright Mitel Networks 2005-2015
#
# AastraIPPhoneImageMenu object.
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
#     setAllowAnswer() to set the allowAnswer tag to 'yes' (optional, only for non softkey phones)
#     setAllowDrop() to set the allowDrop tag to 'yes' (optional, only for non softkey phones)
#     setAllowXfer() to set the allowXfer tag to 'yes' (optional, only for non softkey phones)
#     setAllowConf() to set the allowConf tag to 'yes' (optional, only for non softkey phones)
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
#     setImage(image)to define the image to be displayed
#          @image		string
#     setGDImage(GDImage) to use a GDImage for display, the size is forced to 40x144
#          @GDImage		GDImage
#     setAlignment(vertical,horizontal) to define image alignment
#          @vertical		string, "top", "middle", "bottom"
#          @horizontal	string, "left", "middle", "right"
#     setSize(height,width) to define image size
#          @height		integer (pixels)
#          @width		integer (pixels)
#     setURIBase(uriBase) to define the base URI for the selections
#          @uriBase		string
#     addURI(key,uri) to add a selection key with its URI
#          @key		string (1-9, * and #)
#          @uri		string
#     setMode(mode) to define the image mode to be displayed (normal,extended,fullscreen) (optional, 6739i only)
#          @mode		string enum normal,extended,fullscreen
#     setDoneAction(uri) to set the URI to be called when the user selects the default "Done" key (optional)
#          @uri		string
#     setImageAction(uri) to set the imageAction parameter with the URI to be called when user presses on the displayed image (optional, 6739i only)
#          @uri		string
#
# Example
#     require_once('AastraIPPhoneImageMenu.class.php');
#     $imagem = new AastraIPPhoneImageMenu();
#     $imagem->setDestroyOnExit();
#     $imagem->setSize(40,144);
#     $imagem->setImage('fffffffc02fffffffee4ffffbfffc05fffe7ff7a7ffffffffeffeebd7fffffea6bcfffffe796f3feff6fa289f0a86f4866fa20df42414595dd0134f8037ed1637f0e2522b2dd003b6eb936f05fffbd4f4107bba6eb0080e93715000010b754001281271408c640252081b1b22500013c5c66201368004e04467520dc11067152b82094d418e100247205805494780105002601530020131400020a05c91088b002b08c21c0000c200000001fe800000000000000001c000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000020041000004008300000ff08500000000c900000000710000000000000001401400000140140000014014000001401400000140140000000000000007c0ff00000c30880000081088000008108800000c30700000062000000000003f000001e02000000330200000021000000003301e000001e0330000000021000003f033000002001e0000020000000000001e000c03fc33003c013021007c02101201f00330ff03f001e000039000003e039001e00103f003300101f8021003007c03303f003c01e000000c00001e001c03f033007802002100f002002103e000001203c401702003cc0290ff039c02902101fc02b000007c03f01a003c020039018c0ff02d03c402102703c400001203ec01e000026402b0000264029000026c029000027c01a0000338000000033800000003100000000300000000030003f00003fc03000003fc02000003fc020000030001f0000300000000030001e000030002b000030002900003fc02900003fc01a00003f00000000310030000031c01e000031f003000033f81e00003f383000001e081e000008c003000003c01e00000fc03000001f000000003d001a0000390039000039002d00003f002700000f8012000007c000000001c0000000004000000000000000000000000000');
#     $imagem->addURI('1','http://myserver.com?choice=1');
#     $imagem->addURI('2','http://myserver.com?Choice=2');
#     $imagem->addSoftkey('1', 'Label', 'http://myserver.com/script.php?action=1','1');
#     $imagem->addSoftkey('6', 'Exit', 'SoftKey:Exit');
#     $imagem->addIcon('1', 'Icon:Envelope');
#     $imagem->addIcon('2', 'FFFF0000FFFF0000');
#     $imagem->output();   
#
########################################################################################################

require_once('AastraIPPhone.class.php');
require_once('AastraIPPhoneImageMenuEntry.class.php');

class AastraIPPhoneImageMenu extends AastraIPPhone {
	var $_image;
	var $_verticalAlign=NULL;
	var $_horizontalAlign=NULL;
	var $_height=NULL;
	var $_width=NULL;
	var $_uriBase=NULL;
	var $_mode=NULL;
	var $_imageAction='';
	var $_doneAction='';

	function setImage($image)
	{
		$this->_image = $image;
	}

	function setAlignment($vertical=NULL,$horizontal=NULL)
	{
		$this->_verticalAlign = $vertical;
		$this->_horizontalAlign = $horizontal;
	}

	function setSize($height,$width)
	{
		$this->_height = $height;
		$this->_width = $width;
	}

	function setURIBase($uriBase)
	{
		$this->_uriBase = $uriBase;
	}

	function addURI($key, $uri)
	{
		$this->_entries[] = new AastraIPPhoneImageMenuEntry($key, $uri);
	}

	function setGDImage($GDImage) 
	{
		$img = $GDImage->getGDImage();
		$byte = 0;
		$i = 0;
		$imageHexString = "";
		for ($x=0; $x < 144; $x++) 
			{
			for ($y=0; $y < 40; $y++) 
				{
				$rgb = imagecolorat($img, $x, $y);
				if ($rgb > 0) $byte = $byte + pow(2,(7-($i%8)));
				if ($i%8 == 7) 
					{
					$byteHex = dechex($byte);
					if (strlen($byteHex) == 1) $byteHex = "0".$byteHex; 
					$imageHexString = $imageHexString . $byteHex;
					$byte=0;
					}
				$i++;
				}
			}
		$this->setImage($imageHexString);
		$this->setSize(40,144);
	}

	function setImageAction($action)
	{
		$this->_imageAction = $action;
	}

	function setDoneAction($uri)
	{
		$this->_doneAction = $uri;
	}

	function setMode($mode)
	{
		$this->_mode = $mode;
	}

	function render()
	{
		# Beginning of roor tag
		$out = "<AastraIPPhoneImageMenu";

		# DestroyOnExit
		if ($this->_destroyOnExit=='yes') $out .= " destroyOnExit=\"yes\"";

		# CancelAction
		if($this->_cancelAction != "")
			{ 
			$cancelAction = $this->escape($this->_cancelAction);
			$out .= " cancelAction=\"{$cancelAction}\"";
			}

		# DoneAction
		if($this->_doneAction != "")
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
		if ($this->_allowAnswer == 'yes') $out .= " allowAnswer=\"yes\"";

		# AllowDrop
		if ($this->_allowDrop == 'yes') $out .= " allowDrop=\"yes\"";

		# AllowXfer
		if ($this->_allowXfer == 'yes') $out .= " allowXfer=\"yes\"";

		# AllowConf
		if ($this->_allowConf == 'yes') $out .= " allowConf=\"yes\"";

		# Timeout
		if($this->_timeout!=0) $out .= " Timeout=\"{$this->_timeout}\"";

		# Background color
		if ($this->_background_color!='') $out .= " bgColor=\"{$this->_background_color}\"";

		# ImageAction
		if($this->_imageAction != '')
			{ 
			$imageAction = $this->escape($this->_imageAction);
			$out .= " imageAction=\"{$imageAction}\"";
			}

		# Mode
		if($this->_mode != '')
			{ 
			$mode = $this->escape($this->_mode);
			$out .= " mode=\"{$mode}\"";
			}

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

		# Image tag
		$out .= "<Image";

		# VerticalAlign
		if($this->_verticalAlign!=NULL) $out .= " verticalAlign=\"{$this->_verticalAlign}\"";
	
		# HorizontalAlign
		if($this->_horizontalAlign!=NULL) $out .= " horizontalAlign=\"{$this->_horizontalAlign}\"";

		# Height
		if($this->_height!=NULL) $out .= " height=\"{$this->_height}\"";

		# Width
		if($this->_width!=NULL) $out .= " width=\"{$this->_width}\"";

		# Image
		$out .= ">{$this->_image}</Image>\n";

		# URI List 
		$out .= "<URIList";
		$uriBase = $this->escape($this->_uriBase);
		if($uriBase!=NULL) $out .= " base=\"{$uriBase}\"";
		$out .= ">\n";
		if (isset($this->_entries) && is_array($this->_entries)) 
			{
			foreach ($this->_entries as $entry) $out .= $entry->render();
			}
		$out .= "</URIList>\n";

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

		# End of root tag
		$out .= "</AastraIPPhoneImageMenu>\n";

		# Return XML object
		return($out);
	}
}
?>
