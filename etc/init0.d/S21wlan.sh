#!/bin/sh
echo [$0]: $1 ... > /dev/console

insmod /lib/modules/rt2860v2_ap.ko

case "$1" in
start|stop)
	service WIFI.WLAN-1 $1
	;;
*)
	echo [$0]: invalid argument - $1 > /dev/console
	;;
esac
exit 0
