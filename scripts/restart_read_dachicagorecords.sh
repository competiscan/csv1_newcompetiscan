#!/bin/bash
cd /srv/httpd/competiscan.com/html
pgrep -f cron_read_dachicagorecords.php || /usr/bin/php cron_read_dachicagorecords.php
