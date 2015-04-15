#!/bin/sh
echo 7200 > /proc/sys/net/ipv4/netfilter/ip_conntrack_tcp_timeout_established
echo 4096 > /proc/sys/net/ipv4/netfilter/ip_conntrack_max

# Tune ip_conntrack hash table size, hash table size = max session * 2 is recommended.
echo 8192 > /sys/module/nf_conntrack/parameters/hashsize

# Tune route cache flush timeout
echo 4096 > /proc/sys/net/ipv4/route/gc_thresh
echo 8192 > /proc/sys/net/ipv4/route/max_size
echo 30 > /proc/sys/net/ipv4/route/secret_interval
echo 1 > /proc/sys/net/ipv4/route/gc_elasticity

# Tune kernel min free memory
#echo 4096 > /proc/sys/vm/min_free_kbytes
echo 200 > /proc/sys/vm/vfs_cache_pressure

# to pass 100M BT test
echo 100 > /proc/sys/net/core/netdev_max_backlog
