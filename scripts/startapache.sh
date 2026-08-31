#!/bin/bash
chkconfig httpd on > /var/log/startapache.out 2>&1
CURRENT_IP=$(wget http://ipinfo.io/ip -qO -)
live_ip1=54.87.243.104
if [ "$CURRENT_IP" != "$live_ip1" ]; then
cd /srv/httpd/competiscan.com/html
bash digitalapi-start.sh



else
echo "This is Not the server We want to start service"
fi