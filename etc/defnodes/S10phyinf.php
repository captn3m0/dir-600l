<?
include "/htdocs/phplib/phyinf.php";

/* LAN */
PHYINF_setup("ETH-1", "eth", "br0");
/* WAN */
PHYINF_setup("ETH-2", "eth", "eth2.2");
/* WLAN */
$wlan1 = PHYINF_setup("WLAN-1", "wifi", "ra0");
set($wlan1."/media/band", "11GN");
set($wlan1."/media/multistream", "1T1R");
?>
