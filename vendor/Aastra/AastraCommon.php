<?php 
###################################################################################################
# Aastra XML API - AastraCommon.php
# Copyright Aastra Telecom 2005-2010
#
# This file includes functions specific for Aastra XML API
#
# Constants
#     AASTRA_MAXLINES			Max number of items in a TextMenu
#     AASTRA_MAXSIZE				Max size of an XML answer
#     AASTRA_MAXCONFIGURATIONS		Max number of configuration parameters in an AastraIPPhoneConfiguration
#     AASTRA_CONFIG_DIRECTORY		Location of the configuration directory
#     AASTRA_LANGUAGE_FILE			Location of the language file
#     AASTRA_PATH_CACHE			Location of the cache directory
#     AASTRA_TFTP_DIRECTORY			Location of the TFTP root directory
#
# Global variables
#     $XML_HTTP				HTTP or HTTPS header
#     $XML_SERVER				Callback sddress for the script
#     $XML_IP					IP address of the Web server
#     $XML_SERVER_PATH 			Path of the current script
#     $AA_XML_SERVER				IP address or name of the XML server
#     $AA_XMLDIRECTORY			Name of the XML directory under the Web server root
#     $AA_LANGUAGE				Language
#     $AA_ARRAY_LANGUAGE			Localization array
#     $DEBUG					Debug mode
#     $TRACE					Trace mode
#     $TEST					Test mode
#
# Public functions
#     Aastra_crypt_MAC(mac,salt)
#        Encrypt a MAC address to generate a password
#     Aastra_test_access(application,parameter,uri)
#        Tests if the phone is allowed to access the application if not ask for a password
#     Aastra_add_access(application)
#        Add a MAC address in the config file to authorize it.
#     Aastra_test_php_function(function,message)
# 	  This function checks if the the given function is available in order to check if a PHP extension
#        is installed. If the test fails, the error message is displayed in a TextScreen.
#     Aastra_trace_call(script,string)
#        Traces a call to a function
#     Aastra_debug(string)
#        Debug traces
#     Aastra_readINIfile(filename,commentchar,delim)
#        This function is much more performant than the php native read_ini_file and supports " and '.
#     Aastra_readCFGfile(filename,commentchar,delim)
#        This function allows a clean read of a cfg file (aastra or MAC).
#     Aastra_mail_attachment(from, to, subject, attachment,format)
#        Send an email with an attachment.
#     Aastra_delete_session()
#        This function deletes the temporary file containing the XML session elements.
#     Aastra_save_session(appli,expire,array)
#        This function saves session parameters in a temporary file for future recovery.
#     Aastra_read_session(appli)
#        This function reads the temporary session file and retrieve the data if the TTL has not expired.
#     Aastra_get_user_context(user,appli)
#        This function reads the file which contains the user context parameters under /var/cache/aastra 
#        for each application.
#     Aastra_save_user_context(user,appli,data)
#        This function writes the file with the user context parameters under /var/cache/aastra 
#        for each application.
#     Aastra_natsort2d(array,index)
#        Performs a natural sort in a multi-dimensional array
#     Aastra_search2d(array,search,index)
# 	  Performs a search in a 2 dimensional array
#     Aastra_get_language()
#        Get the phone language.
#     Aastra_get_label(label,language,file)
#        Get a label in the proper language. If label does not exist the Englsih version is returned.
#     Aastra_get_key_function(search,mac)
#        Returns the key where a specific script is located, the script looks in the MAC.cfg of the 
#        phone on the TFTP server.
#     Aastra_getphone_fingerprint()
#         This function returns the phone fingerprint which is a md5 hash of its model, MAC address and 
#         IP address.
#     Aastra_update_HDconfig_file(config,extension,header)
#         This function updates the configuration file that holds all the login/logout status.
#     Aastra_read_HDconfig_file(config)
#	   This function reads the configuration file that holds all the login/logout status.
#     Aastra_send_HDmail(header,callerid,action,to,sender)
#         This function sends an email to report a Hot Desking event
#     Aastra_delete_mac(mac)
#         This function deletes the MAC.cfg in the TFTP directory
#     Aastra_store_signature(user)
#         This function saves phone signature data in the user context.
#     Aastra_read_signature(user)
#         This function reads phone signature data from the user context.
#     Aastra_get_geolocation(ip)
# 	    This function performs a geolocation of a given IP address.
#     Aastra_xml2array(url,get_attributes,priority,encoding)
#	    This function converts the XML content of a URL into an array.
#      Aastra_secure_connection(uri,port)
#	     This function switches the uri from http to https to secure the next HTTP GET.
#
# Private functions
#     None
###################################################################################################

###################################################################################################
# INCLUDES
###################################################################################################
require_once('AastraIPPhone.php');

###################################################################################################
# CONSTANTS
###################################################################################################
define('AASTRA_MAXLINES',Aastra_max_items_textmenu());
define('AASTRA_MAXSIZE','10000');
define('AASTRA_MAXCONFIGURATIONS','30');
if(is_dir('../config')) define('AASTRA_CONFIG_DIRECTORY','../config/');
else define('AASTRA_CONFIG_DIRECTORY','config/');
if(file_exists('../language.ini')) define('AASTRA_LANGUAGE_FILE','../language.ini');
else define('AASTRA_LANGUAGE_FILE','language.ini');

###################################################################################################
# GLOBAL VARIABLES (SERVER)
###################################################################################################
if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' && $_GET['aastra_forced_https']=='') $XML_HTTP='https://';
else $XML_HTTP='http://';
$os=strtolower(PHP_OS);
if(strpos($os,'win') === false) 
	{
	if(file_exists(AASTRA_CONFIG_DIRECTORY.'server.conf')) $array_config_server=Aastra_readINIfile(AASTRA_CONFIG_DIRECTORY.'server.conf','#','=');
	else $array_config_server=Aastra_readINIfile('/etc/aastra-xml.conf','#','=');
	}
else $array_config_server=Aastra_readINIfile(AASTRA_CONFIG_DIRECTORY.'server.conf','#','=');
if($array_config_server['General']['public']!='') 
	{
	$XML_SERVER=$XML_HTTP.$array_config_server['General']['public'].Aastra_getvar_safe('SCRIPT_NAME','','SERVER');
	$XML_IP=$array_config_server['General']['public'];
	}
else 
	{
	$XML_SERVER=$XML_HTTP.Aastra_getvar_safe('HTTP_HOST','','SERVER').Aastra_getvar_safe('SCRIPT_NAME','','SERVER');
	$XML_IP=Aastra_getvar_safe('SERVER_ADDR','','SERVER');
	}

if(isset($_GET['aastra_forced_https']))
	{
	if($_GET['aastra_forced_https']!='')
		{
		$parsed=parse_url($XML_SERVER);
		$XML_SERVER=$parsed['scheme'].'://'.$parsed['host'].':'.$_GET['aastra_forced_https'].$parsed['path'];
		}
	}
$XML_SERVER_PATH = substr($XML_SERVER,0,(strlen($XML_SERVER)-strlen(strrchr($XML_SERVER, '/'))+1));
if($array_config_server['General']['public']!='') $AA_XML_SERVER = $array_config_server['General']['public'];
else $AA_XML_SERVER = Aastra_getvar_safe('HTTP_HOST','','SERVER');

###################################################################################################
# PATHS for LOCAL APPLICATIONS
###################################################################################################
if($array_config_server['General']['cache']!='') define('AASTRA_PATH_CACHE',$array_config_server['General']['cache'].'/');
else define('AASTRA_PATH_CACHE','/var/cache/aastra/');

###################################################################################################
# XML DIRECTORY
###################################################################################################
if($array_config_server['General']['xmldirectory']!='') $AA_XMLDIRECTORY=$array_config_server['General']['xmldirectory'];
else $AA_XMLDIRECTORY='xml';

###################################################################################################
# TFTP DIRECTORY
###################################################################################################
if($array_config_server['General']['tftp']!='') define('AASTRA_TFTP_DIRECTORY',$array_config_server['General']['tftp']);
else define('AASTRA_TFTP_DIRECTORY','/tftpboot');

###################################################################################################
# DEBUG/TRACE/TEST/LANGUAGE MODES
###################################################################################################
$DEBUG=False;
if(isset($array_config_server['General']['debug']))
	{
	if($array_config_server['General']['debug']!='') $DEBUG=True;
	}
$TRACE=False;
if(isset($array_config_server['General']['trace']))
	{
	if($array_config_server['General']['trace']!='') $TRACE=True;
	}
$TEST=False;
if(isset($array_config_server['General']['test']))
	{
	if($array_config_server['General']['test']!='') $TEST=TRUE;
	}
$AA_LANGUAGE='';
if(isset($array_config_server['General']['language']))
	{
	if($array_config_server['General']['language']!='') $AA_LANGUAGE=$array_config_server['General']['language'];
	}

###################################################################################################
# Aastra_crypt_MAC(mac,salt)
#
# Encrypt a MAC address to generate a password
#
# Parameters
#   @mac 	MAC address to encrypt
#   @salt	variable seed (only the first 2 characters are used by the encryption algorithm
#
# Returns
#   Encrypted MAC address
###################################################################################################
function Aastra_crypt_MAC($mac,$salt)
{
# extract the 8 end characters
$mac=substr($mac,strlen($mac)-8,8);

# cryt the string
$crypt=crypt($mac,$salt);

# Take the last 6 characters
$crypt=substr($crypt,strlen($crypt)-6,6);

# Convert them to digits
for ($i=0;$i<6;$i++) $crypt{$i}=ord($crypt{$i})%9;

# Return the password
return($crypt);
}

###################################################################################################
# Aastra_test_access(application,parameter,uri)
#
# Tests if the phone is allowed to access the application if not ask for a password
#
# Parameters
#    @application 	name of the application to test
#    @parameter   	name of the parameter to send back the password
#    @uri   		uri called for the input
#
# Returns
#    None
###################################################################################################
function Aastra_test_access($application,$parameter,$uri,$user='')
{
Global $TEST;

# Debug mode
if($TEST) return;

# Get MAC address
if($user=='') 
	{
	$header=Aastra_decode_HTTP_header();
	$user=$header['mac'];
	}
$found=0;

# Check if in the file
$filename=AASTRA_PATH_CACHE.$application.'.cfg';
$handle=@fopen($filename,'r');
if($handle)
	{
	while(($line=fgets($handle,80)) and ($found==0))
		{
		$mac=preg_split("/\n/",$line);
		if($mac[0]==$user) $found=1;
		}
	fclose($handle);
	}

# MAC not found
if($found==0)
	{
	# Display Input Screen
	$output = "<AastraIPPhoneInputScreen type=\"string\" destroyOnExit=\"yes\" password=\"yes\">\n";
	$output .= "<Title>Restricted Application</Title>\n";
	$output .= "<Prompt>Input app. password</Prompt>\n";
	$output .= "<URL>".Aastra_escape_encode($uri)."</URL>\n";
	$output .= "<Parameter>".$parameter."</Parameter>\n";
	$output .= "<Default></Default>\n";
	$output .= "</AastraIPPhoneInputScreen>\n";
	header("Content-Type: text/xml");
	header("Content-Length: ".strlen($output));
	echo $output;
	exit;
	}
}

###################################################################################################
# Aastra_add_access(application)
#
# Add a MAC address in the config file to authorize it.
#
# Parameters
#    @application 	name of the application
#
# Returns
#    None
###################################################################################################
function Aastra_add_access($application,$user)
{
# Check if target directory is present
if (!is_dir(AASTRA_PATH_CACHE))@mkdir(AASTRA_PATH_CACHE);

# Add MAC address
if($user=='')
	{
	$header=Aastra_decode_HTTP_header();
	$user=$header['mac'];
	}
$filename=AASTRA_PATH_CACHE.$application.'.cfg';
$handle=@fopen($filename,'a+');
if($handle)
	{
	fputs($handle,$user."\n");
	fclose($handle);
	}
}

###################################################################################################
# Aastra_test_php_function(function,message)
#
# This function checks if the the given function is available in order to check if a PHP extension
# is installed. If the test fails, the error message is displayed in a TextScreen.
#
# Parameters
#    @function 	php function to test
#    @message 	Error message to be displayed if test fails
# 
# Returns
#   None
#
# Example
#   Aastra_test_php_function('imagecreate','PHP-GD extension not installed');
###################################################################################################
function Aastra_test_php_function($function,$message)
{
if(!function_exists($function))
	{
	$output = "<AastraIPPhoneTextScreen>\n";
	$output .= "<Title>Configuration error</Title>\n";
	$output .= "<Text>".$message."</Text>\n";
	$output .= "</AastraIPPhoneTextScreen>\n";
	header('Content-Type: text/xml');
	header('Content-Length: '.strlen($output));
	echo $output;
	exit;
	}
}

###################################################################################################
# Aastra_trace_call(script,string)
#
# Traces a call to a function
#
# Parameters
#    @version 	name of the script
#    @string 		string to trace
# 
# Returns
#    None
###################################################################################################
function Aastra_trace_call($script,$string)
{
Global $TRACE;

# Traces?
if($TRACE)
	{
	# Check if target directory is present
	if (!is_dir(AASTRA_PATH_CACHE))@mkdir(AASTRA_PATH_CACHE);
	
	# Trace in the file
	$date=AASTRA_PATH_CACHE.date('mdY').'.log';
	$time=date('H:i:s A');
	$handle=@fopen($date,"a");
	if($handle)
		{
		fputs($handle,$time."\t".Aastra_getvar_safe('REMOTE_ADDR','','SERVER')."\t".Aastra_getvar_safe('HTTP_USER_AGENT','','SERVER')."\t".$script."\t".$string."\n");
		fclose($handle);
		}
	}
}

###################################################################################################
# Aastra_debug(string)
#
# Traces a call to a function
#
# Parameters
#    @string 		string to trace
# 
# Returns
#    None
###################################################################################################
function Aastra_debug($string)
{
Global $DEBUG;

# Traces?
if($DEBUG)
	{
	# Trace in the file
	$date=AASTRA_PATH_CACHE.date('mdY').'.debug';
	$time=date('H:i:s A');
	$handle=@fopen($date,'a');
	if($handle)
		{
		fputs($handle,$time."\t".Aastra_getvar_safe('HTTP_USER_AGENT','','SERVER')."\t".Aastra_getvar_safe('SCRIPT_NAME','','SERVER')."\t".$string."\n");
		fclose($handle);
		}
	}
}

###################################################################################################
# Aastra_readINIfile(filename,commentchar,delim)
#
# This function is much more performant than the php native read_ini_file and supports " and '.
#
# Parameters
#   @filename		file to use
#   @commentchar	char used for comments
#   @delim		delimiter
#
# Returns
#   Array with the content of the entry file
###################################################################################################
function Aastra_readINIfile ($filename, $commentchar, $delim) 
{
# Get file content with a shared lock to avoid race conditions
$array1 = array();
$handle = @fopen($filename, 'r');
if ($handle)
	{
	if (flock($handle, LOCK_SH))
		{
		while (!feof($handle)) $array1[] = fgets($handle);
		flock($handle, LOCK_UN);
		}   
	fclose($handle);
	}
$section='';
$array2=NULL;
foreach($array1 as $filedata) 
	{
   	$dataline=trim($filedata);
   	$firstchar=substr($dataline, 0, 1);
   	if ($firstchar!=$commentchar && $dataline!='') 
		{
     		#It's an entry (not a comment and not a blank line)
     		if ($firstchar == '[' && substr($dataline, -1, 1) == ']') 
			{
       		#It's a section
			$section = substr($dataline, 1, -1);
     			}
		else
			{
		       #It's a key...
       		$delimiter = strpos($dataline, $delim);
       		if ($delimiter > 0) 
				{
         			#...with a value
         			$key = strtolower(trim(substr($dataline, 0, $delimiter)));
         			$value = trim(substr($dataline, $delimiter + 1));
         			if (substr($value, 1, 1) == '"' && substr($value, -1, 1) == '"') { $value = substr($value, 1, -1); }
         			$array2[$section][$key] = stripcslashes($value);
       			}
			else
				{
         			#...without a value
         			$array2[$section][strtolower(trim($dataline))]='';
       			}
     			}
   		}
	else
		{
     		#It's a comment or blank line.  Ignore.
   		}
  	}

# Return array with data
return $array2;
}

###################################################################################################
# Aastra_readCFGfile(filename,commentchar,delim)
#
# This function allows a clean read of a cfg file (aastra or MAC).
#
# Parameters
#   @filename		file to use
#   @commentchar	char used for comments
#   @delim		delimiter
#
# Returns
#   Array with the content of the entry file
###################################################################################################
function Aastra_readCFGfile ($filename, $commentchar, $delim) 
{
$array1=@file($filename);
$section='';
if(isset($array1))
	{
	foreach($array1 as $filedata) 
		{
	   	$dataline = trim($filedata);
   		$firstchar = substr($dataline, 0, 1);
	   	if ($firstchar!=$commentchar && $dataline!='') 
			{
     			#It's an entry (not a comment and not a blank line)
	     		if ($firstchar == '[' && substr($dataline, -1, 1) == ']') 
				{
       			#It's a section
				$section = substr($dataline, 1, -1);
	     			}
			else
				{
		       	#It's a key...
	       		$delimiter = strpos($dataline, $delim);
       			if ($delimiter > 0) 
					{
         				#...with a value
         				$key = trim(substr($dataline, 0, $delimiter));
	         			$value = trim(substr($dataline, $delimiter + 1));
       	  			if (substr($value, 1, 1) == '"' && substr($value, -1, 1) == '"') { $value = substr($value, 1, -1); }
         				$array2[$section][$key] = stripcslashes($value);
       				}
				else
					{
         				#...without a value
         				$array2[$section][trim($dataline)]='';
       				}
	     			}
   			}
		else
			{
     			#It's a comment or blank line.  Ignore.
	   		}
  		}
	}

if(isset($array2)) return($array2);
else return(NULL);
}


###################################################################################################
# Aastra_mail_attachment ($from , $to, $subject, $message, $attachment, $format)
# 
# Sends an email with an attachment
#
# Parameters
#    @from		Email address of the sender
#    @to		Email address of the destination
#    @subject		Subject of the email
#    @message		Body of the message.
#    @attachment	Path to the file to attach.
#    @format		Format of the attachment ('text' by default, 'html' is also supported)
#
# Returns
#    None
###################################################################################################

# Aastra_mail_attachment(from, to, subject, attachment,format)
#
# Returns
# format=text or html
#

function Aastra_mail_attachment ($from , $to, $subject, $message, $attachment, $format='text')
{
# Process attachment
$fileatt = $attachment;
$fileatt_type = 'application/octet-stream';
$start=strrpos($attachment,'/') == -1 ? strrpos($attachment, '//') : strrpos($attachment, '/')+1;
$fileatt_name = substr($attachment, $start, strlen($attachment));

# Create enveloppe of the email
$email_from = $from;
$email_subject =  $subject;
$email_txt = $message; 
$email_to = $to; 
$headers = 'From: '.$email_from;
$file = @fopen($fileatt,'rb'); 
$data = @fread($file,filesize($fileatt)); 
@fclose($file); 
$semi_rand = md5(time()); 
$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 

# Prepare headers    
$headers .= "\nMIME-Version: 1.0\n" . 
            "Content-Type: multipart/mixed;\n" . 
            " boundary=\"{$mime_boundary}\""; 
$email_message .= "This is a multi-part message in MIME format.\n\n" . "--{$mime_boundary}\n" ;
if($format=="html")$email_message .= "Content-Type:text/html; charset=\"iso-8859-1\"\n";
else $email_message .= "Content-Type:text; charset=\"iso-8859-1\"\n"; 
$email_message .= "Content-Transfer-Encoding: 7bit\n\n" . 
$email_txt . "\n\n"; 
$data = chunk_split(base64_encode($data)); 

# Add mime
$email_message .= "--{$mime_boundary}\n" . 
                  "Content-Type: {$fileatt_type};\n" . 
                  " name=\"{$fileatt_name}\"\n" . 
                  "Content-Transfer-Encoding: base64\n\n" . 
                 $data . "\n\n" . 
                  "--{$mime_boundary}--\n"; 

# Send the mail
@mail($email_to, $email_subject, $email_message, $headers); 
}

###################################################################################################
# Aastra_delete_session(filename)
#
# This function deletes the temporary file containing the XML session elements.
#
# Parameters
#    filename		File name (optional)
#
# Returns
#    None
###################################################################################################
function Aastra_delete_session($filename=NULL)
{
if($filename==NULL)
	{
	$header=Aastra_decode_HTTP_header();
	$filename=$header['mac'];
	}
$file=AASTRA_PATH_CACHE.$filename.'.session';
if(@file_exists($file)) @unlink($file);
}

###################################################################################################
# Aastra_save_session(appli,expire,array,filename)
#
# This function saves session parameters in a temporary file for future recovery.
#
# Parameters
#    @appli		Name of the application
#    @expire		Expiration time (for the TTL)
#    @array		Data to save (array)
#    @filename	File name (optional)
#
# Returns
#    None
###################################################################################################
function Aastra_save_session($appli,$expire,$array,$filename=NULL)
{
# Check if target directory is present
if (!is_dir(AASTRA_PATH_CACHE))@mkdir(AASTRA_PATH_CACHE);

# Save the session file
if($filename==NULL) 
	{
	$header=Aastra_decode_HTTP_header();
	$filename=$header['mac'];
	}
$handle = @fopen(AASTRA_PATH_CACHE.$filename.'.session', 'w');
if($handle)
	{
	if($expire!=0) $timestamp=time()+$expire;
	else $timestamp=0;
	fputs($handle,'appli='.$appli."\n");
	fputs($handle,'exp='.$timestamp."\n");
	foreach($array as $key=>$item)
		{
		if(($key!='appli') && ($key!='exp')) fputs($handle,$key."=\"".$item."\"\n");
		}
	fclose($handle);
	}
}

###################################################################################################
# Aastra_read_session(appli,filename)
#
# This function reads the temporary session file and retrieve the data if the TTL has not expired.
#
# Parameters
#    @appli		Name of the application
#    @filename	File name (optional)
#
# Returns
#    Array with all the saved parameters from the session
###################################################################################################
function Aastra_read_session($appli,$filename=NULL)
{
if($filename==NULL) 
	{
	$header=Aastra_decode_HTTP_header();
	$filename=$header['mac'];
	}
$array=@parse_ini_file(AASTRA_PATH_CACHE.$filename.'.session',true);
if($array==NULL) $array=array();
else
	{
	if(($appli!=$array['appli']) || (($array['exp']!=0) and (time()>$array['exp']))) $array=array();
	}
return($array);
}

###################################################################################################
# Aastra_get_user_context($user,$appli)
#
# This function reads the file which contains the user context parameters under /var/cache/aastra 
# for each application.
#
# Parameters
#    @user		User ID (usually the MAC address)
#    @appli		Name of the application
#
# Returns
#    Array with all the saved parameters for the application
###################################################################################################
function Aastra_get_user_context($user,$appli)
{
# Check if target directory is present
if (!is_dir(AASTRA_PATH_CACHE))@mkdir(AASTRA_PATH_CACHE);

# File name
$file=AASTRA_PATH_CACHE.$user.'.context';

# If file exists
if(is_file($file)) $array=Aastra_readINIfile($file,'#','=');
else $array=array();

# Return value
if(isset($array[$appli]['data'])) return(unserialize(base64_decode($array[$appli]['data'])));
else return(NULL);
}

###################################################################################################
# Aastra_save_user_context($user,$appli,$data)
#
# This function writes the file with the user context parameters under /var/cache/aastra 
# for each application.
#
# Parameters
#    @user		User ID (usually the MAC address)
#    @appli		Name of the application
#    @data		Data to save as an array
#
# Returns
#    None
###################################################################################################
function Aastra_save_user_context($user,$appli,$data)
{
# Check if target directory is present
if(!is_dir(AASTRA_PATH_CACHE))@mkdir(AASTRA_PATH_CACHE);

# File name
$file=AASTRA_PATH_CACHE.$user.'.context';

# File exists?
if(is_file($file)) $array=Aastra_readINIfile($file,'#','=');
else 
	{
	touch($file);
	chmod($file,0666);
	$array=array();
	}

# Update content
if($data!=NULL) $array[$appli]['data']=base64_encode(serialize($data));
else unset($array[$appli]);

# Create cache file
$handle=@fopen($file,'a+');
if($handle)
	{
	if(flock($handle, LOCK_EX))
 		{
        	ftruncate($handle, 0);
		foreach($array as $key=>$value)
			{
			fputs($handle,'['.$key.']'."\n");
			fputs($handle,'data='.$value['data']."\n");
			}
		flock($handle, LOCK_UN);
 		}
 	fclose($handle);
	}
}

###################################################################################################
# Aastra_natsort2d(&$arrIn,$index=null)
# 
# Performs a natural sort in a multi-dimensional array
#
# Parameters
#    @array		Array to sort
#    @index		Index to use for the sort.
#
# Returns
#    Sorted array
###################################################################################################
function Aastra_natsort2d(&$arrIn,$index=null)
{
$arrTemp = array();
$arrOut = array();
foreach ( $arrIn as $key=>$value ) 
	{
   	reset($value);
       $arrTemp[$key] = is_null($index)
                           ? current($value)
                           : $value[$index];
   	}
   
natsort($arrTemp);
foreach ( $arrTemp as $key=>$value ) $arrOut[$key] = $arrIn[$key];
$arrIn = $arrOut;
}

###################################################################################################
# Aastra_search2d(array,search,index)
# 
# Performs a search in a 2 dimensional array
#
# Parameters
#    @array		Array to sort
#    @search		Value to search
#    @index		Index to use for the search.
#
# Returns
#    False		Not found
#    Array index	if found
###################################################################################################
function Aastra_search2d($array,$search,$index)
{
# Not found yet
$return[0]=False;

# Browse the array
foreach($array as $key=>$value)
	{
	if($value[$index]==$search)
		{
		$return[0]=True;
		$return[1]=$key;
		break;
		}
	}

# Return index
return($return);
}

###################################################################################################
# Aastra_array_multi_sort(array,index,order,natural_sort,case_sensitive)
# 
# Performs a natural sort in a multi-dimensional array recreating indexes
#
# Parameters
#    @array			Array to sort
#    @index			Index to use for the sort.
#    @order			asc or desc, optional default is asc
#    @natural_sort		boolean optional default is FALSE
#    @case_sensitive		boolean optional default is FALSE
#
# Returns
#    Sorted array
###################################################################################################
function Aastra_array_multi_sort($array, $index, $order='asc', $natural_sort=FALSE, $case_sensitive=FALSE)
{
if(is_array($array) && count($array)>0)
	{
	foreach(array_keys($array) as $key) $temp[$key]=$array[$key][$index];
	if(!$natural_sort) ($order=='asc')? asort($temp) : arsort($temp);
	else
		{
		($case_sensitive)? natural_sort($temp) : natcasesort($temp);
		if($order!='asc') $temp=array_reverse($temp,TRUE);
		}
	foreach(array_keys($temp) as $key)
		(is_numeric($key))? $sorted[]=$array[$key] : $sorted[$key]=$array[$key];
	return $sorted;
	}
return $array;
}

###################################################################################################
# Aastra_get_language()
#
# Get the phone language.
#
# Parameters
#     None
#
# Returns
#     Language string
###################################################################################################
function Aastra_get_language()
{
Global $AA_LANGUAGE;

# Get language
if($AA_LANGUAGE!='') $language=$AA_LANGUAGE;
else
	{
	$header=Aastra_decode_HTTP_header();
	$language=$header['language'];
	}

# English is default
if($language=='') $language='en';

# Return Language
return($language);
}

###################################################################################################
# Aastra_get_label(label,language,file)
#
# Get a label in the proper language. If label does not exist the Englsih version is returned.
#
# Parameters
#     @label		label to retrieve
#     @language	language to search (en, fr,...)
#     @file		optional to override the regular location of the language file
#
# Returns
#     Requested string
###################################################################################################
function Aastra_get_label($label,$language,$file=AASTRA_LANGUAGE_FILE)
{
Global $AA_ARRAY_LANGUAGE;
Global $AA_LANGUAGE;

# Load file if needed
if($AA_ARRAY_LANGUAGE==NULL) $AA_ARRAY_LANGUAGE=Aastra_readINIfile($file,'#','=');

# Force language if needed
if($AA_LANGUAGE!='') $language=$AA_LANGUAGE;

# Return the right value
if($AA_ARRAY_LANGUAGE[$label][$language]!='') $return=$AA_ARRAY_LANGUAGE[$label][$language];
else
 	{
  	# check if language code has format like fr_eu or fr_ca (European or Canadian French)
  	if (preg_match('/^([a-z]+)_[a-z]+$/i',$language,$matches)) 
  		{
	   	# if yes, strip _xy suffix and fallback to general language (e.g. fr_eu --> fr)
   		$language=$matches[1];
   		if($AA_ARRAY_LANGUAGE[$label][$language]!='') $return=$AA_ARRAY_LANGUAGE[$label][$language];
  		}

  	# check if we have something to return
  	if (empty($return))
  		{
   		# if entry still not found, fallback to English
   		if($AA_ARRAY_LANGUAGE[$label]['en']!='') $return=$AA_ARRAY_LANGUAGE[$label]['en'];

   		# finally, as a last resort, if English entry was not found, fallback to label name
		else $return=$label;
  		}
	}

# Return label
return($return);
}

###################################################################################################
# Aastra_get_key_function(search,mac)
#
# Returns the key where a specific script is located, the script looks in the MAC.cfg of the phone
# on the TFTP server.
#
# Parameters
#    @search		script name to look for
#    @mac		MAC address of the phone
#
# Returns
#    key name or ''
###################################################################################################
function Aastra_get_key_function($search,$mac)
{
# Not found yet
$return='';

# Read profile file
$array=Aastra_readCFGfile(AASTRA_TFTP_DIRECTORY.'/'.$mac.'.cfg', '#', ':');

# Look into each parameter
foreach($array[''] as $key => $value)
	{
	if(stristr($key,'key'))
		{
		if(stristr($value,$search))
			{
			$pieces=explode(' ',$key);
			$return=$pieces[0];
			}
		}
	}

# Return result
return($return);
}

###################################################################################################
# Aastra_update_HDconfig_file(config,extension,header)
#
# This function updates the configuration file that holds all the login/logout status.
#
# Parameters
#   @config		File name
#   @extension	User extension
#   @header		Phoe info header
#
# Return
#    None
###################################################################################################
function Aastra_update_HDconfig_file($config,$extension,$header=NULL)
{
# Read config file
$array=Aastra_read_HDconfig_file($config);

# Update value
if($header) 
	{
	$array[$extension]=$header;
	$array[$extension]['time']=time();
	}
else unset($array[$extension]);

# Update config file
$handle=@fopen($config,'w');
if($handle)
	{
	foreach($array as $key=>$value)
		{
		fputs($handle,'['.$key.']'."\n");
		foreach($value as $key2=>$value2) 
			{
			if($key2!='module') fputs($handle,$key2.'='.$value2."\n");
			else fputs($handle,$key2.'='.implode(',',$value2)."\n");
			}
		}

	fclose($handle);
	@chmod($config,0777);   
	}
else Aastra_debug('Cannot open '.$config.' in write mode');
}

###################################################################################################
# Aastra_read_HDconfig_file(config)
#
# This function reads the configuration file that holds all the login/logout status.
#
# Parameters
#   @config		File name
#
# Return
#    Array		Header details per extension
###################################################################################################
function Aastra_read_HDconfig_file($config)
{
# Read config file
$array=Aastra_readINIfile($config,'#','=');
if($array==NULL) $array=array();

# Process the modules
foreach($array as $key=>$value)
	{
	$array[$key]['module']=explode(',',$array[$key]['module']);
	array_unshift($array[$key]['module'],'');
	unset($array[$key]['module'][0]);
	}

# Return configuration
return($array);
}

###################################################################################################
# Aastra_send_HDmail(header,callerid,action,to,sender)
#
# This function sends an email to report a Hot Desking event
#
# Parameters
#   @header		Array with 'ip', 'mac' and 'model' parameters
#   @callerid		User extension
#   @action		Action type
#   @to		Email address to send to
#   @sender		Return email address
#
# Return
#    None
###################################################################################################
function Aastra_send_HDmail($header,$callerid,$action,$to,$sender)
{
Global $language;

# Test target
if($to!='')
	{
	$subject=sprintf(Aastra_get_label('Hot Desking event for %s (%s)',$language),$callerid,$action);
	$body=sprintf(Aastra_get_label('Action: %s',$language),$action)."\n";
	$body.=sprintf(Aastra_get_label('User: %s',$language),$callerid)."\n";
	$body.=sprintf(Aastra_get_label('MAC address: %s',$language),$header['mac'])."\n";
	$body.=sprintf(Aastra_get_label('Phone model: %s',$language),$header['model'])."\n";
	$body.=sprintf(Aastra_get_label('IP address: %s',$language),$header['ip'])."\n";
	$body.=sprintf(Aastra_get_label('Server address: %s',$language),$_SERVER['SERVER_ADDR'])."\n";
	$header='From: '.$sender."\nX-Mailer: PHP Script\n";
	mail($to, $subject, $body, $header);
	}
}

###################################################################################################
# Aastra_delete_mac(mac)
#
# This function deletes the MAC.cfg in the TFTP directory
#
# Parameters
#   @mac		MAC address of the phone
#
# Return
#    None
###################################################################################################
function Aastra_delete_mac($mac)
{
$file=AASTRA_TFTP_DIRECTORY.'/'.$mac.'.cfg';
@chmod($file, 0777);
$stream=@fopen($file,'w');
if($stream)
	{
	@fputs($stream,'# Empty MAC file'."\n");
	@fputs($stream,'# Timestamp:'.microtime()."\n");
	@fclose($stream);
	}
else Aastra_debug('Cannot open '.$file.' in write mode');
}

###################################################################################################
# Aastra_store_signature(user)
#
# This function saves phone signature data in the user context.
#
# Parameters
#   @user		user ID
#
# Return
#    None
###################################################################################################
function Aastra_store_signature($user)
{
# Store the signature
$header=Aastra_decode_HTTP_header();
$signature['mac']=$header['mac'];
$signature['ip']=$header['ip'];
$signature['model']=$header['model'];
$signature['signature']=Aastra_getphone_fingerprint();
Aastra_save_user_context($user,'signature',$signature);
}

###################################################################################################
# Aastra_read_signature(user)
#
# This function reads phone signature data from the user context.
#
# Parameters
#   @user		user ID
#
# Return
#    array
#       mac
# 	 ip
#       model
#       signature
###################################################################################################
function Aastra_read_signature($user)
{
return(Aastra_get_user_context($user,'signature'));
}

###################################################################################################
# Aastra_get_geolocation(ip)
#
# This function performs a geolocation of a given IP address.
#
# Parameters
#   @ip		IP address
#
# Return
#    array
#       0		Boolean, success or failure of the search
# 	 array		Array with the geolocation data	
#       	city
#       	region
#		country_code
#		country_name
#		continent_code
#		latitude
#		longitude
###################################################################################################
function Aastra_get_geolocation($ip)
{
# OK so far
$return[0]=True;

# Open and retrieve XML answer
$handle = @fopen('http://www.geoplugin.net/xml.gp?ip='.$ip, 'r');
if($handle)
	{	
	while ($line=fgets($handle,1000)) $data.=$line;
	fclose($handle);
	}
else $return[0]=False;

# Next step
if($return[0])
	{
	# Parse the answer
	$p = xml_parser_create('ISO-8859-1');
	xml_parse_into_struct($p, $data, $vals, $index);
	xml_parser_free($p);
	
	# Check data
	if($vals!=NULL)
		{
		# Save data
		$array['city']=$vals[$index['GEOPLUGIN_CITY'][0]]['value'];
		if($array['city']=='(null)') $array['city']=''; 
		$array['region']=$vals[$index['GEOPLUGIN_REGION'][0]]['value'];
		if($array['region']=='(null)') $array['region']='';
		$array['country_code']=$vals[$index['GEOPLUGIN_COUNTRYCODE'][0]]['value'];
		$array['country_name']=$vals[$index['GEOPLUGIN_COUNTRYNAME'][0]]['value'];
		$array['continent_code']=$vals[$index['GEOPLUGIN_CONTINENTCODE'][0]]['value'];
		$array['latitude']=$vals[$index['GEOPLUGIN_LATITUDE'][0]]['value'];
		$array['longitude']=$vals[$index['GEOPLUGIN_LONGITUDE'][0]]['value'];
		if($array['continent_code']=='IP Address not found') $return[0]=False;
		}
	else $return[0]=False;
	}

# Prepare the data
if($return[0]) $return[1]=$array;

# Return result
return($return);
}

###################################################################################################
# Aastra_xml2array(url,get_attributes,priority,encoding)
#
# This function converts the XML content of a URL into an array.
#
# Parameters
#   @url		URL to get data from
#   @get_attributes	Boolean to tell if attributes are wanted (yes by default)
#   @priority		Priority (tag by default)
#   @encoding		iso, utf8... (default iso)
#
# Return
#    array
###################################################################################################
function Aastra_xml2array($url,$get_attributes=1,$priority='tag',$encoding='iso')
{
# XML parser must be available
if(!function_exists('xml_parser_create'))return array ();
$parser=xml_parser_create('');

# Read content
ini_set('user_agent', $_SERVER['HTTP_USER_AGENT']);
if(!($fp=@fopen($url,'rb'))) 
	{
	Aastra_debug('Failed to open URL='.$url.' HTTP header='.print_r($http_response_header,True));
	return array();
	}
$contents='';
while(!feof($fp)) $contents.=fread($fp, 8192);
fclose($fp);

# Set XML decoding parameters
if($encoding=='utf8') xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
xml_parse_into_struct($parser, trim($contents), $xml_values);
xml_parser_free($parser);
if (!$xml_values) return array();

# Init arrays
$xml_array=array();
$parents=array();
$opened_tags=array();
$arr=array();
$current=& $xml_array;
$repeated_tag_index=array (); 

# Browse the values
foreach ($xml_values as $data)
	{
       unset($attributes,$value);
       extract($data);
       $result=array();
       $attributes_data=array();
       if(isset($value))
       	{
            	if ($priority == 'tag') $result=$value;
            	else $result['value']=$value;
        	}
        if(isset($attributes) and $get_attributes)
        	{
            	foreach ($attributes as $attr => $val)
            		{
                	if($priority=='tag') $attributes_data[$attr]=$val;
                	else $result['attr'][$attr]=$val;
            		}
        	}
        if($type=='open')
        	{ 
            	$parent[$level -1]= &$current;
            	if (!is_array($current) or (!in_array($tag, array_keys($current))))
            		{
                	$current[$tag]=$result;
                	if($attributes_data) $current[$tag.'_attr']=$attributes_data;
                	$repeated_tag_index[$tag.'_'.$level]=1;
                	$current=&$current[$tag];
            		}
            else
            		{
                	if(isset($current[$tag][0]))
                		{
                    		$current[$tag][$repeated_tag_index[$tag.'_'.$level]]=$result;
                    		$repeated_tag_index[$tag.'_'.$level]++;
                		}
                	else
                		{ 
                    		$current[$tag]=array (
                        				$current[$tag],
                        				$result
                    					); 
                    		$repeated_tag_index[$tag.'_'.$level]=2;
                    		if(isset($current[$tag.'_attr']))
                    			{
                        		$current[$tag]['0_attr']=$current[$tag.'_attr'];
                        		unset($current[$tag.'_attr']);
                    			}
                		}
               	$last_item_index=$repeated_tag_index[$tag.'_'.$level]-1;
                	$current=& $current[$tag][$last_item_index];
            		}
        	}
        elseif ($type=='complete')
        	{
            	if (!isset($current[$tag]))
            		{
                	$current[$tag]=$result;
                	$repeated_tag_index[$tag.'_'.$level]=1;
                	if($priority=='tag' and $attributes_data)
                    	$current[$tag.'_attr']=$attributes_data;
            		}
            	else
            		{
                	if (isset ($current[$tag][0]) and is_array($current[$tag]))
                		{
                    		$current[$tag][$repeated_tag_index[$tag.'_'.$level]]=$result;
                    		if ($priority=='tag' and $get_attributes and $attributes_data) $current[$tag][$repeated_tag_index[$tag.'_'.$level].'_attr']=$attributes_data;
                    		$repeated_tag_index[$tag.'_'.$level]++;
		             	}
                	else
                		{
                    		$current[$tag]=array(
                        				$current[$tag],
                        				$result
                    					); 
                    		$repeated_tag_index[$tag.'_'.$level]=1;
                    		if($priority=='tag' and $get_attributes)
                    			{
                        		if (isset ($current[$tag . '_attr']))
                        			{ 
                            		$current[$tag]['0_attr']=$current[$tag.'_attr'];
                            		unset($current[$tag.'_attr']);
                        			}
                        		if ($attributes_data) $current[$tag][$repeated_tag_index[$tag.'_'.$level].'_attr']=$attributes_data;
                    			}
                    		$repeated_tag_index[$tag.'_'. $level]++;
                		}
            		}
        	}
        elseif ($type=='close') $current= &$parent[$level -1];
    	}

# Return array
return ($xml_array);
}

###################################################################################################
# Aastra_secure_connection(uri,port)
#
# This function switches the uri from http to https to secure the next HTTP GET.
#
# Parameters
#   @uri		URI 
#   @port		SSL port to use (optional 443 by default)
#
# Return
#    New secured uri
###################################################################################################
function Aastra_secure_connection($uri,$port='443')
{
# Parse the initial uri
$parsed=parse_url($uri);

# HTTP?
if($parsed['scheme']=='http')
	{
	# Change scheme to HTTPS
	$parsed['scheme']='https';

	# Keep the current HTTP port
	$forced=$parsed['port'];
	if($forced=='') $forced='80';

	# Set the new port
	$parsed['port']=$port;

	# Modify the query
	if($parsed['query']=='') $parsed['query'].='aastra_forced_https='.$forced;
	else $parsed['query'].='&aastra_forced_https='.$forced;

	# Regenerate the new uri
	$uri=$parsed['scheme'].'://'.$parsed['host'].':'.$parsed['port'].$parsed['path'].'?'.$parsed['query'];
	}

# Return uri
return($uri);
}

# Debug mode using php-cli
if(php_sapi_name()=='cli')
	{
	for($i=1;$i<$argc;$i++) 
		{
		$things=split('=',$argv[$i]);
		$_GET[$things[0]]=$things[1];
		}
	unset($things);
	}
?>
