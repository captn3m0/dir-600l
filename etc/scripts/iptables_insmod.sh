#!/bin/sh
# kernel > 2.6.22, it (may)will use nf_conntrack_max
if [ -f /proc/sys/net/netfilter/nf_conntrack_max ]; then
	echo 4096 > /proc/sys/net/netfilter/nf_conntrack_max
else
	echo 4096 > /proc/sys/net/ipv4/ip_conntrack_max
fi
#insmod /lib/modules/sw_tcpip.ko
insmod /lib/modules/ifresetcnt.ko
