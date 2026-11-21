<?php
set_time_limit(0);
require_once('includes/globalSession.php');
if(!isset($_SESSION['public_admin_access'])){
	require_once('includes/checklogin.php');
}
require_once('includes/paginator.php');       //paginator class.
require_once('includes/paginator_html.php');  //paginator_html class.
require_once 'HTTP/Download.php';


$width = 750;
$height = 800;

if(isset($_REQUEST['productID'])) $productID = (float) $_REQUEST['productID'];
else $productID = 0;
if(isset($_REQUEST['ci_id'])) {
	$ci_id = (float) $_REQUEST['ci_id'];
}
else {
	$ci_id = 0;
}
if(!empty($ci_id)){
	$qwhere = 'ci_id='.$ci_id;
}
else{
	$qwhere = 'productID='.$productID.' ORDER BY ci_date DESC';
}

if(isset($_REQUEST['avg'])) $show_avg = (int) $_REQUEST['avg'];
else $show_avg = 0;

$q = "SELECT value1,value2,value3,value4,value5,value6,value7,value8,value9,value10,value11,value12,value13,value14,value15,value16,value17,ci_date 
	FROM cscan_insight WHERE $qwhere LIMIT 1";
$rows = $DRW->query($q,$DRW_read);
$rs = $DRW->fetch_row($rows);
$value1 = $rs[0];//Friendly
$value2 = $rs[1];//Personal
$value3 = $rs[2];//Trustworthy
$value4 = $rs[3];//Experienced
$value5 = $rs[4];//Innovative
$value6 = $rs[5];//Comfortable
$value7 = $rs[6];//Engaging
$value8 = $rs[7];//Professional
$value9 = $rs[8];//Accommodating
$value10 = $rs[9];//Down to Earth
$value11 = $rs[10];//Honest
$value12 = $rs[11];//Easy to Understand
$value13 = $rs[12];//Contemporary
$value14 = $rs[13];//Confident
$value15 = $rs[14];//Good Value
$value16 = $rs[15];//Interested in Learning More
$value17 = $rs[16];//Likelihood to Respond
$ci_date = $rs[17];

$categories = array(
'Confident',
'Contemporary',
'Friendly',
'Personal',
'Trustworthy',
'Experienced',
'Innovative',
'Comfortable',
'Engaging',
'Professional',
'Accommodating',
'Down to Earth',
'Honest',
'Easy to Understand'
);

$q = "SELECT MAX(value14),MAX(value13),MAX(value1),MAX(value2),MAX(value3),MAX(value4),MAX(value5),MAX(value6),MAX(value7),MAX(value8),MAX(value9),MAX(value10),MAX(value11),MAX(value12),
	MIN(value14),MIN(value13),MIN(value1),MIN(value2),MIN(value3),MIN(value4),MIN(value5),MIN(value6),MIN(value7),MIN(value8),MIN(value9),MIN(value10),MIN(value11),MIN(value12)
	FROM cscan_insight";// WHERE ci_date='$ci_date'
	/*
	,MAX(value15),MAX(value16),MAX(value17)
	,MIN(value15),MIN(value16),MIN(value17)
	*/
$rows = $DRW->query($q,$DRW_read);
$rs = $DRW->fetch_row($rows);

$highest = 0;
$lowest = 10;
$highest2 = 10;
$lowest2 = 0;
for($i=0;$i<14;$i++){
	if($rs[$i]>$highest){
		$highest = ceil($rs[$i]);
	}
}
for($i=14;$i<28;$i++){
	if($rs[$i]<$lowest){
		$lowest = floor($rs[$i]);
	}
}
$lowest--;

$resultsspider = array($value14,$value13,$value1,$value2,$value3,$value4,$value5,$value6,$value7,$value8,$value9,$value10,$value11,$value12);
$textsizespider = 11;
$textscale = 8;
$webs = 4;

$resultsspider_offset = array();
$spideroutof = $highest - $lowest;
foreach($resultsspider as $k=>$v){
	$resultsspider_offset[$k] = $v - $lowest;
}

if($show_avg){
	$q = "SELECT AVG(value15),AVG(value16),AVG(value17),
	AVG(value14),AVG(value13),AVG(value1),AVG(value2),AVG(value3),AVG(value4),AVG(value5),AVG(value6),AVG(value7),AVG(value8),AVG(value9),AVG(value10),AVG(value11),AVG(value12)
	FROM cscan_insight WHERE ci_date='$ci_date'";
	$rows = $DRW->query($q,$DRW_read);
	$rs = $DRW->fetch_row($rows);
	$results_avg = array($rs[0],$rs[1],$rs[2]);
	$results_avg_spider = array($rs[3],$rs[4],$rs[5],$rs[6],$rs[7],$rs[8],$rs[9],$rs[10],$rs[11],$rs[12],$rs[13],$rs[14],$rs[15],$rs[16]);
	$results_avg_spider_offset = array($rs[3] - $lowest,$rs[4] - $lowest,$rs[5] - $lowest,$rs[6] - $lowest,$rs[7] - $lowest,$rs[8] - $lowest,$rs[9] - $lowest,$rs[10] - $lowest,$rs[11] - $lowest,$rs[12] - $lowest,$rs[13] - $lowest,$rs[14] - $lowest,$rs[15] - $lowest,$rs[16] - $lowest);
}

$titles = array(
'Good Value',
"Interested in\nLearning\nMore",
"Likelihood to\nRespond"
);
$results = array($value15,$value16,$value17);
$outof = $highest2;
$textsize = 11;
$barw = 80;
$barw_space = 90;
$margin = 12;

$font = 'includes/verdana.ttf';
$image = imagecreatetruecolor($width, $height);

$white = imagecolorallocate($image, 255,255,255);
$black = imagecolorallocate($image,0,0,0);
$light = imagecolorallocate($image,225,225,225);
$fill = imagecolorallocate($image, 1, 131, 183);//49, 54, 148
$scalecolor = imagecolorallocate($image, 153, 51, 51); //255, 0, 0
$averagecolor = imagecolorallocate($image, 102, 0, 0); //151, 207, 0

imagefilledrectangle($image, 0, 0, $width, $height, $white);

$cat_count = count($categories);
$angle = (360/$cat_count);
$radius = ($width/4);
$centerx = round(($width - ($radius*2))/2) + $radius;
$centery = round($radius+$margin+($textsize*3));
for($w=$webs;$w>0;$w--){
	$firstx = $firsty = $lastx = $lasty = 0;
	$h = round(($w/$webs) * $radius);
	
	for($a=0;$a<360;$a+=$angle){
		$x = $centerx + round($h * sin(deg2rad($a)));
		$y = $centery - round($h * cos(deg2rad($a)));
		
		if($a==0){
			$firstx = $x;
			$firsty = $y;
		}
		else{
			imagelinethick($image, $lastx, $lasty, $x, $y, $black);
		}
		$lastx = $x;
		$lasty = $y;
	}
	imagelinethick($image, $lastx, $lasty, $firstx, $firsty, $black);
}

$pointsArray = array();
$a = 0;
foreach($resultsspider_offset as $points){
	$h = round(($points/$spideroutof) * $radius);
	$x = $centerx + round($h * sin(deg2rad($a)));
	$y = $centery - round($h * cos(deg2rad($a)));
	
	$pointsArray[] = $x;
	$pointsArray[] = $y;
	$a+=$angle;
}
imagefilledpolygon($image, $pointsArray, count($pointsArray)/2, $fill);

if($show_avg){
	$a = 0;
	$x0 = 0;
	$y0 = 0;
	$x1 = 0;
	$y1 = 0;
	foreach($results_avg_spider_offset as $points){
		$h = round(($points/$spideroutof) * $radius);
		$x = $centerx + round($h * sin(deg2rad($a)));
		$y = $centery - round($h * cos(deg2rad($a)));
		
		if($x1!=0 && $y1!=0){
			imagelinethick($image, $x1, $y1, $x, $y, $averagecolor, 3);
		}
		else{
			$x0 = $x;
			$y0 = $y;
		}
		$x1 = $x;
		$y1 = $y;
		$a+=$angle;
	}
	imagelinethick($image, $x1, $y1, $x0, $y0, $averagecolor, 3);
}

for($w=$webs;$w>0;$w--){
	$h = round(($w/$webs) * $radius);
	
	imagefttext($image,$textscale,0,$centerx+2,$centery+($textscale*1.75)-$h,$scalecolor,$font,number_format($spideroutof*($w/$webs)+$lowest,2));
}

$c = 0;
$h = $radius;
$away = 5;
for($a=0;$a<360;$a+=$angle){
	$x = $centerx + round($h * sin(deg2rad($a)));
	$y = $centery - round($h * cos(deg2rad($a)));
	
	imagelinethick($image, $centerx, $centery, $x, $y, $black);
	
	$label1 = $categories[$c];
	$textwidth1 = getTextWidth($textsizespider, 0, $font, $label1);
	$label2 = number_format($resultsspider[$c],2);
	$textwidth2 = getTextWidth($textsizespider, 0, $font, $label2);
	
	if($x>$centerx) {
		$addx = $away;
		$addx2 = ($textwidth1/2)-($textwidth2/2)+$away;
	}
	elseif($x==$centerx) {
		$addx = -1 * ($textwidth1/2);
		$addx2 = -1 * ($textwidth2/2);
	}
	else {
		$addx = -1 * ($textwidth1+$away);
		$addx2 = -1 * ((($textwidth1+$textwidth2)/2)+$away);
	}
	if($y>$centery) {
		$addy = $textsizespider+$away;
	}
	elseif($y==$centery) {
		$addy = 0;
	}
	else {
		if($x==$centerx) {
			$addy = -1 * ($textsizespider+$away+$away);
		}
		else{
			$addy = -1 * $away;
		}
	}
	$addy2 = $addy+$textsizespider+$away;
	
	imagefttext($image,$textsizespider,0,$x+$addx, $y+$addy,$black,$font,$label1);
	imagefttext($image,$textsizespider,0,$x+$addx2, $y+$addy2,$black,$font,$label2);
	
	$c++;
}
if($show_avg){
	$width2 = $width*0.75;
}
else{
	$width2 = $width;
}
$barh = round($radius);
$barw_tot = $barw + $barw_space;
$totalres = count($results);
$totalwid = ($barw*$totalres) + ($barw*($totalres-1));
$barx = round(($width2 - $totalwid)/2);
$bary = $height - ($textsize*7.5); //4 lines below

for($r=0;$r<=$highest2;$r++){
	$h = round(($r/$highest2) * $barh);
	
	imagelinethick($image, $margin+$textscale, $bary-$h, $width2-$margin, $bary-$h, $light);
	
	if($r!=0){
		imagefttext($image,$textscale,0,$margin,$bary-$h,$scalecolor,$font,$r);
	}
}
if($show_avg){
	foreach($titles as $key=>$title){
		$h = round(($results_avg[$key]/$outof) * $barh);
		
		if($h>0){
			imagefilledrectangle($image, $barx-$textsize, $bary-$h-1, $barx+$barw+$textsize, $bary-$h+1, $averagecolor);
			
			$barx+=$barw_tot;
		}
	}
}

$barx = round(($width2 - $totalwid)/2);
foreach($titles as $key=>$title){
	$h = round(($results[$key]/$outof) * $barh);
	
	if($h>0){
		$label = number_format($results[$key],2);
		//$textwidth = getTextWidth($textsize, 0, $font, $label);
		//$addx = round(($barw - $textwidth)/2);
		//imagefttext($image,$textsize,0,$barx+$addx,$bary-$h-5,$black,$font,$label);
		
		imagefilledrectangle($image, $barx, $bary, $barx+$barw, $bary-$h, $fill);
		
		$starty = $bary+($textsize*1.5);
		$titleArray = preg_split('/(\\r?\\n|\\r)/',$title,-1,PREG_SPLIT_NO_EMPTY);
		foreach($titleArray as $key=>$title){
			$textwidth = getTextWidth($textsize, 0, $font, $title);
			$addx = round(($barw - $textwidth)/2);
			imagefttext($image,$textsize,0,$barx+$addx,$starty+($key*($textsize*1.5)),$black,$font,$title);
		}
		$textwidth = getTextWidth($textsize, 0, $font, $label);
		$addx = round(($barw - $textwidth)/2);
		imagefttext($image,$textsize,0,$barx+$addx,$starty+(count($titleArray)*($textsize*1.5)),$black,$font,$label);
		
		$barx+=$barw_tot;
	}
}
if($show_avg){
	imagefilledrectangle($image, $width2, $bary-($textscale*3), $width2+$textscale, ($bary-($textscale*3))-$textscale, $fill);
	imagefttext($image,$textscale,0,$width2+$textscale+5,$bary-($textscale*3),$black,$font,'This Piece');
	imagefilledrectangle($image, $width2, $bary, $width2+$textscale, $bary-$textscale, $averagecolor);
	imagefttext($image,$textscale,0,$width2+$textscale+5,$bary,$black,$font,'Average Score');
}

//header('Content-Type: text/plain'); exit;

ob_end_clean();

/*
makeCacheable(time());
header("Content-Disposition: inline; filename=\"Competiscan_".date('YmdHis').".jpg\"");
header('Content-Type: image/jpeg');
//header('Content-Type: image/png');
*/
ob_start();
imagejpeg($image,NULL,100);
//imagepng($image);
$ImageData = ob_get_contents();
//$ImageDataLength = ob_get_length();
ob_end_clean();
/*
header("Content-Length: ".$ImageDataLength);
echo $ImageData;
header("Accept-Ranges: bytes");
*/
imagedestroy($image);

$dl = new HTTP_Download();
$dl->setData($ImageData);
$dl->setLastModified(time());
$dl->setContentType('image/jpeg');
$dl->setCacheControl('private');
$dl->setCache(false);
$dl->setContentDisposition(HTTP_DOWNLOAD_INLINE, "Competiscan_".date('YmdHis').".jpg");
$dl->send();

function getTextWidth($textsize, $angle, $font, $text){
	$coords = imageftbbox($textsize, $angle, $font, $text);
	$textwidth = $coords[0] - $coords[2];
	if($textwidth<0) $textwidth = -1 * $textwidth;
	return $textwidth;
}

function imagelinethick($image, $x1, $y1, $x2, $y2, $color, $thick = 1)
{
    /* this way it works well only for orthogonal lines
    imagesetthickness($image, $thick);
    return imageline($image, $x1, $y1, $x2, $y2, $color);
    */
    if ($thick == 1) {
        return imageline($image, $x1, $y1, $x2, $y2, $color);
    }
    $t = $thick / 2 - 0.5;
    if ($x1 == $x2 || $y1 == $y2) {
        return imagefilledrectangle($image, round(min($x1, $x2) - $t), round(min($y1, $y2) - $t), round(max($x1, $x2) + $t), round(max($y1, $y2) + $t), $color);
    }
    $k = ($y2 - $y1) / ($x2 - $x1); //y = kx + q
    $a = $t / sqrt(1 + pow($k, 2));
    $points = array(
        round($x1 - (1+$k)*$a), round($y1 + (1-$k)*$a),
        round($x1 - (1-$k)*$a), round($y1 - (1+$k)*$a),
        round($x2 + (1+$k)*$a), round($y2 - (1-$k)*$a),
        round($x2 + (1-$k)*$a), round($y2 + (1+$k)*$a),
    );
    imagefilledpolygon($image, $points, 4, $color);
    return imagepolygon($image, $points, 4, $color);
}
?>