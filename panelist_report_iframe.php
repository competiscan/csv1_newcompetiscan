<?php 
$LOGOUT_PAGE = 'content/index.php';
$ALLOW_GROUPS = array(20);
require_once("auth_auth.php");
require_once('includes/functions.php');

$contactType = array(0=>'All','prod_panelist'=>'Producer Panelists','cons_panelist'=>'Consumer Panelists');//,'member'=>'Competiscan Members'
$contactType_email = array(0=>'',1=>'jennifer@competiscan.com',2=>'maureen@competiscan.com');
$messageTypes = array(0=>'',1=>'Unused',2=>'Used',3=>'Junk',4=>'Copy');
$consumer_max_points = 2000;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Competiscan Report</title>
<link href="includes/competiscan_stylesheet.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" type="text/JavaScript" src="includes/ajax.js"></script>
<style type="text/css">
<!--
 .likeresults {
	padding: 4px;
	border-bottom: dotted 1px #D80000;
	 font-family: arial;
	 font-size: 12px;
	 color: #505050;
	 text-decoration: none;
	 line-height: 18px;
}
-->
</style>
<script type="text/JavaScript">
<!--
function showNote(pid){
	var obj = document.getElementById('notediv'+pid);
	var obj2 = document.getElementById('img'+pid);
	if(obj){
		obj.style.left = (findPosX(obj2)-80)+'px';
		obj.style.top = (findPosY(obj2)+20)+'px';
		obj.style.display = 'block';
	}
}
function hideNote(pid){
	var obj = document.getElementById('notediv'+pid);
	if(obj){
		obj.style.display = 'none';
	}
}
//-->
</script>
</head>
<body style="padding:0px;margin:0px;">
<?php 
if(isset($_SESSION['ctype'])) $ctype = $_SESSION['ctype'];
else $_SESSION['ctype'] = $ctype = 2;

if(isset($_SESSION['sortr'])) $sortr = $_SESSION['sortr'];
else $_SESSION['sortr'] = $sortr = 1;

$start_mr = '00';
$start_dr = '00';
$start_yr = '0000';
$end_mr = '00';
$end_dr = '00';
$end_yr = '0000';
$start_year = 2007;
$to_year = (int)date('Y');
$orderby = '';
$do_points_sent = 1;

function sortLinks($sel,$dis,$text,$orderby1='',$orderby2=''){
	if($sel==$dis){
		$GLOBALS['orderby'] = $orderby1;
	}
	else{
		if(abs($sel)==$dis){
			$GLOBALS['orderby'] = $orderby2;
		}
	}
}
sortLinks($sortr,1,'First Name',' ORDER BY first_name,last_name',' ORDER BY first_name DESC,last_name');
sortLinks($sortr,10,'Last Name',' ORDER BY last_name,first_name',' ORDER BY last_name DESC,first_name');
sortLinks($sortr,8,'(ID)',' ORDER BY competi_id',' ORDER BY competi_id DESC');
sortLinks($sortr,6,'&lt;Email Address&gt;',' ORDER BY email1,email2',' ORDER BY email1 DESC,email2 DESC');
if($ctype!=2){
	sortLinks($sortr,2,'Used<br />Emails',' ORDER BY used DESC,email1',' ORDER BY used ASC,email1');
	sortLinks($sortr,3,'Unused<br />Emails',' ORDER BY unused DESC,email1',' ORDER BY unused ASC,email1');
}
elseif($sortr==2 || $sortr==3 || $sortr==4){
	$orderby = ' ORDER BY last_name,first_name';
}
sortLinks($sortr,4,'Total<br />Emails',' ORDER BY utot DESC,email1',' ORDER BY utot ASC,email1');
sortLinks($sortr,5,'Email<br />Points',' ORDER BY em_points DESC,email1',' ORDER BY em_points ASC,email1');
sortLinks($sortr,7,'',' ORDER BY dm_points DESC,email1',' ORDER BY dm_points ASC,email1');
if($ctype==2){
	sortLinks($sortr,11,'Retrieval<br />Points',' ORDER BY ret_points DESC,email1',' ORDER BY ret_points ASC,email1');
}
if($ctype==2) {
	$tmporderby = ' ORDER BY tot_points2 ASC,email1';
	$tmporderby2 = ' ORDER BY tot_points2 DESC,email1';
}
else {
	$tmporderby = ' ORDER BY prod_points ASC,tot_points ASC,email1';
	$tmporderby2 = ' ORDER BY prod_points DESC,tot_points DESC,email1';
}
sortLinks($sortr,9,'Total<br />Points',$tmporderby2,$tmporderby);
sortLinks($sortr,12,'Envelopes<br />Left',' ORDER BY envelopes_left DESC,envelope_sent ASC,email1',' ORDER BY envelopes_left,envelope_sent DESC,email1');

$tstamp = mktime(0,0,0,(int)date('n')-1,1,$to_year);
$m = date('m',$tstamp);
$Y = date('Y',$tstamp);

if(isset($_SESSION['start_mr'])) $start_mr = $_SESSION['start_mr'];
elseif($ctype==1) $_SESSION['start_mr'] = $start_mr = $m;
if(isset($_SESSION['start_dr'])) $start_dr = $_SESSION['start_dr'];
elseif($ctype==1) $_SESSION['start_dr'] = $start_dr = '01';
if(isset($_SESSION['start_yr'])) $start_yr = $_SESSION['start_yr'];
elseif($ctype==1) $_SESSION['start_yr'] = $start_yr = $Y;
if(isset($_SESSION['end_mr'])) $end_mr = $_SESSION['end_mr'];
elseif($ctype==1) $_SESSION['end_mr'] = $end_mr = $m;
if(isset($_SESSION['end_dr'])) $end_dr = $_SESSION['end_dr'];
elseif($ctype==1) $_SESSION['end_dr'] = $end_dr = '31';
if(isset($_SESSION['end_yr'])) $end_yr = $_SESSION['end_yr'];
elseif($ctype==1) $_SESSION['end_yr'] = $end_yr = $Y;
if(isset($_SESSION['do_points_sent'])) $do_points_sent = $_SESSION['do_points_sent'];
else $_SESSION['do_points_sent'] = $do_points_sent;


if(isset($_GET['page'])) $_SESSION['page_r'] = $page = (int)$_GET['page'];
else {
	if(isset($_SESSION['page_r'])) $page = $_SESSION['page_r'];
	else $_SESSION['page_r'] = $page = 0;
}

if(isset($_GET['limshow'])) {
	$_SESSION['limshow_r'] = $limshow = (int)$_GET['limshow'];
	setcookie('competiscan_limshow_r',$limshow,time()+(86400*364),$COOKIEPATH,$COOKIEDOMAIN);
}
else {
	if(isset($_SESSION['limshow_r'])) $limshow = $_SESSION['limshow_r'];
	elseif(isset($_COOKIE['competiscan_limshow_r'])) $_SESSION['limshow_r'] = $limshow = $_COOKIE['competiscan_limshow_r'];
	else $_SESSION['limshow_r'] = $limshow = 100;
}

$pageend = $page + $limshow;

$n = 0;
$cwhere = '';
$crm_contact_type_m_c = '';
$month_year1 = '';
$last  = count($contactType) - 1;
foreach($contactType as $contact_type_m_c=>$contact_type_m_cTitle){
	if($ctype==$n){
		if($n>0) {
			$cwhere .= " AND ce1.`contact_type_m_c`='{$contact_type_m_c}'";
			$crm_contact_type_m_c = " AND contactTypeID=$n";
		}
	}
	$n++;
}

$query2 = "SELECT `producer_envelope`,`consumer_envelope` FROM `cscan_filter` ORDER BY `filterdate` DESC LIMIT 1";
$query_result2 = $DRW->query($query2,$DRW_read);
$data2 = $DRW->fetch_row($query_result2);
$DRW->free_result($query_result2);
$producer_envelope = $data2[0];
$consumer_envelope = $data2[1];
if($ctype==2){
	$max_envelopes = $consumer_envelope;
}
else {
	$max_envelopes = $producer_envelope;
}

require_once('panelist_report_iframe_update.php');

if($start_yr!='0000' || $end_yr!='0000'){
	$from = '';
	$to = '';
	if($start_yr!='0000'){
		$cwhere .= " AND ce1.`email_date`>='".$DRW->real_escape_string("$start_yr-$start_mr-$start_dr")." 00:00:00'";
		$month_year1 .= " AND `month_year`>='".$DRW->real_escape_string("$start_yr-$start_mr")."'";
		$from = " From $start_yr-$start_mr-$start_dr";
	}
	if($end_yr!='0000'){
		$cwhere .= " AND ce1.`email_date`<='".$DRW->real_escape_string("$end_yr-$end_mr-$end_dr")." 23:59:59'";
		$month_year1 .= " AND `month_year`<='".$DRW->real_escape_string("$end_yr-$end_mr")."'";
		$to = "To $end_yr-$end_mr-$end_dr ";
	}
	
	$month_year_title = "$end_mr/$end_yr";
	$exportitle = date('m/d/Y')." [$from $to]";
}
else {
	$month_year_Y = date('Y');
	$month_year_m = date('m');
	$month_year_title = "$month_year_m/$month_year_Y";
	$exportitle = date('m/d/Y');
}

print "<form name=\"directer\" method=\"post\" action=\"{$_SERVER['PHP_SELF']}\" style=\"padding:0px;margin:0px;\"><input type=\"hidden\" name=\"send_dm\" value=\"1\" /><input type=\"hidden\" name=\"point_type\" value=\"1\" />
<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"950\">";

$query = "CREATE TEMPORARY TABLE `crm_contacts` (
	panelist_id int(10) NOT NULL DEFAULT '0',
	id char(36) NOT NULL DEFAULT '',
	first_name varchar(100) NOT NULL DEFAULT '',
	last_name varchar(100) NOT NULL DEFAULT '',
	competi_id varchar(100) NOT NULL DEFAULT '',
	office_phone varchar(25) NOT NULL DEFAULT '',
	address varchar(150) NOT NULL DEFAULT '',
	city varchar(100) NOT NULL DEFAULT '',
	state varchar (100) NOT NULL DEFAULT '',
	postal_code varchar(20) NOT NULL DEFAULT '',
	email1 varchar(100) NOT NULL DEFAULT '',
	email2 varchar(255) NOT NULL DEFAULT '',
	em_points int(10) NOT NULL DEFAULT '0',
	dm_points int(10) NOT NULL DEFAULT '0',
	unused int(10) NOT NULL DEFAULT '0',
	used int(10) NOT NULL DEFAULT '0',
	noaction int(10) NOT NULL DEFAULT '0',
	ret_points int(10) NOT NULL DEFAULT '0',
	envelopes_curr int(10) NOT NULL DEFAULT '0',
	envelope_sent tinyint(1) NOT NULL DEFAULT '0',
	points_sent int(10) NOT NULL DEFAULT '0',";
$psel = '';
$options = array('0'=>'0','1'=>'1','2'=>'2','5'=>'5','10'=>'10','50'=>'50');
foreach($options as $p){
	$query .= "points_$p int(10) NOT NULL DEFAULT '0',\n";
	$psel .= ',points_'.$p;
}
$query .= "
	PRIMARY KEY (panelist_id)
)";

$DRW->query($query,$DRW_main);

$where = '';
if($_SESSION['search_text_ri']!=''){
	$vs = explode(',',$_SESSION['search_text_ri']);
	$ors = array();
	foreach($vs as $v){
		$v = trim($v);
		if(!empty($v)){
			$v = $DRW->real_escape_string($v);
			$ors[] = "(competi_id LIKE '".$v."%' OR email LIKE '".$v."%' OR first_name LIKE '".$v."%' OR last_name LIKE '".$v."%')";
		}
	}
	$where .= " AND (".implode(' OR ',$ors).")";
}

$result = $DRW->query("SELECT sugar_id,first_name,last_name,competi_id,phone, address, city, state, postalcode,email,alt_email,envelope_sent,panelist_id,points_sent,envelope_count FROM cscan_panelists WHERE active=1 AND parent_panelist_id=0$crm_contact_type_m_c$where",$DRW_read);
while($data = $DRW->fetch_row($result)){
	$id = $data[0];
	$first_name = trim($data[1]);
	$last_name = trim($data[2]);
	$competi_id = trim($data[3]);
	$office_phone = trim($data[4]);
	$address =  trim($data[5]);
	$city = trim($data[6]);
	$state = trim($data[7]);
	$postal_code = trim($data[8]);
	$email1 = trim($data[9]);
	$email2 = trim($data[10]);
	$envelope_sent = (int)$data[11];
	$panelist_id = $data[12];
	$points_sent = $data[13];
	$envelopes_curr = $data[14];

	$result2 = $DRW->query("SELECT SUM(ps_score) FROM cscan_panelist_score WHERE ps_score_type=1 AND panelist_id='".$DRW->real_escape_string($id)."'$month_year1",$DRW_read);
	$data2 = $DRW->fetch_row($result2);
	$ps_score_env = (int)$data2[0];
	
	$result2 = $DRW->query("SELECT SUM(ps_score) FROM cscan_panelist_score WHERE ps_score_type=2 AND panelist_id='".$DRW->real_escape_string($id)."'$month_year1",$DRW_read);
	$data2 = $DRW->fetch_row($result2);
	$ps_score_ret = (int)$data2[0];
	
	$email_from = '';
	$points = 0;
	$unused = 0;
	$used = 0;
	$noaction = 0;
	$extrapA = array();
	$hists = array('');
	$cy = (int)date('Y');
	for($hy=2007;$hy<$cy;$hy++){
		if($start_yr<=$hy && ($end_yr=='0000' || $end_yr>=$hy)){
			if($hy>=2013){
				$hm = '01';
				//if($start_mr<=$hm && ($end_mr=='00' || $end_mr>=$hm)){
				$hists[] = intval($hy.$hm);
				$hm = '07';
				$hists[] = intval($hy.$hm);
			}
			else{
				$hists[] = $hy;
			}
		}
	}
	foreach($hists as $t){
		$query = "SELECT ce1.`email_from`,SUM(ce1.`panelist_score`) as points,COUNT(ce2.`deleted`) as unused,COUNT(ce3.`deleted`) as used,COUNT(ce4.`deleted`) as noaction
			FROM `cscan_email$t` ce1 
			LEFT JOIN `cscan_email$t` ce2 ON(ce1.`muid`=ce2.`muid` AND ce2.`deleted`=1) 
			LEFT JOIN `cscan_email$t` ce3 ON(ce1.`muid`=ce3.`muid` AND ce3.`deleted`=2)
			LEFT JOIN `cscan_email$t` ce4 ON(ce1.`muid`=ce4.`muid` AND ce4.`deleted`=0)
			WHERE ce1.panelist_id=$panelist_id $cwhere GROUP BY ce1.`panelist_id`";
		$query_result = $DRW->query($query,$DRW_read);
		$data = $DRW->fetch_row($query_result);
		$email_from = $data[0];
		$points += (int)$data[1];
		$unused += (int)$data[2];
		$used += (int)$data[3];
		$noaction += (int)$data[4];
		if($ctype==2 && ($unused+$used+$noaction)>0){
			$queryp = "SELECT `panelist_score`,COUNT(*) FROM `cscan_email$t` ce1 WHERE ce1.panelist_id=$panelist_id $cwhere GROUP BY `panelist_score`";
			$query_resultp = $DRW->query($queryp,$DRW_read);
			while($datap = $DRW->fetch_row($query_resultp)){
				if($datap[0]!=''){
					$extrap1 .= 'points_'.$datap[0];
					$extrap2 .= ','.(int)$datap[1];
					if(!isset($extrapA['points_'.$datap[0]])){
						$extrapA['points_'.$datap[0]] = 0;
					}
					$extrapA['points_'.$datap[0]] += (int)$datap[1];
				}
			}
		}
	}
	$extrap1 = '';
	$extrap2 = '';
	foreach($extrapA as $k=>$v){
		$extrap1 .= ','.$k;
		$extrap2 .= ','.$v;
	}
	
	$query = "REPLACE INTO crm_contacts (panelist_id,id,first_name,last_name,competi_id,office_phone,address,city,state,postal_code,email1,email2,
		dm_points,em_points,used,unused,noaction,ret_points,envelopes_curr,envelope_sent,points_sent$extrap1) VALUES ($panelist_id,'".$DRW->real_escape_string($id)."','".$DRW->real_escape_string($first_name)."','".$DRW->real_escape_string($last_name)."','".$DRW->real_escape_string($competi_id)."','".$DRW->real_escape_string($office_phone)."','".$DRW->real_escape_string($address)."','".$DRW->real_escape_string($city)."','".$DRW->real_escape_string($state)."','".$DRW->real_escape_string($postal_code)."','".$DRW->real_escape_string($email1)."','".$DRW->real_escape_string($email2)."',
		".$DRW->real_escape_string($ps_score_env).",".$DRW->real_escape_string($points).",".$DRW->real_escape_string($used).",".$DRW->real_escape_string($unused).",".$DRW->real_escape_string($noaction).",".$DRW->real_escape_string($ps_score_ret).",".$DRW->real_escape_string($envelopes_curr).",$envelope_sent,'".$DRW->real_escape_string($points_sent)."'$extrap2)";
	$DRW->query($query,$DRW_main);
}

$tot = 0;
$em_tot = 0;
$dm_tot  = 0;
$used_tot = 0;
$unused_tot = 0;
$noaction_tot = 0;
$ret_points_tot = 0;
$envelopes_left_tot = 0;
$tot_tot = 0;
$tot_pointsArray = array();
foreach($options as $p){
	$tot_pointsArray[$p] = 0;
}

if(isset($_GET['export'])){
	ob_end_clean();
	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=panelist_report".date("Ymd").".csv");
	
	if($exportitle!='') echo "$exportitle\n";
	
	echo "Number,First Name,Last Name,Email,Office Phone,Address,City,State,Postal Code,";
	if($ctype!=2){
		echo "Used Emails,Unused Emails,";
	}
	echo "Total Emails,Email Points,";
	if($ctype==2){
		echo "Envelope Points,Retrieval Points,";
	}
	else{
		echo "Direct Mail Points,";
	}
	echo "Total Points,Envelopes Left,Notes\n";
}
if($do_points_sent==1){
	$do_points_sent_text = '-points_sent';
}
else{
	$do_points_sent_text = '';
}
$currpage = 0;
//,IF((em_points+dm_points+ret_points)>2000,(em_points+dm_points+ret_points) % 2000,(em_points+dm_points+ret_points)) as tot_points2
$query = "SELECT id,first_name,last_name,competi_id,email1,email2,em_points,dm_points,unused,used,unused+used+noaction as utot,em_points+dm_points+ret_points as tot_points,noaction,ret_points,envelopes_curr as envelopes_left,envelopes_curr,
	office_phone,address,city,state,postal_code,envelope_sent,IF(em_points>0,10,0)+IF(dm_points>0,10,0) as prod_points,panelist_id,points_sent,em_points+dm_points+ret_points$do_points_sent_text as tot_points2$psel
	FROM `crm_contacts`$orderby";
$query_result = $DRW->query($query,$DRW_main);
while($data = $DRW->fetch_row($query_result)){
	$id = $data[0];
	$first_name = $data[1];
	$last_name = $data[2];
	$competi_id = $data[3];
	$email1 = $data[4];
	$email2 = $data[5];
	$em_points = $data[6];
	$dm_points = $data[7];
	$unused = $data[8];
	$used = $data[9];
	$utot = $data[10];
	$tot_points = $data[11];
	$noaction = $data[12];
	$ret_points = $data[13];
	$envelopes_left = $data[14];
	$envelopes_curr = $data[15];
	$office_phone = $data[16];
	$address = $data[17];
	$city = $data[18];
	$state = $data[19];
	$postal_code = $data[20];
	$envelope_sent = $data[21];
	$prod_points = $data[22];
	$panelist_id = $data[23];
	$points_sent = $data[24];
	$tot_points2 = $data[25];
	$nexti = 26;
	$pointsArray = array();
	foreach($options as $p){
		$pointsArray[$p] = $data[$nexti++];
	}
	
	$email_from_txt = "<a href=\"http://crm.competiscan.com/index.php?action=DetailView&amp;module=Contacts&amp;record=$id\" class=\"bluelink\" target=\"_blank\">".$first_name.' '.$last_name.'</a>';
	if($competi_id!='') {
		$email_from_txt .= ' ('.$competi_id.')';
	}
	if($email1!=''){
		$email_from_txt .= '<br />&lt;'.$email1.'&gt;';
	}
	$tot++;
	$used_tot += $used;
	$unused_tot += $unused;
	$noaction_tot += $noaction;
	$em_tot += $em_points;
	$dm_tot += $dm_points;
	$ret_points_tot += $ret_points;
	$envelopes_left_tot += $envelopes_left;
	if($ctype==1){
		$tot_points_text = "\$$prod_points";
		$tot_points_in = $prod_points;
		$tot_tot += $prod_points;
	}
	else {
		$tot_points_text = $tot_points2;
		$tot_points_in = $tot_points2;
		$tot_tot += $tot_points2;
	}
	
	if(isset($_GET['export'])){
		echo  csvExcape($competi_id).",". csvExcape($first_name) .",". csvExcape($last_name). "," . csvExcape($email1). ",". csvExcape($office_phone). "," . csvExcape($address). "," . csvExcape($city). "," . csvExcape($state) . ",". csvExcape($postal_code). ",";
		
		if($ctype!=2){
			echo "$used,$unused,";
		}
		echo "$utot,$em_points,$dm_points,";
			
		if($ctype==2){
			print "$ret_points,";
		}
		print "$tot_points_text,$envelopes_left,";
		$notex = '';
		$resultN = $DRW->query("SELECT DATE_FORMAT(note_date,'%m/%y'),note_data FROM cscan_panelists_note WHERE panelist_id=$panelist_id ORDER BY note_date DESC",$DRW_read);
		while($dataN = $DRW->fetch_row($resultN)){
			$note_date = $dataN[0];
			$note_data = $dataN[1];
			if($notex!=''){
				$notex .= ' ';
			}
			$notex .= $note_date.' - '.$note_data;
		}
		echo csvExcape($notex);
		print "\n";
	}
	elseif(isset($_GET['update'])){
		//nothing
	}
	elseif($currpage>=$page && $currpage<$pageend){
		print "<tr><td valign=\"top\" class=\"likeresults\">$email_from_txt</td>";
		if($ctype!=2){
			print "<td valign=\"top\" class=\"likeresults\" width=\"50\">$used</td>
			<td valign=\"top\" class=\"likeresults\" width=\"55\">$unused</td>";
		}
		print "<td valign=\"top\" class=\"likeresults\" width=\"50\">$utot</td>
		<td valign=\"top\" class=\"likeresults\" style=\"background:#eeeeee;\" width=\"50\"><strong>$em_points</strong>";
		if($ctype==2){
			foreach($options as $p){
				if($pointsArray[$p]>0) {
					print '<br /><em>'.$pointsArray[$p].'x'.$p.'</em>';
					$tot_pointsArray[$p] += $pointsArray[$p];
				}
			}
		}
		print "</td>
		<td valign=\"top\" class=\"likeresults\" style=\"background:#eeeeee;\" width=\"";
		if($ctype==2) print '60';
		else print '50';
		print "\"><strong>$dm_points</strong></td>";
		if($ctype==2){
			print "<td valign=\"top\" class=\"likeresults\" style=\"background:#eeeeee;\" width=\"60\"><strong>$ret_points</strong></td>";
		}
		print "<td valign=\"top\" class=\"likeresults\" style=\"background:#eeeeee;\" width=\"50\"><strong>$tot_points_text</strong>";
		if($ctype==2){
			if($do_points_sent==1){
				if($tot_points_in>=$consumer_max_points){
					print '<br /><label><input type="checkbox" name="ps'.$panelist_id.'" value="1" />sent</label>';
				}
				if($points_sent>0) {
					print '<br /><em>(+'.$points_sent.')</em>';
				}
			}
		}
		print "</td>
		<td valign=\"top\" class=\"likeresults\" width=\"75\">$envelopes_left";
		if($envelopes_left>0){
			print " - <input type=\"text\" name=\"used_envelopes$panelist_id\" size=\"1\" style=\"width:30px;\" />";
		}
		if($envelopes_left!=$max_envelopes){
			print '<br /><label><input type="checkbox" name="es'.$panelist_id.'" value="1" />sent</label>';
			print "<input type=\"hidden\" name=\"old_es$panelist_id\" value=\"$envelope_sent\" />";
		}
		print "</td>
		<td valign=\"top\" nowrap=\"nowrap\" class=\"likeresults\" width=\"";
		if($ctype==2) print '90';
		else print '75';
		print "\">";
		if($ctype==2) print "<input type=\"text\" name=\"dm$panelist_id\" size=\"1\" style=\"width:30px;\" /> <input type=\"text\" name=\"dmc$panelist_id\" size=\"1\" style=\"width:30px;\" />";
		else print "<input type=\"text\" name=\"dm$panelist_id\" size=\"4\" />";
		print "<a href=\"#\" onclick=\"document.directer.submit(); return false;\" title=\"Update\"><img src=\"images/arrow.gif\" border=\"0\" alt=\"Update\" width=\"13\" height=\"13\" id=\"img$panelist_id\" /></a>
		<input type=\"hidden\" name=\"totalpoints$panelist_id\" value=\"$tot_points_in\" />";
		$nstyle = '';
		$resultN = $DRW->query("SELECT DATE_FORMAT(note_date,'%m/%y'),note_data FROM cscan_panelists_note WHERE panelist_id=$panelist_id ORDER BY note_date DESC",$DRW_read);
		if($DRW->num_rows($resultN)>0){
			$nstyle = ' style="border:solid 1px #770008;"';
			print '<div id="notediv'.$panelist_id.'" style="width:200px;position:absolute;display:none;z-index:100;border:solid 1px #000000;background-color:#eeeeee;margin-top:2px;padding:4px;">';
			while($dataN = $DRW->fetch_row($resultN)){
				$note_date = $dataN[0];
				$note_data = $dataN[1];
				print '<div style="border-bottom: dotted 1px #D80000;">'.$note_date.' - '.$note_data.'</div>';
			}
			print '</div>';
		}
		print "</td>
		<td valign=\"top\" class=\"likeresults\" width=\"100\"><input type=\"text\" name=\"note$panelist_id\"$nstyle size=\"10\" onfocus=\"showNote($panelist_id);\" onblur=\"hideNote($panelist_id);\" /></td>
		</tr>";
	}
	$currpage++;
}
if(isset($_GET['export'])){
	exit;
}
print "<tr><td valign=\"top\" class=\"likeresults\" style=\"border-top:solid 1px #000000;border-bottom-color:white;\"><strong>$tot</strong></td>";
if($ctype!=2){
	print "<td valign=\"top\" class=\"likeresults\" style=\"border-top:solid 1px #000000;border-bottom-color:white;\"><strong>$used_tot</strong></td>
	<td valign=\"top\" class=\"likeresults\" style=\"border-top:solid 1px #000000;border-bottom-color:white;\"><strong>$unused_tot</strong></td>";
}
print "<td valign=\"top\" class=\"likeresults\" style=\"border-top:solid 1px #000000;border-bottom-color:white;\"><strong>".($used_tot+$unused_tot+$noaction_tot)."</strong></td>
<td valign=\"top\" class=\"likeresults\" style=\"border-top:solid 1px #000000;border-bottom-color:white;\"><strong>$em_tot</strong>";
if($ctype==2){
	foreach($options as $p){
		if($tot_pointsArray[$p]>0) {
			print '<br /><em>'.$tot_pointsArray[$p].'x'.$p.'</em>';
		}
	}
}
print "</td>
<td valign=\"top\" class=\"likeresults\" style=\"border-top:solid 1px #000000;border-bottom-color:white;\"><strong>$dm_tot</strong></td>";
if($ctype==2){
	print "<td valign=\"top\" class=\"likeresults\" style=\"border-top:solid 1px #000000;border-bottom-color:white;\"><strong>$ret_points_tot</strong></td>";
}
print "<td valign=\"top\" class=\"likeresults\" style=\"border-top:solid 1px #000000;border-bottom-color:white;\"><strong>";
if($ctype==1) print '$';
print "$tot_tot</strong></td>
<td valign=\"top\" class=\"likeresults\" style=\"border-top:solid 1px #000000;border-bottom-color:white;\"><strong>$envelopes_left_tot</strong></td>
<td valign=\"top\" class=\"likeresults\" style=\"border-top:solid 1px #000000;border-bottom-color:white;\"><input class=\"button\" type=\"submit\" name=\"updater\" value=\"Update\" /></td>
<td valign=\"top\" class=\"likeresults\" style=\"border-top:solid 1px #000000;border-bottom-color:white;\">&nbsp;</td>
</tr>";
print '</table></form>';
list($limittext,$pagingtext) = showPaging($_SERVER['PHP_SELF'],$currpage,$page,$limshow);
echo $pagingtext;
?>
</body>
</html>
<?php
function csvExcape($in,$delim = ','){
	$out = $in;
	if(strpos($out, $delim)!==false || strpos($out, '"')!==false || strpos($out, "\r\n")!==false || strpos($out, "\n")!==false || strpos($out, "\r")!==false || preg_match('/^0+\\d+$/',$out)>0){
		$out = '"'.str_replace('"', '""', $out).'"';
	}
	return $out;
}
function showPaging($link,$rowcnt=0,$limstart=0,$limiter=50,$show=10){
	if($rowcnt>0){
		$paging = '<table border="0" cellspacing="2" cellpadding="4">';
		$limit = " LIMIT $limstart,$limiter";
		if(strpos($link,'?')===false) $link .= '?';
		else $link .= '&amp;';
		
		$firstlink = '[First]';
		$prevlink = '[Prev]';
		$nextlink = '[Next]';
		$lastlink = '[Last]';
		$middlelinks = '';
		
		//first and previous only if not on first
		if($limstart>0){
			if($limstart>=$limiter) $prev = $limstart - $limiter;
			else $prev = 0;
			$firstlink = "[<a href=\"{$link}page=0\" class=\"bluelink\">First</a>]";
			$prevlink = "<a href=\"{$link}page=$prev\" class=\"bluelink\">&laquo; Prev $limiter</a>";
		}
		// middle loop through total results
		$numbers = ceil($rowcnt/$limiter);
		$loopstart = ceil($limstart/$limiter);
		if($loopstart<($show-1)) $loopstart = 0; // begin, do not move until 4
		if($numbers<$show) $loopend = $numbers; // loopend is less than $show
		else $loopend = $loopstart+$show;
		if($loopend>$numbers && $loopstart!=0) { // end, show last $show
			$loopstart = $numbers - $show;
			$loopend = $numbers;
		}
		for($i=$loopstart; $i<$loopend; $i++){
			$startnum = $limiter * $i;
			if($startnum!=$limstart) {
				$middlelinks .= "<a href=\"{$link}page=$startnum\" class=\"bluelink\">".($i+1)."</a> ";
			}
			else $middlelinks .= ($i+1).' ';
		}
		$limsum = $limstart+$limiter;
		$limsum2 = $limstart+($limiter*2);
		//next and last if not on last
		if($limstart<$rowcnt && ($limsum2<$rowcnt || ($rowcnt - $limsum)>0)){
			if($limsum2 < $rowcnt) $nextnum = $limiter;
			else $nextnum = $rowcnt-$limsum;
			$nextlink = "<a href=\"{$link}page=$limsum\" class=\"bluelink\">Next $nextnum &raquo;</a>";
			$lastlink = "[<a href=\"{$link}page=".(($numbers-1)*$limiter)."\" class=\"bluelink\">Last</a>]";
		}
		if($middlelinks!='') $middlelinks = "[ $middlelinks ] &nbsp;";
		$paging .= "<tr><td align=\"center\" class=\"bodytext\">Showing ";
		if($rowcnt>100) {
			$paging .= "[ ";
			for($k=100;$k<=500;$k+=100){
				if($rowcnt>($k-100)){
					if($limiter!=$k) $paging .= "<a href=\"{$link}page=0&amp;limshow=$k\" class=\"bluelink\">";
					$paging .= $k;
					if($limiter!=$k) $paging .= "</a>";
					$paging .= ' ';
				}
			}
			$paging .= "] ";
		}
		$paging .= "results ".($limstart+1)." to ";
		if($limsum < $rowcnt) $paging .= $limsum;
		else $paging .= $rowcnt;
		$paging .= " of $rowcnt";
		$paging .= "</td><td align=\"center\" class=\"bodytext\">$firstlink &nbsp; $prevlink &nbsp; $middlelinks $nextlink &nbsp; $lastlink</td></tr>";
		$paging .= "</table>";
	}
	else {
		$paging = '';
		$limit = '';
	}
	return array($limit,$paging);
}
?>