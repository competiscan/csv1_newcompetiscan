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

$countryr = 'US';
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
//also change in imap.php, panelist_score.php and alter table cscan_crm_contacts_data
$options = array('0'=>'0','1'=>'1','2'=>'2','3'=>'3','5'=>'5','10'=>'10','50'=>'50','0.5'=>'half');

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
sortLinks($sortr,6,'&lt;Email Address&gt;',' ORDER BY email,alt_email',' ORDER BY email DESC,alt_email DESC');
if($ctype!=2){
	sortLinks($sortr,2,'Used<br />Emails',' ORDER BY used DESC,competi_id',' ORDER BY used ASC,competi_id');
	sortLinks($sortr,3,'Unused<br />Emails',' ORDER BY unused DESC,competi_id',' ORDER BY unused ASC,competi_id');
}
elseif($sortr==2 || $sortr==3 || $sortr==4){
	$orderby = ' ORDER BY last_name,first_name';
}
sortLinks($sortr,4,'Total<br />Emails',' ORDER BY utot DESC,competi_id',' ORDER BY utot ASC,competi_id');
sortLinks($sortr,5,'Email<br />Points',' ORDER BY email_points DESC,competi_id',' ORDER BY email_points ASC,competi_id');
if($ctype==2){
	sortLinks($sortr,7,'',' ORDER BY envelope_points DESC,competi_id',' ORDER BY envelope_points ASC,competi_id');
	sortLinks($sortr,11,'Retrieval<br />Points',' ORDER BY retrieval_points DESC,competi_id',' ORDER BY retrieval_points ASC,competi_id');
	$tmporderby = ' ORDER BY tot_points ASC,competi_id';
	$tmporderby2 = ' ORDER BY tot_points DESC,competi_id';
}
else{
	sortLinks($sortr,7,'',' ORDER BY directmail_points DESC,competi_id',' ORDER BY directmail_points ASC,competi_id');
	$tmporderby = ' ORDER BY producer_points ASC,tot_points ASC,competi_id';
	$tmporderby2 = ' ORDER BY producer_points DESC,tot_points DESC,competi_id';
}
sortLinks($sortr,9,'Total<br />Points',$tmporderby2,$tmporderby);
sortLinks($sortr,12,'Envelopes<br />Left',' ORDER BY envelope_count DESC,envelope_sent ASC,competi_id',' ORDER BY envelope_count,envelope_sent DESC,competi_id');

$tstamp = mktime(0,0,0,(int)date('n')-1,1,$to_year);
$m = date('m',$tstamp);
$Y = date('Y',$tstamp);

if(isset($_SESSION['countryr'])) $countryr = $_SESSION['countryr'];
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
$where = '';
$cwhere = '';
$crm_contact_type_m_c = '';
$last  = count($contactType) - 1;
foreach($contactType as $contact_type_m_c=>$contact_type_m_cTitle){
	if($ctype==$n){
		if($n>0) {
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
		$cwhere .= " AND `data_date`>='".$DRW->real_escape_string("$start_yr-$start_mr")."'";
		$from = " From $start_yr-$start_mr-$start_dr";
	}
	if($end_yr!='0000'){
		$cwhere .= " AND `data_date`<='".$DRW->real_escape_string("$end_yr-$end_mr")."'";
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
$join = '';
if($countryr!=''){
	$join .= " JOIN cscan_state ON (cp.stateID=cscan_state.stateID AND cscan_state.countryCode='".$DRW->real_escape_string($countryr)."')";
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
$q_count = "SELECT COUNT(*) FROM cscan_panelists cp$join WHERE active=1 AND parent_panelist_id=0$crm_contact_type_m_c$where";
$result = $DRW->query($q_count,$DRW_read);
$data = $DRW->fetch_row($result);
$tot = $data[0];
$psel = '';
foreach($options as $p){
	$psel .= ',sum(points_'.$p.')';
}
$q_sel = "SELECT sum(envelope_points),sum(retrieval_points),sum(directmail_points),sum(email_points),sum(unused),sum(used),sum(noaction),sum(producer_points),sum(cc.points_sent)$psel
	FROM cscan_panelists cp JOIN cscan_crm_contacts_data cc ON (cp.panelist_id=cc.panelist_id)$join WHERE active=1 AND parent_panelist_id=0 $crm_contact_type_m_c $where $cwhere";
$result = $DRW->query($q_sel,$DRW_read);
$data = $DRW->fetch_row($result);
$i = 0;
$env_points_tot = $data[$i++];
$ret_points_tot = $data[$i++];
$dm_tot  = $data[$i++];
$em_tot = $data[$i++];
$unused_tot = $data[$i++];
$used_tot = $data[$i++];
$noaction_tot = $data[$i++];
$prod_points_tot = $data[$i++];
$tot_points_sent = $data[$i++];
$tot_pointsArray = array();
foreach($options as $p){
	$tot_pointsArray[$p] = $data[$i++];
}
$points_sent_text = '';
if($ctype==1){
	$tot_tot = $prod_points_tot;
}
else{
	if($do_points_sent==1){
		$points_sent_text = '-sum(cc.points_sent)';
	}
	else{
		$tot_points_sent = 0;
	}
	$tot_tot = $em_tot+$dm_tot-$tot_points_sent;
}

list($limittext,$pagingtext) = showPaging($_SERVER['PHP_SELF'],$tot,$page,$limshow);
if(isset($_GET['export'])){
	$limittext = '';
}
$q_sel = "SELECT sugar_id,first_name,last_name,competi_id,phone, address, city, state, postalcode,email,alt_email,envelope_sent,cp.panelist_id,envelope_count,
	sum(cc.points_sent),
	sum(envelope_points) as envelope_points,sum(retrieval_points) as retrieval_points,sum(directmail_points) as directmail_points,sum(email_points) as email_points,sum(unused) as unused,sum(used) as used,sum(noaction) as noaction,sum(producer_points) as producer_points,sum(unused)+sum(used)+sum(noaction) as utot,sum(email_points)+sum(directmail_points)$points_sent_text as tot_points
	$psel
	FROM cscan_panelists cp JOIN cscan_crm_contacts_data cc ON (cp.panelist_id=cc.panelist_id)$join WHERE active=1 AND parent_panelist_id=0 $crm_contact_type_m_c $where $cwhere
	GROUP BY cc.panelist_id $orderby $limittext";
$result = $DRW->query($q_sel,$DRW_read);
while($data = $DRW->fetch_row($result)){
    
	$i = 0;
	$id = $data[$i++];
	$first_name = trim($data[$i++]);
	$last_name = trim($data[$i++]);
	$competi_id = trim($data[$i++]);
	$office_phone = trim($data[$i++]);
	$address =  trim($data[$i++]);
	$city = trim($data[$i++]);
	$state = trim($data[$i++]);
	$postal_code = trim($data[$i++]);
	$email1 = trim($data[$i++]);
	$email2 = trim($data[$i++]);
	$envelope_sent = (int)$data[$i++];
	$panelist_id = $data[$i++];
	$envelopes_curr = $data[$i++];
	
	$points_sent = $data[$i++];
	
	$envelope_points = $data[$i++];
	$ret_points = $data[$i++];
	$dm_points = $data[$i++];
	$em_points = $data[$i++];
	$unused = $data[$i++];
	$used = $data[$i++];
	$noaction = $data[$i++];
	$prod_points = $data[$i++];
	$utot = $data[$i++];
	$tot_points = $data[$i++];
	
	$pointsArray = array();
	foreach($options as $p){
		$pointsArray[$p] = $data[$i++];
	}
	
	$email_from_txt = "<a href=\"http://crm.competiscan.com/index.php?action=DetailView&amp;module=Contacts&amp;record=$id\" class=\"bluelink\" target=\"_blank\">".$first_name.' '.$last_name.'</a>';
	if($competi_id!='') {
		$email_from_txt .= ' ('.$competi_id.')';
	}
	if($email1!=''){
		$email_from_txt .= '<br />&lt;'.$email1.'&gt;';
	}
	
	if($ctype==1){
		$tot_points_text = "\$$prod_points";
		$tot_points_in = $prod_points;
	}
	else {
		$tot_points_text = $tot_points;
		$tot_points_in = $tot_points;
	}
	
	if(isset($_GET['export'])){
		echo  csvExcape($competi_id).",". csvExcape($first_name) .",". csvExcape($last_name). "," . csvExcape($email1). ",". csvExcape($office_phone). "," . csvExcape($address). "," . csvExcape($city). "," . csvExcape($state) . ",". csvExcape($postal_code). ",";
		
		if($ctype!=2){
			echo "$used,$unused,";
		}
		echo "$utot,$em_points,";
			
		if($ctype==2){
			print "$envelope_points,$ret_points,";
		}
		else{
			echo "$dm_points,";
		}
		print "$tot_points_text,$envelopes_curr,";
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
	else{ 
		print "<tr><td valign=\"top\" class=\"likeresults\">$email_from_txt </td>";
		if($ctype!=2){
			print "<td valign=\"top\" class=\"likeresults\" width=\"50\">$used</td>
			<td valign=\"top\" class=\"likeresults\" width=\"55\">$unused</td>";
		}
		print "<td valign=\"top\" class=\"likeresults\" width=\"50\">$utot</td>
		<td valign=\"top\" class=\"likeresults\" style=\"background:#eeeeee;\" width=\"50\"><strong>$em_points</strong>";
		if($ctype==2){
			foreach($options as $p){
				if($pointsArray[$p]>0) {
                                    if($p=='half'){
                                        $pv='0.5';
                                        print '<br /><em>'.$pointsArray[$p].'x'.$pv.'</em>';
                                    }else{
                                       print '<br /><em>'.$pointsArray[$p].'x'.$p.'</em>'; 
                                    }
					
				}
			}
		}
		print "</td>
		<td valign=\"top\" class=\"likeresults\" style=\"background:#eeeeee;\" width=\"";
		if($ctype==2) print '60';
		else print '50';
		print "\">";
		if($ctype==2){
			print "<strong>$envelope_points</strong></td><td valign=\"top\" class=\"likeresults\" style=\"background:#eeeeee;\" width=\"60\"><strong>$ret_points</strong></td>";
		}
		else{
			print "<strong>$dm_points</strong></td>";
		}
		print "<td valign=\"top\" class=\"likeresults\" style=\"background:#eeeeee;\" width=\"50\"><strong>$tot_points_text</strong>";
		if($ctype==2){
			if($do_points_sent==1){
				if($tot_points_in>=$consumer_max_points && ("$start_yr-$start_mr"=='0000-00' || "$start_yr-$start_mr"=='2009-04')){//&& "$end_yr-$end_mr"=='0000-00'
					print '<br /><label><input type="checkbox" name="ps'.$panelist_id.'" value="1" />sent</label>';
				}
				if($points_sent>0) {
					print '<br /><em>(+'.$points_sent.')</em>';
				}
			}
		}
		print "</td>
		<td valign=\"top\" class=\"likeresults\" width=\"75\">$envelopes_curr";
		if($envelopes_curr>0){
			print " - <input type=\"text\" name=\"used_envelopes$panelist_id\" size=\"1\" style=\"width:30px;\" />";
		}
		if($envelopes_curr!=$max_envelopes){
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
                       if($p=='half'){
                           $pv='0.5';
                           print '<br /><em>'.$tot_pointsArray[$p].'x'.$pv.'</em>';
                       }else{
			print '<br /><em>'.$tot_pointsArray[$p].'x'.$p.'</em>';
                       }
		}
	}
}
print "</td>";
if($ctype==2){
	print "<td valign=\"top\" class=\"likeresults\" style=\"border-top:solid 1px #000000;border-bottom-color:white;\"><strong>$env_points_tot</strong></td><td valign=\"top\" class=\"likeresults\" style=\"border-top:solid 1px #000000;border-bottom-color:white;\"><strong>$ret_points_tot</strong></td>";
}
else{
	print "<td valign=\"top\" class=\"likeresults\" style=\"border-top:solid 1px #000000;border-bottom-color:white;\"><strong>$dm_tot</strong></td>";
}
print "<td valign=\"top\" class=\"likeresults\" style=\"border-top:solid 1px #000000;border-bottom-color:white;\"><strong>";
if($ctype==1) print '$';
print "$tot_tot</strong></td>
<td valign=\"top\" class=\"likeresults\" style=\"border-top:solid 1px #000000;border-bottom-color:white;\">&nbsp;</td>
<td valign=\"top\" class=\"likeresults\" style=\"border-top:solid 1px #000000;border-bottom-color:white;\"><input class=\"button\" type=\"submit\" name=\"updater\" value=\"Update\" /></td>
<td valign=\"top\" class=\"likeresults\" style=\"border-top:solid 1px #000000;border-bottom-color:white;\">&nbsp;</td>
</tr>";
print '</table></form>';
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