<?
// require this file to post status to nagios
$_scriptname=basename($_SERVER['PHP_SELF']);
if(!isset($_rc)) $_rc=0; // return code
if(!isset($_interval)) $_interval=86400; // run this often
if($_interval == 0) $_interval=86400; // run this often
if(!isset($_nintervals)) $_nintervals=7; // keep status for this many days
if($_nintervals == 0) $_nintervals=7; // keep status for this many days
exec("/etc/nagios/post2nagios.pl $_scriptname '' $_rc $_interval $_nintervals");
?>
