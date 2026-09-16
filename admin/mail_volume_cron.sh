#!/bin/sh
logfile='../cron_logs/mail_volume_cron.log'
function logit(){ echo "`date +'%Y-%m-%d %H:%M:%S'` $0: $@" >> $logfile; }

test -f my_lockfile || { logit "cant find my_lockfile";exit 1; }
. my_lockfile
LOCK=`basename $0 .sh`.LOCK

exitcode=0
# wait for a lock
my_lockfile $LOCK || { logit "locked";exit 1; }
trap 'rm -f $LOCK;exit' 0 1 2 3 15
/usr/local/bin/php mail_volume_cron.php
