<?php

###########################################################################
# Sample php applications using the Aastra XML API Classes
# Aastra 6739i Firmware 3.0.1 or better
# Copyright Aastra Telecom 2005-2010
#
###########################################################################

###########################################################################
# Includes
###########################################################################
require_once('AastraIPPhone.php');

###########################################################################
# Main Code
###########################################################################

# Test firmware version
#Aastra_test_phone_version('3.0.1',0);

# Retrieve type parameter
$type=$_GET['type'];

# Global parameters
$XML_SERVER = "http://".$_SERVER['SERVER_ADDR'].$_SERVER['SCRIPT_NAME'];

# Get UA information
$header=Aastra_decode_HTTP_header();

# Display the object
switch($type)
	{
	case '':
		require_once('AastraIPPhoneTextMenu.class.php');
		$menu = new AastraIPPhoneTextMenu();
		$menu->setTitle('Sample Applications');
		$menu->setDestroyOnExit();
		$menu->addEntry('Configuration', $XML_SERVER.'?type=configuration');
		$menu->addEntry('TextMenu1', $XML_SERVER.'?type=textmenu1');
		$menu->addEntry('TextMenu2', $XML_SERVER.'?type=textmenu2');
		$menu->addEntry('TextScreen', $XML_SERVER.'?type=textscreen');
		$menu->addEntry('InputScreen1', $XML_SERVER.'?type=inputscreen1');
		$menu->addEntry('InputScreen2', $XML_SERVER.'?type=inputscreen2');
		$menu->addEntry('InputScreen3', $XML_SERVER.'?type=inputscreen3');
		$menu->addEntry('Execute', $XML_SERVER.'?type=execute');
		$menu->addEntry('FormattedTextScreen', $XML_SERVER.'?type=formattedtextscreen');
		$menu->addEntry('Status1', $XML_SERVER.'?type=status1');
		$menu->addEntry('Status2', $XML_SERVER.'?type=status2');
		$menu->natsortByName();
		$menu->output();
		break;

	case 'textmenu1':
		require_once('AastraIPPhoneTextMenu.class.php');
		$menu = new AastraIPPhoneTextMenu();
		$menu->setTitle('Title, which is a pretty long title');
 		$menu->setTitleWrap();
		$menu->setDestroyOnExit();
		$menu->setDeFaultIndex('3');
		$menu->addEntry('Choice 2', 'http://myserver.com/script.php?choice=2', 'Value=2','','12345');
		$menu->addEntry('Choice 1', 'http://myserver.com/script.php?choice=1', 'Value=1');
		$menu->addEntry('Choice 3', 'http://myserver.com/script.php?choice=3', 'Value=3');
		$menu->addEntry('Choice 4', 'http://myserver.com/script.php?choice=4', 'Value=4');
		$menu->addEntry('Choice 5', 'http://myserver.com/script.php?choice=5', 'Value=5');
		$menu->addEntry('Choice 6', 'http://myserver.com/script.php?choice=6', 'Value=6');
		$menu->addEntry('Choice 7', 'http://myserver.com/script.php?choice=7', 'Value=7');
		$menu->natsortByName();
		$menu->addSoftkey('1', 'My Select', 'http://myserver.com/script.php?action=1');
		$menu->addSoftkey('10', 'Done', 'SoftKey:Exit');
		$menu->output();
	break;

	case 'textmenu2':
		require_once('AastraIPPhoneTextMenu.class.php');
		$menu = new AastraIPPhoneTextMenu();
		$menu->setTitle('Title');
		$menu->setDestroyOnExit();
		$menu->setDefaultIndex('2');
		$menu->addEntry('Choice 2', 'http://myserver.com/script.php?choice=2', 'Value=2','1');
		$menu->addEntry('Choice 1', 'http://myserver.com/script.php?choice=1', 'Value=1','2');
		$menu->addEntry('Choice 3', 'http://myserver.com/script.php?choice=3', 'Value=3','3');
		$menu->natsortByName();
		$menu->addSoftkey('1', 'My Select', 'http://myserver.com/script.php?action=1');
		$menu->addSoftkey('9', 'Back', $XML_SERVER);
		$menu->addSoftkey('10', 'Exit', 'SoftKey:Exit');
		$menu->output();
	break;

	case 'textscreen':
		require_once('AastraIPPhoneTextScreen.class.php');
		$text = new AastraIPPhoneTextScreen();
		$text->setTitle('Title');
		$text->setText('The screen object can be implemented similar to the firmware info screen.');
 		$text->setDestroyOnExit();
		$text->addSoftkey('1', 'Mail', 'http://myserver.com/script.php?action=1');
		$text->addSoftkey('6', 'Exit', 'SoftKey:Exit');
		$text->output();
	break;
	
	case 'inputscreen1':
		require_once('AastraIPPhoneInputScreen.class.php');
		$input = new AastraIPPhoneInputScreen();
		$input->setTitle('Title');
		$input->setPrompt('Enter your password');
		$input->setParameter('param');
		$input->setType('string');
		$input->setURL('http://myserver.com/script.php?test=1');
		$input->setPassword();
		$input->setDestroyOnExit();
		$input->setDefault('');
		$input->setCancelAction($XML_SERVER);
		$input->output();
	break;

	case 'inputscreen2':
		require_once('AastraIPPhoneInputScreen.class.php');
		$input = new AastraIPPhoneInputScreen();
		$input->setTitle('Title');
		$input->setPrompt('Enter the date');
		$input->setParameter('param');
		$input->setType('dateUS');
		$input->setURL('http://myserver.com/script.php');
		$input->setDestroyOnExit();
		$input->setCancelAction($XML_SERVER);
		$input->output();
	break;

	case 'inputscreen3':
		require_once('AastraIPPhoneInputScreen.class.php');
		$input = new AastraIPPhoneInputScreen();
		$input->setTitle('Restricted application');
		$input->setDisplayMode('condensed');
		$input->setURL($XML_SERVER);
		$input->setDestroyOnExit();
		$input->addField('empty');
		$input->addField('string');
		$input->setFieldSelection('1');
		$input->setFieldPrompt('Username:');
		$input->setFieldParameter('user');
		$input->setFieldSelection('1');
		$input->addField('number');
		$input->setFieldPassword('yes');
		$input->setFieldPrompt('Password:');
		$input->setFieldParameter('password');
		$input->setFieldSelection('2');
		$input->addSoftkey('10', 'Exit', 'SoftKey:Exit');
		$input->output();
	break;

	case 'execute':
		require_once('AastraIPPhoneExecute.class.php');
		$execute = new AastraIPPhoneExecute();
		$execute->setTriggerDestroyOnExit();
		$execute->addEntry('Led: softkey1=on');
		$execute->addEntry('Dial:7001','no');
		$execute->output();
	break;

	case 'configuration':
		require_once('AastraIPPhoneConfiguration.class.php');
     		$configuration = new AastraIPPhoneConfiguration();
		$configuration->addEntry('softkey1 label','Test');
		$configuration->addEntry('softkey1 type','xml');
		$configuration->setTriggerDestroyOnExit();
		$configuration->output();
	break;

	case 'formattedtextscreen':
		require_once('AastraIPPhoneFormattedTextScreen.class.php');
		$ftext = new AastraIPPhoneFormattedTextScreen();
		$ftext->setDestroyOnExit();
		$ftext->addLine('Formatted Screen','double','center','red');
		$ftext->setScrollStart();
		$ftext->addLine('Scrolled text1');
		$ftext->addLine('Scrolled text2');
		$ftext->addLine('Scrolled text3');
		$ftext->addLine('Scrolled text4');
		$ftext->addLine('Scrolled text5');
		$ftext->setScrollEnd();
		$ftext->addLine('Footer',NULL,'center');
		$ftext->addSoftkey('10', 'Exit', 'SoftKey:Exit');
		$ftext->output();   
	break;

	case 'status1':
		require_once('AastraIPPhoneStatus.class.php');
		$status = new AastraIPPhoneStatus();
		$status->setTriggerDestroyOnExit();
		$status->setSession('Session');
		$status->addEntry('1','Message 1');
		$status->addEntry('2','Message 2');
		$status->output();
		break;

	case 'status2':
		require_once('AastraIPPhoneStatus.class.php');
		$status = new AastraIPPhoneStatus();
		$status->setSession('Session');
		$status->setTriggerDestroyOnExit();
		$status->addEntry('1','');
		$status->addEntry('2','');
		$status->output();
		break;
	}
?>
