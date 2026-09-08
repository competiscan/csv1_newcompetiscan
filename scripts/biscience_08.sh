#!/bin/bash
cd /srv/httpd/competiscan.com/html/scripts/
pgrep -f biscience_execute_08.sh || /bin/sh biscience_execute_08.sh