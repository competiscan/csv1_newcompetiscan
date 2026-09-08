#!/usr/bin/php
<?php
if($_SERVER['argc']<6) {
	print "convert_back.php path name productID dops did\n";
	exit;
}

require_once("includes/ehLog_set.php");
$ehL->start(__FILE__,false);

require_once ("includes/dbcon.php");
$DRW->databaseReadWrite_die = 1;
require_once 'includes/functions.php';
if(!defined('ENV')){
    define('ENV',getenv('SERVER_NAME'));
}
$path = $_SERVER['argv'][1];
$name = $_SERVER['argv'][2];
$productID = $_SERVER['argv'][3];
$dops = $_SERVER['argv'][4];
$document_id = (int)$_SERVER['argv'][5];

if($dops==3){
	$rsize = "-resize '900>'";
	$dops = 0;
}
elseif($dops==2){
	$rsize = '-resize 300 -crop x400';
	$dops = 0;
}
else{
	$rsize = '-resize 400x400';
}
$convert = 'convert -quiet -colorspace RGB -type Optimize -alpha Off '.$rsize.' -strip'; // -density 300x300 -verbose -debug -watermark -define pdf:use-trimbox=true
$pdf = escapeshellarg($path.$name);
$ps = $path.$productID.'.ps';
$jpg = $path.$productID.'%d.jpg';
$jpg0 = $path.$productID.'0.jpg';
$ps_cmd = 'pdftops -paper A3 -expand -nocrop -noshrink -q';

// limit convert and pdftops to cpu cores 0 and 1
$convert = 'taskset -c 0,1 '.$convert;
$ps_cmd = 'taskset -c 0,1 '.$ps_cmd;

$del_i = 0;
$del =  $path.$productID.$del_i.'.jpg';
while(is_file($del)){
	unlink($del);
	$del_i++;
	$del =  $path.$productID.$del_i.'.jpg';
}

$redirect_text = '2>&1'; //$ehL->logpath

$dops_text = "$ps_cmd $pdf $ps $redirect_text; $convert $ps $jpg $redirect_text; [ -f $jpg0 ] || { $ps_cmd -noembt1 -noembtt $pdf $ps $redirect_text; $convert $ps $jpg $redirect_text; [ -f $jpg0 ] || { $ps_cmd -noembt1 -noembtt -noembcidps -noembcidtt $pdf $ps $redirect_text; $convert $ps $jpg $redirect_text; [ -f $jpg0 ] || { $ps_cmd -f 1 -l 1 $pdf $ps $redirect_text; $convert $ps $jpg $redirect_text; [ -f $jpg0 ] || { $ps_cmd -f 2 $pdf $ps $redirect_text; $convert $ps $jpg $redirect_text; } } } }";

if($dops){
	$s = $dops_text;
}
else{
	$s = "$convert -define pdf:use-cropbox=true $pdf $jpg $redirect_text; [ -f $jpg0 ] || { $convert $pdf $jpg $redirect_text; [ -f $jpg0 ] || { $dops_text } }";
}
if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
    echo $s; die;
}

$ex = shell_exec($s);

if(trim($ex)!=''){
	//$ehL->write('['.$s.'] '.$ex);
}

if($document_id!=0){
	$AUTH_DATA = array();
	$AUTH_DATA['userID'] = 0;
	savePDFImage($productID,$document_id,$path);
}

if(is_file($ps)){
	@unlink($ps);
}

$ehL->stop(false);
?>