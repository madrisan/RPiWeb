#!/bin/bash

# config-rpi-wireless: Configure the wireless interface (requires NetworkManager)
# Copyright (C) 2013 Davide Madrisan <davide,madrisan@fr.ibm,com>

progname="config-rpi-wireless"
progver="4"

function copying() {
   echo "\
"$"This program is free software; you can redistribute it and/or modify it under
the terms of the GNU General Public License version 2 as published by the
Free Software Foundation.  There is NO warranty; not even for MERCHANTABILITY
or FITNESS FOR A PARTICULAR PURPOSE."
}

function version() {
   echo "\
$progname, version $progver
Copyright (C) 2013 Davide Madrisan <davide.madrisan@gmail.com>"
}

function usage() {
   version
   echo "\
Configure the wireless interface (requires NetworkManager)

Usage:
 $progname [--interfacea-alias <interfacea-alias>] [--force] \\
    --encryption {wep/wpa/none} --ssid <SSID> --secret <WEP/WAP-PERSONAL>

where the above options mean:
 -a, --interface-alias    Name of the interface alias (wireless by default)
 -f, --force              Overwrite the wireless configuration files if any
 -e, --encryption         Encryption techology (WEP, WPA, and None)
 -s, --ssid               SSID - Service Set Identifier
 -k, --secret             WEP key or WAP/WAP2 Passphrase
 
Report bugs to <davide.madrisan@gmail.com>."

   exit ${1:-1}
}

INTERFACE_ALIAS="wireless"
ENCRYPTION=
SECRET=
SSID=

CHECK_RESULT=0
CONFIG_FORCE=0

exec_options=`LC_ALL=C getopt -o a:e:fs:k:chV \
   --long interface-alias:,encryption:,force,ssid:,secret:,check-result,\
help,version \
   -n "$progname" -- "$@"`
[ $? = 0 ] || exit 1

eval set -- "$exec_options"

while :; do
   case $1 in
      -a|--interface-alias)
         INTERFACE_ALIAS="$2"; shift ;;
      -e|--encryption)
         ENCRYPTION="$2"; shift ;;
      -f|--force)
         CONFIG_FORCE=1 ;;
      -s|--ssid)
         SSID="$2"; shift ;;
      -k|--secret)
         SECRET="$2"; shift ;;
      -c|--check-result)
         CHECK_RESULT=1 ;;
      -h|--help)
         usage 0 ;;
      -V|--version)
         version; echo; copying; exit 0 ;;
      --) shift; break ;;
      *) notify.error $"unrecognized option"" -- \`$1'" ;;
   esac
   shift
done

[ -n "$SSID" -a -n "$ENCRYPTION" ] || usage 1
[ "$ENCRYPTION" != "none" -a -z "$SECRET" ] && usage 1

NETWORK_CFG_DIR="/etc/sysconfig/network-scripts"

WIRELESS_CFG_FILE="$NETWORK_CFG_DIR/ifcfg-${INTERFACE_ALIAS}"
KEYS_CFG_FILE="$NETWORK_CFG_DIR/keys-${INTERFACE_ALIAS}"

# exit if already configured and --force has not been used
[ -r "$WIRELESS_CFG_FILE" -a -r "$KEYS_CFG_FILE" -a "$CONFIG_FORCE" = 0 ] && exit 2

rm -f "$WIRELESS_CFG_FILE" 
rm -f "$KEYS_CFG_FILE"
# let Network Manager the time to delete the old configuration (if any)
sleep 3

WIRELESS_UUID="$(uuidgen 2>/dev/null)"

case "$ENCRYPTION" in
   "wep") cat > $WIRELESS_CFG_FILE << _EOF
ESSID="$SSID"
MODE=Managed
SECURITYMODE=open
DEFAULTKEY=1
TYPE=Wireless
BOOTPROTO=dhcp
DEFROUTE=yes
PEERDNS=yes
PEERROUTES=yes
IPV4_FAILURE_FATAL=no
IPV6INIT=no
NAME=wireless
UUID=$WIRELESS_UUID
ONBOOT=yes
_EOF
   cat > $KEYS_CFG_FILE << _EOF
KEY1=$SECRET
_EOF
   ;;
   "wpa") cat > $WIRELESS_CFG_FILE << _EOF
ESSID="$SSID"
MODE=Managed
KEY_MGMT=WPA-PSK
WPA_ALLOW_WPA=yes
WPA_ALLOW_WPA2=yes
TYPE=Wireless
BOOTPROTO=dhcp
DEFROUTE=yes
PEERDNS=yes
PEERROUTES=yes
IPV4_FAILURE_FATAL=no
IPV6INIT=no
NAME=wireless
UUID=$WIRELESS_UUID
ONBOOT=yes
_EOF
   cat > $KEYS_CFG_FILE << _EOF
WPA_PSK='$SECRET'
_EOF
   ;;
   "none")
   cat > $WIRELESS_CFG_FILE << _EOF
ESSID="$SSID"
MODE=Managed
TYPE=Wireless
BOOTPROTO=dhcp
DEFROUTE=yes
IPV4_FAILURE_FATAL=yes
IPV6INIT=no
NAME=wireless
UUID=$WIRELESS_UUID
ONBOOT=yes
_EOF
   ;;
*)
   echo "Unsupported encryption technology: $ENCRYPTION" 1>&2
   exit 1
   ;;
esac
 
[ -f "$WIRELESS_CFG_FILE" ] ||
 { echo "Error creating file: $WIRELESS_CFG_FILE"; exit 1; }

if [ "$ENCRYPTION" != "none" ]; then 
   [ -f "$KEYS_CFG_FILE" ] ||
    { echo "Error creating file: $KEYS_CFG_FILE"; exit 1; }
   chmod 600 $KEYS_CFG_FILE
fi

if [ "$CHECK_RESULT" == 1 ]; then
   sleep 5
   /sbin/ip -o -4 addr list wlan0 >/dev/null 2>&1 ||
    { echo "Error configuring the Wireless interface"; exit 1; }
fi

exit 0

