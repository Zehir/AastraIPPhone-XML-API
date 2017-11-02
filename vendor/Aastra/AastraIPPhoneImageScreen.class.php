<?php
########################################################################################################
# Aastra XML API Classes - AastraIPPhoneImageScreen
# Copyright Mitel Networks 2005-2015
#
# AastraIPPhoneImageScreen object.
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
#     setImage(image,scaling) to define the image to be displayed
#          @image		string
#					 @scaling	scaling indicator "yes"/"no" optional
#     setGDImage(GDImage) to use a GDImage for display, the size is forced to 40x144
#          @GDImage		GDImage
#     setSPImage(SPImage) to use a SPImage for display, the size is forced to 40x144
#          @SPImage		SPImage
#     setAlignment(vertical,horizontal) to define image alignment
#          @vertical		string, "top", "middle", "bottom"
#          @horizontal	string, "left", "middle", "right"
#			setScaling() to indicate if image must be scaled
#     setSize(height,width) to define image size
#          @height		integer (pixels)
#          @width		integer (pixels)
#     setAllowDTMF() to allow DTMF passthrough on the object
#     setScrollUp(uri) to set the URI to be called when the user presses the Up arrow (optional)
#          @uri		string
#     setScrollDown(uri) to set the URI to be called when the user presses the Down arrow (optional)
#          @uri		string
#     setScrollLeft(uri) to set the URI to be called when the user presses the Left arrow (optional)
#          @uri		string
#     setScrollRight(uri) to set the URI to be called when the user presses the Right arrow (optional)
#          @uri		string
#     setMode(mode) to define the image mode to be displayed (normal,extended,fullscreen) (optional, 6739i only)
#          @mode		string enum normal,extended,fullscreen
#     setDoneAction(uri) to set the URI to be called when the user selects the default "Done" key (optional)
#          @uri		string
#     setImageAction(uri) to set the imageAction parameter with the URI to be called when user presses on the displayed image (optional, 6739i only)
#          @uri		string
#
# Example
#
#     Using a Pixel image
#
#     require_once('AastraIPPhoneImageScreen.class.php');
#     $images = new AastraIPPhoneImageScreen();
#     $images->setDestroyOnExit();
#     $images->setSize(40,40);
#     $images->setImage('fffffffc02fffffffee4ffffbfffc05fffe7ff7a7ffffffffeffeebd7fffffea6bcfffffe796f3feff6fa289f0a86f4866fa20df42414595dd0134f8037ed1637f0e2522b2dd003b6eb936f05fffbd4f4107bba6eb0080e93715000010b754001281271408c640252081b1b22500013c5c66201368004e04467520dc11067152b82094d418e100247205805494780105002601530020931400020ac5c91088b0f2b08c21c07d0c2006009fdfe81f80efe0107fe0fb1c3ffff8ffc3fffef8f7febffbfcf87ffbff64');
#     $images->addSoftkey('1', 'Mail', 'http://myserver.com/script.php?action=1','1');
#     $images->addSoftkey('6', 'Exit', 'SoftKey:Exit');
#     $images->addIcon('1', 'Icon:Envelope');
#     $images->output();
#
#     Using a GD image
#
#	require_once('AastraIPPhoneGDImage.class.php');
#	require_once('AastraIPPhoneImageScreen.class.php');
#	$PhoneImageGD = new AastraIPPhoneGDImage();
#	$object = new AastraIPPhoneImageScreen();
#	$time = strftime("%H:%M");
#	$PhoneImageGD->drawttftext(30, 0, 10, 39, $time, 1,'Ni7seg.ttf');	
#	$object->setGDImage($PhoneImageGD);
#	$object->output();
#
#     Using a SP image
#
#	require_once('AastraIPPhoneImageScreen.class.php');
#	$object = new AastraIPPhoneImageScreen();
#	require_once('AastraIPPhoneSPImage.class.php');
#	$SPimage=new AastraIPPhoneSPImage();
#	$SPimage->addIcon('1','286CEE6C2800');
#	$SPimage->setBitmap('answered',3,1);
#	$SPimage->setText('Jean Valjean',1,'left',3);
#	$SPimage->setText('9057604454',2,'left',3);
#	$SPimage->setText('Sep 9 10:14am',4,'left',3);
#	$SPimage->setText('Use #1# to browse',5,'center');
#	$object->setSPImage($SPimage);
#	$object->output();
#
########################################################################################################

require_once('AastraIPPhone.class.php');

class AastraIPPhoneImageScreen extends AastraIPPhone {
	var $_image;
	var $_verticalAlign=NULL;
	var $_horizontalAlign=NULL;
	var $_scaled='';
	var $_height=NULL;
	var $_width=NULL;
	var $_allowDTMF='';
	var $_scrollUp='';
	var $_scrollDown='';
	var $_scrollLeft='';
	var $_scrollRight='';
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

	function setScaling()
	{
		$this->_scaled = 'yes';
	}

	function setSize($height,$width)
	{
		$this->_height = $height;
		$this->_width = $width;
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

	function setSPImage($SPImage) 
	{
		$this->setImage($SPImage->getSPImage());
		$this->setSize(40,144);
	}

	function setAllowDTMF()
	{
		$this->_allowDTMF = 'yes';
	}

	function render()
	{
		# Beginning of root tag
		$out = "<AastraIPPhoneImageScreen";

		# DestroyOnExit
		if($this->_destroyOnExit == 'yes') $out .= " destroyOnExit=\"yes\"";

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

		# Timeout
		if($this->_timeout!=0) $out .= " Timeout=\"{$this->_timeout}\"";

		# Background color
		if ($this->_background_color!='') $out .= " bgColor=\"{$this->_background_color}\"";

		# AllowAnswer
		if ($this->_allowAnswer == 'yes') $out .= " allowAnswer=\"yes\"";

		# AllowDrop
		if ($this->_allowDrop == 'yes') $out .= " allowDrop=\"yes\"";

		# AllowXfer
		if ($this->_allowXfer == 'yes') $out .= " allowXfer=\"yes\"";

		# AllowConf
		if ($this->_allowConf == 'yes') $out .= " allowConf=\"yes\"";

		# AllowDTMF
		if($this->_allowDTMF=='yes') $out .= " allowDTMF=\"yes\"";

		# Scrolls up/down/left/right
		if($this->_scrollUp!='') $out .= " scrollUp=\"".$this->escape($this->_scrollUp)."\"";
		if($this->_scrollDown!='') $out .= " scrollDown=\"".$this->escape($this->_scrollDown)."\"";
		if($this->_scrollLeft!='') $out .= " scrollLeft=\"".$this->escape($this->_scrollLeft)."\"";
		if($this->_scrollRight!='') $out .= " scrollRight=\"".$this->escape($this->_scrollRight)."\"";

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

		# Beginning of Image tag
		$out .= "<Image";

		# VerticalAlign
		if($this->_verticalAlign!=NULL) $out .= " verticalAlign=\"{$this->_verticalAlign}\"";

		# HorizontalAlign
		if($this->_horizontalAlign!=NULL) $out .= " horizontalAlign=\"{$this->_horizontalAlign}\"";

		# Height
		if($this->_height!=NULL) $out .= " height=\"{$this->_height}\"";

		# Width
		if($this->_width!=NULL) $out .= " width=\"{$this->_width}\"";

		# Scaling
		if($this->_scaled!='') $out .= " scaled=\"{$this->_scaled}\"";

		# Image and end of image tag
		$out .= ">{$this->_image}</Image>\n";

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
		$out .= "</AastraIPPhoneImageScreen>\n";

		# Return XML object
		return $out;
	}
}
?>
