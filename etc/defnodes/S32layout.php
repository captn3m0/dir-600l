<?
include "/htdocs/phplib/xnode.php";

$layout = query("/device/layout");

$wlan1 = XNODE_getpathbytarget("/runtime", "phyinf", "uid", "WLAN-1", 1);
$wlan2 = XNODE_getpathbytarget("/runtime", "phyinf", "uid", "WLAN-2", 1);

if ($layout=="router")
{
	set($wlan1."/brinf", "LAN-1");
	set($wlan2."/brinf", "LAN-2");
}
else
{
	set($wlan1."/brinf", "BRIDGE-1");
	set($wlan2."/brinf", "BRIDGE-1");
}

?>
