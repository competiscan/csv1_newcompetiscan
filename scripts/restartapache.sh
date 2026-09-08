#!/bin/bash
#iservice httpd reload > /var/log/restartapache.out 2>&1
rsync -avz /var/www/test/* /srv/httpd/competiscan.com/html
CURRENT_IP=$(wget http://ipinfo.io/ip -qO -)
#echo "$CURRENT_IP"
live_ip1="34.192.5.239"
live_ip2="34.192.33.146"
#live_ip2="180.151.76.131"
mda='fileuploads'
mda1='retrivalservices'
mda2='contentFiles'
if [ "$CURRENT_IP" != "$live_ip1" ] &&  [ "$CURRENT_IP" != "$live_ip2" ]; then


echo "This is Not the server We want to chnage link"

elif [ ! -L /srv/httpd/competiscan.com/html/$mda ]
then
ln -s /data/fileuploads /srv/httpd/competiscan.com/html/$mda
ln -s /data/retrivalservices /srv/httpd/competiscan.com/html/$mda1
ln -s /data/contentFiles /srv/httpd/competiscan.com/html/$mda2
else

echo "Good"



fi
