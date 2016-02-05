<?php
#############################################################################
# AastraIPPhoneScrollHandler
#
# Copyright 2009 Aastra Telecom Ltd
#
# Note
#		This script is a helper script of the AastraIPPhoneScrollableTextMenu 
#		and AastraIPPhoneScrollableDirectory class. It should only be called 
#		by those classes.
#
#############################################################################

#############################################################################
# PHP customization for includes and warnings
#############################################################################
$os = strtolower(PHP_OS);
if(strpos($os, "win") === false) ini_set('include_path',ini_get('include_path').':include:../include');
else ini_set('include_path',ini_get('include_path').';include;..\include');
error_reporting(E_ERROR | E_WARNING | E_PARSE);

#############################################################################
# Includes
#############################################################################
require_once('AastraCommon.php');
require_once('AastraIPPhoneTextScreen.class.php');
require_once('AastraIPPhoneScrollableTextMenu.class.php');
require_once('AastraIPPhoneScrollableDirectory.class.php');

$cookie = Aastra_getvar_safe('listCookie');
$page = Aastra_getvar_safe('listPage');
$zoomIndex = Aastra_getvar_safe('zoomIndex');
$recentSelection = Aastra_getvar_safe('recentSelection');
$recentPage = Aastra_getvar_safe('recentPage');

Aastra_trace_call('LDAP directory',$_SERVER['REQUEST_URI']);

# Get Language and HTTP header
$language = Aastra_get_language();
$header = Aastra_decode_HTTP_header();

# Load user context
$menu = Aastra_get_user_context($header['mac'],'scrollableTextMenuData');

if (!is_object($menu)) {
	# If not an object: Something went wrong when fetching the user context. Display error and exit.
	$object = new AastraIPPhoneTextScreen();
	$object->setTitle(Aastra_get_label('Server error',$language));
	$object->setText(Aastra_get_label('Context not found. Check cache directory settings.',$language));
	$object->output();
	exit;
}

if (!$menu->verifyCookie($cookie)) {
	# If cookie does not match: Display error and exit.
	$object = new AastraIPPhoneTextScreen();
	$object->setTitle(Aastra_get_label('Server error',$language));
	$object->setText(Aastra_get_label('Session not found. Please try again.',$language));
	$object->output();
	exit;
}

if ($recentSelection != "") $menu->setDefaultIndex($recentSelection);

if ($zoomIndex != "") $menu->zoom($zoomIndex,$recentPage,$recentSelection); # This means $object is of type AastraIPPhoneScrollableDirectory
	else $menu->output($page);
exit;

?>