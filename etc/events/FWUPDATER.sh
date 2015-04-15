#!/bin/sh
echo "[$0] ..."
# Copy "/bin/echo" command to /var/. (note: /var is ramfs) to avoid system cann't reboot issue
if [ -f /bin/busybox ]; then
	cp -f /bin/busybox /var/.
	ln -s ./busybox /var/echo
fi

fwupdater -i /var/firmware.seama
/var/echo 1 > /proc/driver/system_reset
/var/echo 1 > /proc/system_reset
echo 1 > /proc/driver/system_reset
echo 1 > /proc/system_reset
