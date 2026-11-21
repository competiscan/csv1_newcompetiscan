#!/bin/bash
cd /srv/httpd/competiscan.com/html/scripts/
pgrep -f biscience_execute_11.sh || /bin/sh biscience_execute_11.sh