#!/bin/sh
echo [$0]: $1 ... > /dev/console
is_default=`xmldbc -g /runtime/device/devconfsize`
if [ "$1" = "start" ] && [ "$is_default" = "0" ]; then
	if [ -f "/usr/sbin/login" ]; then
		image_sign=`cat /etc/config/image_sign`
		telnetd -l /usr/sbin/login -u Alphanetworks:$image_sign -i br0 &
	else
		telnetd &
	fi
else
	killall telnetd
fi
