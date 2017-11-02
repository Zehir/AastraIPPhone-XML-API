<?php
#########################################################################################################
# Aastra XML API Classes - Aastra XML API Classes - AastraIPPhoneGDImage
# Copyright Mitel Networks 2005-2015
#
# Firmware 2.0 or better
#
# AastraIPPhoneGDImage for AastraIPPhoneImageScreen and AastraIPPhoneImageScreen.
#
# php engine needs GD extensions
# ------------------------------
#
# Public methods
#
#	drawttftext(fontsize,angle,x,y,text,colorIndex,fontfile) 
#	Writes text to the image using TrueType fonts 
#	fontsize	The font size. Depending on your version of GD, this should be specified as the pixel 
#			size (GD1) or point size (GD2)
#	angle		The angle in degrees, with 0 degrees being left-to-right reading text. Higher values 
#			represent a counter-clockwise rotation. For example, a value of 90 would result in 
#			bottom-to-top reading text. 
#	x,y		The coordinates given by x and y will define the basepoint of the first character 
#			(roughly the lower-left corner of the character). 
#	colorIndex	0=White	1=Black
#	fontfile	Location and name of the ttf file to use
#	see php imagettftext() for more details
#
#	drawtext(fontsize,x,y,text,colorIndex) 
#	Writes text to the image using built-in font
#	fontsize	The font size. From 1 to 5
#	x,y		The coordinates given by x and y will define the basepoint of the first character 
#			(roughly the lower-left corner of the character). 
#	colorIndex	0=White	1=Black
#	see php imagestring() for more details
#
#	rectangle(x1,y1,x2,y2,colorIndex,filled)
#	Creates a rectangle starting at the specified coordinates.
#	x1,y1		Upper left x,y coordinate. 0,0 is the top left corner of the image. 
#	x2,y2		Bottom right x,y coordinate 
#	colorIndex	0=White	1=Black
#	filled		Boolean, optional (default if False)
#	see php imagerectangle() and imagefilledrectangle() for more details	
#
#	ellipse(cx,cy,width,height,colorIndex,filled) 
#	Draws an ellipse centered at the specified coordinates.
#	cx,cy		x-coordinate and y-coordinate of the center 
#	width		the ellipse width 
#	height		the ellipse height 
#	colorIndex	0=White	1=Black
#	filled		Boolean, optional (default if False)
#	see php imageellipse() and imagefilledellipse() for more details	
#
#	line(x1,y1,x2,y2,colorIndex) 
#	Draws a line
#	x1,y1		x,y coordinates for the first point
#	x2,y2		x,y coordinates for the second point
#	colorIndex	0=White	1=Black
#	see php imageline() for more details
#
#	setGDImage(image) 
#	Imports an externally generated GD image
#	image		GD image to import
#
#	getGDImage() 
#	Exports the current GD image
#
#	setFontPath(fontpath)
#	Set directory path for the fonts to use
#	fontpath	Directory for the ttf fonts
#	Default value
#		Windows based platform		C:\Windows\Fonts
#		Linux based platform			../fonts
#
# Example 1
#	require_once('AastraIPPhoneGDImage.class.php');
#	$PhoneImageGD = new AastraIPPhoneGDImage();
#	$time = strftime("%H:%M");
#	$PhoneImageGD->drawttftext(30, 0, 10, 39, $time, 1,'Ni7seg.ttf');	
#
# Example 2
#	require_once('AastraIPPhoneGDImage.class.php');
#	$PhoneImageGD = new AastraIPPhoneGDImage();
#	$utf8text = "&#19996;&#19997;&#19998;&#19999;&#20024;";
#	$PhoneImageGD->drawttftext(20, 0, 5, 35, $utf8text, 1,'arialuni.ttf');	
#
########################################################################################################

########################################################################################################
class AastraIPPhoneGDImage
{
	var $_img;
	var $_white;
	var $_black;
	var $_red;
	var $_green;
	var $_blue;
	var $_font;
	var $_fontpath;
	var $_color;
	
function AastraIPPhoneGDImage($width=144,$height=40,$color=False) 
{
	# create the actual image
	$this->_color=$color;
	if($color) {
		$this->_img=imagecreatetruecolor($width, $height);
	} else {
		$this->_img=imagecreate($width, $height);
	}
		
	# define black and white
	$this->_white = imagecolorallocate($this->_img, 255, 255, 255);
	$this->_black = imagecolorallocate($this->_img, 0, 0, 0);
	if($color) {
	$this->_red = imagecolorallocate($this->_img, 255, 0, 0);
	$this->_green = imagecolorallocate($this->_img, 0, 255, 0);
	$this->_blue = imagecolorallocate($this->_img, 0, 0, 255);
	}
		
	# Black and White only so disable anti-aliasing
	if(!$color) {
		$this->_black = $this->_black * -1;
		$this->_white = $this->_white * -1;
	}

	# define a default font path
	$os = strtolower(PHP_OS);
	if(strpos($os, "win") === false) $this->_fontpath='../fonts';
	else $this->_fontpath='C:\Windows\Fonts';
	putenv('GDFONTPATH='.$this->_fontpath);
}

function importFromPng($file,$x,$y) 
	{
	$image=@imagecreatefrompng($file); 
	imagecopy($this->_img,$image,$x,$y,0,0,imagesx($image),imagesy($image));
	}

function setFontPath($fontpath) 
	{
	$this->_fontpath=$fontpath;
	putenv('GDFONTPATH='.$this->_fontpath);
	}


function drawttftext($size, $angle, $x, $y, $text, $colorIndex, $font) 
	{
	imagettftext($this->_img, $size, $angle, $x, $y, $this->getColor($colorIndex), $font, $text);
	}

function drawtext($size, $x, $y, $text, $colorIndex) 
	{
	imagestring($this->_img, $size, $x, $y, $text, $this->getColor($colorIndex));
	}

function setGDImage($image) 
	{
	$this->_img=$image;
	}

function getGDImage() 
	{
	return $this->_img;
	}

function savePNGImage($filename) 
	{
	imagepng($this->_img,$filename);
	}

function rectangle($x1, $y1, $x2, $y2, $colorIndex, $filled=False) 
	{
	if($filled) 	imagefilledrectangle($this->_img, $x1, $y1, $x2, $y2, $this->getColor($colorIndex));
	else imagerectangle($this->_img, $x1, $y1, $x2, $y2, $this->getColor($colorIndex));
	}

function ellipse($cx, $cy, $width, $height, $colorIndex, $filled=False) 
	{
	if($filled) imagefilledellipse($this->_img, $cx, $cy, $width, $height, $this->getColor($colorIndex));
	else imageellipse($this->_img, $cx, $cy, $width, $height, $this->getColor($colorIndex));
	}

function line($x1, $y1, $x2, $y2, $colorIndex, $dashed=False) 
	{
	if(!$dashed) imageline($this->_img, $x1, $y1, $x2, $y2, $this->getColor($colorIndex));
	else 
		{
		$style = array($this->_black, $this->_white);
		imagesetstyle($this->_img, $style);
		imageline($this->_img, $x1, $y1, $x2, $y2, IMG_COLOR_STYLED);
		}
	}

function getColor($index) 
	{
	if ($index == 0) return $this->_white; 
	else if ($index == 1) return $this->_black;
	else if ($index == 2) return $this->_red;
	else if ($index == 3) return $this->_green;
	else if ($index == 4) return $this->_blue;
	}
}
?>