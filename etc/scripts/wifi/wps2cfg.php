# Auto generated config file<?
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/upnp.php";
include "/htdocs/phplib/trace.php";

$phy	= XNODE_getpathbytarget("", "phyinf", "uid", $PHY_UID, 0);
$phyrp	= XNODE_getpathbytarget("/runtime", "phyinf", "uid", $PHY_UID, 0);
$wifi	= XNODE_getpathbytarget("/wifi", "entry", "uid", query($phy."/wifi"), 0);
$br		= XNODE_getpathbytarget("", "inf", "uid", query($phyrp."/brinf"),0);
$brrp	= XNODE_getpathbytarget("/runtime", "phyinf", "uid", query($br."/phyinf"), 0);

$upnp_devtype = "urn:schemas-wifialliance-org:device:WFADevice:1";
$upnprp = UPNP_getdevpathbytype(query($phyrp."/brinf"), $upnp_devtype);
if ($upnprp == "")
{
	$USE_UPNP	= 0;
	$UUID		= "";
}
else
{
	$USE_UPNP	= 1;
	$UUID		= query($upnprp."/guid");
}
anchor($wifi);

/* WPS parsing state*/
set("/runtime/wps/parsing/result_code","0");
set("/runtime/wps/parsing/result_reason","");

/* WPS function check */
if (query("wps/enable") != "1" || query("ssidhidden") != "0") 
{
	set("/runtime/wps/parsing/result_code","-2");
}

/* WPS state */
if (query("wps/configured")==1)	$WPS_STATE = 2;
else							$WPS_STATE = 1;

$PINDISABLED=query("wps/locksecurity");
if ($PINDISABLED == "")
{
	if ($WPS_STATE == "2")	$PINDISABLED = 1;
	else					$PINDISABLED = 0;
}

$SSID = query("ssid");
$auth = query("authtype");
$wepmode = query("encrtype");

if ($PARAM=="enrollee")
{
	/* use our own PIN */
	$DEVPWDID = "0x0000";
	/* Get configured PIN. */
	$DEVPWD = query("wps/pin");
	/* Factory default PIN. (label) */
	if ($DEVPWD == "") $DEVPWD = query("/runtime/devdata/pin");
}
else
{
	if ($PBC==1) $PIN = "00000000";
	else $PIN = query($phyrp."/media/wps/enrollee/pin");
	/* Get all the config setting for WPS */
	if ($PIN=="00000000")	$DEVPWDID = "0x0004";
	else					$DEVPWDID = "0x0000";

	$DEVPWD=$PIN;
	set($phyrp."/media/wps/enrollee/pin", "");
	/* Config ourself, if we are unconfigured. */
	if ($WPS_STATE == 1)
	{
		$auth = "WPA2PSK";
		$wepmode = "AES";
	}
}

if		($auth == "OPEN")		{ $AUTH_TYPE="0x0001"; }	/* Open */
else if	($auth == "SHARED")		{ $AUTH_TYPE="0x0004"; set("/runtime/wps/parsing/result_code","-1");}	/* Shared-Key Note: Deprecated in version 2.0 */
else if	($auth == "WPA")		{ $AUTH_TYPE="0x0008"; set("/runtime/wps/parsing/result_code","-1");}	/* WPA  Note: Deprecated in version 2.0 */
else if	($auth == "WPAPSK")		{ $AUTH_TYPE="0x0002"; set("/runtime/wps/parsing/result_code","-1");}	/* WPA PSK Note: Deprecated in version 2.0 */
else if	($auth == "WPA2")		{ $AUTH_TYPE="0x0010"; }	/* WPA2 */
else if	($auth == "WPA2PSK")	{ $AUTH_TYPE="0x0020"; }	/* WPA2 PSK */
else if	($auth == "WPA+2")		{ $AUTH_TYPE="0x0018"; set("/runtime/wps/parsing/result_code","-1");}	/* WPA/WPA2 */
else if	($auth == "WPA+2PSK")	{ $AUTH_TYPE="0x0022"; }	/* For WPA/WPA2 PSK*/
else if	($auth == "8021X")		{ $AUTH_TYPE="0x0001"; set("/runtime/wps/parsing/result_code","-1");}	/* 802.1x */

if		($wepmode == "NONE")		{ $ENCR_TYPE="0x0001"; }	/* None */
else if	($wepmode == "WEP")			{ $ENCR_TYPE="0x0002"; set("/runtime/wps/parsing/result_code","-1");}	/* WEP Note: Deprecated in version 2.0 */
else if	($wepmode == "TKIP")		{ $ENCR_TYPE="0x0004"; set("/runtime/wps/parsing/result_code","-1");}	/* TKIP Note: Deprecated in version 2.0 */
else if	($wepmode == "AES")			{ $ENCR_TYPE="0x0008"; }	/* AES */
else if	($wepmode == "TKIP+AES")	{ $ENCR_TYPE="0x000c"; } 	/* For TKIP+AES*/

$NWKEY_IDX =1;
if ($wepmode == "WEP")
{
	$NWKEY_IDX = query("nwkey/wep/defkey");
	$NWKEY = query("nwkey/wep/key:".$NWKEY_IDX);
}
else if ($wepmode == "NONE")
{
	$NWKEY = "";
}
else
{
	$NWKEY = query("nwkey/psk/key");
}

/*For AP capabilities*/
$AUTH_TYPE_FLAGS="0x003f";
$ENCR_TYPE_FLAGS="0x000f";

/*For AP only*/
$PRI_DEV_CATEGORY="6";
$PRI_DEV_SUB_CATEGORY="1";
if(query("/wireless/wps/apsetuplocked") == "1" || 
   query("/runtime/wps/setting/aplocked") == "1")
	$APLOCKED = 1;
else
	$APLOCKED = 0;

/*Set configure method according to what we operated.*/
if($PBC == "")
{
	$SELDEVCONFMETHOD=0x278C;
}
else if ($PBC == 1)
{
	if($SELCONFIGUREMETHOD == "virtual") 	   { $SELDEVCONFMETHOD="0x0280";}
	else if($SELCONFIGUREMETHOD == "physical") { $SELDEVCONFMETHOD="0x0480";}
	else									   { $SELDEVCONFMETHOD="0x0480";}
}
else
{
	if($SELCONFIGUREMETHOD == "virtual") 	   { $SELDEVCONFMETHOD="0x2008";}
	else if($SELCONFIGUREMETHOD == "physical") { $SELDEVCONFMETHOD="0x4008";}
	else									   { $SELDEVCONFMETHOD="0x2008";}
}

/*Set configure method according to whether PIN Disabled*/
if($PINDISABLED == "1" && $WPS_STATE == "2")	$CONFIGMETHODS=0x0680;
else											$CONFIGMETHODS=0x278C;

/*Parsing result code and give some explanations*/
$RESULT_CODE = query("/runtime/wps/parsing/result_code");
if($RESULT_CODE == "-1")
{
	set("/runtime/wps/parsing/result_reason","<WPS>: Incompatibility and deprecation in this version.");
}
else if($RESULT_CODE == "-2")
{
	set("/runtime/wps/parsing/result_reason","<WPS>: WPS function is disabled! That could be caused by user expected operation or invisible SSID is enabled.");
}

?>
WPS_STATE=<?=$WPS_STATE?>
AP_SETUP_LOCKED=<?=$APLOCKED?>
PINDISABLED=<?=$PINDISABLED?>
CONFIG_METHODS=<?=$CONFIGMETHODS?>
SELECTED_CONFIGURATION_METHODS=<?=$SELDEVCONFMETHOD?>
DEV_PASSWORD_ID=<?=$DEVPWDID?>
DEV_PASSWORD=<?=$DEVPWD?>
PRI_DEV_CATEGORY=<?=$PRI_DEV_CATEGORY?>
PRI_DEV_OUI=0x0050F204
PRI_DEV_SUB_CATEGORY=<?=$PRI_DEV_SUB_CATEGORY?>
CONN_TYPE_FLAGS=0x01
UUID=<?=$UUID?> 
VERSION=0x10
VERSION2=0x20
DEVICE_NAME=<?	echo query("/runtime/device/modelname"); ?>
MAC_ADDRESS=<?	echo query($phyrp."/macaddr");?>
MANUFACTURER=<?	echo query("/runtime/device/vendor"); ?>
MODEL_NAME=<?	echo query("/runtime/device/modelname"); ?>
MODEL_NUMBER=123456
SERIAL_NUMBER=00000000
RF_BAND=1
OS_VER=0x80000000
SSID=<?=$SSID?>
AUTH_TYPE_FLAGS=<?=$AUTH_TYPE_FLAGS?>
ENCR_TYPE_FLAGS=<?=$ENCR_TYPE_FLAGS?>
AUTH_TYPE=<?=$AUTH_TYPE?>
ENCR_TYPE=<?=$ENCR_TYPE?>
NW_KEY=<?=$NWKEY?>
NW_KEY_IDX=<?=$NWKEY_IDX?>
USE_UPNP=<?=$USE_UPNP?> 
NW_KEY_SHAREABLE=1
DUALBAND=0
RESTART_AP_CMD=/etc/scripts/wps.sh restartap
APLOCKED_CMD=/etc/scripts/aplocked.sh start
WPS_HELPER=/etc/scripts/wps.sh
