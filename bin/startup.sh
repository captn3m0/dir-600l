#!/bin/sh
#
# script file to startup
TOOL=flash
GETMIB="$TOOL get"
SETMIB="$TOOL set"
LOADDEF="$TOOL default"
LOADDEFSW="$TOOL default-sw"
LOADDS="$TOOL reset"
SET_TIME="/var/system/set_time"

# See if flash data is valid
$TOOL test-hwconf
if [ $? != 0 ]; then
	echo 'HW configuration invalid, reset default without MAC!'
	eval `$GETMIB HW_NIC0_ADDR`
	eval `$GETMIB HW_NIC1_ADDR`
	eval `$GETMIB HW_WLAN0_WLAN_ADDR`
	$LOADDEF
	$SETMIB HW_NIC0_ADDR $HW_NIC0_ADDR
	$SETMIB HW_NIC1_ADDR $HW_NIC1_ADDR
	$SETMIB HW_WLAN0_WLAN_ADDR $HW_WLAN0_WLAN_ADDR
fi

$TOOL test-dsconf
if [ $? != 0 ]; then
	echo 'Default configuration invalid, reset default!'
	$LOADDEFSW
fi

$TOOL test-csconf
if [ $? != 0 ]; then
	echo 'Current configuration invalid, reset to default configuration!'
	$LOADDS
fi

if [ ! -e "$SET_TIME" ]; then
	flash settime
fi

# Generate WPS PIN number
eval `$GETMIB HW_WSC_PIN`
if [ "$HW_WSC_PIN" = "" ]; then
	$TOOL gen-pin
fi

