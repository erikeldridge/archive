#!/bin/bash
#ref: http://wiki.centos.org/HowTos/Network/IPTables#head-724ed81dbcd2b82b5fd3f648142796f3ce60c730

iptables -P INPUT ACCEPT
iptables -F
iptables -A INPUT -i lo -j ACCEPT
iptables -A INPUT -m state --state ESTABLISHED,RELATED -j ACCEPT
#open 22 for ssh
iptables -A INPUT -p tcp --dport 22 -j ACCEPT
#open 80 for http
iptables -A INPUT -p tcp --dport 80 -j ACCEPT
iptables -P INPUT DROP
iptables -P FORWARD DROP
iptables -P OUTPUT ACCEPT
iptables -L -v
/sbin/service iptables save
