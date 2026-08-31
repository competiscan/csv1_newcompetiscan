<?php 
$TITLE = 'Competiscan Email';
require_once('panelist_top.php');

if(isset($_REQUEST['export'])) {
	$export = (int)$_REQUEST['export'];
}
else {
	$export = 0;
}

if(isset($_GET['sort'])) $_SESSION['sort'] = $sort = (int)$_GET['sort'];
else {
	if(isset($_SESSION['sort'])) $sort = $_SESSION['sort'];
	else $_SESSION['sort'] = $sort = 5;
}

if(isset($_REQUEST['e_assigned_admin_userID'])) {
	$_SESSION['e_assigned_admin_userID'] = (int) $_REQUEST['e_assigned_admin_userID'];
}

if(checkGroup(20) && isset($_GET['ctype'])) {
	$_SESSION['ctype'] = $ctype = (int)$_GET['ctype'];
	setcookie('competiscan_content_contact',$ctype,time()+(86400*364),$COOKIEPATH,$COOKIEDOMAIN);
}
else {
	if(isset($_SESSION['ctype'])) $ctype = $_SESSION['ctype'];
	elseif(isset($_COOKIE['competiscan_content_contact'])) $_SESSION['ctype'] = $ctype = $_COOKIE['competiscan_content_contact'];
	else $_SESSION['ctype'] = $ctype = 2;
}

if(isset($_GET['page'])) $_SESSION['page'] = $page = (int)$_GET['page'];
else {
	if(isset($_SESSION['page'])) $page = $_SESSION['page'];
	else $_SESSION['page'] = $page = 0;
}

if(isset($_GET['limshow'])) {
	$_SESSION['limshow'] = $limshow = (int)$_GET['limshow'];
	setcookie('competiscan_limshow',$limshow,time()+(86400*364),$COOKIEPATH,$COOKIEDOMAIN);
}
else {
	if(isset($_SESSION['limshow'])) $limshow = $_SESSION['limshow'];
	elseif(isset($_COOKIE['competiscan_limshow'])) $_SESSION['limshow'] = $limshow = $_COOKIE['competiscan_limshow'];
	else $_SESSION['limshow'] = $limshow = 10;
}

if(isset($_SESSION['hy'])) $hy = $_SESSION['hy'];
else $hy = '';

if(isset($_POST['sendmass']) && $_POST['muids']!=''){
	$muids = explode(',',$_POST['muids']);
	foreach($muids as $m_uid){
		if(isset($_POST["processed$m_uid"]) || isset($_POST["read$m_uid"]) || isset($_POST["panelist_score$m_uid"]) || isset($_POST["panelist_core$m_uid"])){
			$set = '';
			if(isset($_POST["processed$m_uid"]) && $_POST["processed$m_uid"]!=$_POST["old_processed$m_uid"]){
				$set .= "`deleted`='".$DRW->real_escape_string($_POST["processed$m_uid"])."'";
				if($ctype==2 && $_POST["processed$m_uid"]==0){
					//$set .= ",e_assigned_admin_userID=".getEAssignment('2');
				}
			}
			if(isset($_POST["read$m_uid"]) && $_POST["read$m_uid"]!=$_POST["old_read$m_uid"]){
				if($set!='') $set .= ', ';
				$set .= "`email_read`='".$DRW->real_escape_string($_POST["read$m_uid"])."'";
			}
			if(isset($_POST["panelist_score$m_uid"])){
				$panelist_score = intval($_POST["panelist_score$m_uid"]);
				$panelist_score_old = intval($_POST["old_panelist_score$m_uid"]);
				if($panelist_score!=$panelist_score_old){
					if($set!='') $set .= ', ';
					$set .= "`panelist_score`='".$DRW->real_escape_string($_POST["panelist_score$m_uid"])."'";
					/*if($ctype==2){
						$query = "SELECT SUM(`panelist_score`) FROM `cscan_email$hy` WHERE `muid`<>'".$DRW->real_escape_string($m_uid)."' AND `panelist_id`=".(int)$_POST["panelist_id$m_uid"];
						$query_result = $DRW->query($query,$DRW_read);
						$data2 = $DRW->fetch_row($query_result);
						$panelist_score_sum = (int)$data2[0];
						
						//this can be changed if panelist_id is saved with score instead of sugar_id
						$result2 = $DRW->query("SELECT SUM(ps_score) FROM cscan_panelist_score cs,cscan_panelists cp WHERE cp.`panelist_id`=".(int)$_POST["panelist_id$m_uid"]." AND cs.panelist_id=sugar_id",$DRW_read);
						$data2 = $DRW->fetch_row($result2);
						$ps_score = (int)$data2[0];
						
						$totalbefore = $panelist_score_sum+$ps_score;
						if($totalbefore>2000){
							$totalbefore = $totalbefore % 2000;
						}
						$total = $panelist_score+$totalbefore;
						
						if($totalbefore<2000 && $total>=2000){
							$result = $DRW->query("SELECT first_name,last_name,competi_id,phone, address, city, state, postalcode,email,alt_email FROM cscan_panelists WHERE `panelist_id`=".(int)$_POST["panelist_id$m_uid"],$DRW_read);
							$data = $DRW->fetch_row($result);
							$first_name = $data[0];
							$last_name = $data[1];
							$competi_id = $data[2];
							$phone = $data[3];
							$address = $data[4];
							$city = $data[5];
							$state = $data[6];
							$postalcode = $data[7];
							$email = $data[8];
							$alt_email = $data[9];
							
							if($competi_id!=''){
								$body = "Panelist 2,000 Points Reminder\n\n$first_name $last_name ($competi_id)\n$address\n$city, $state $postalcode\n\n$phone\n$email\n$alt_email";
								$subject = "Panelist 2,000 Points Reminder ($competi_id) ".date('m/d/Y');
								$to = 'maureen@competiscan.com';
								$headers = "From: $to\n";
								mail($to,$subject,$body,$headers);
							}
						}
					}*/
				}
			}
			if(isset($_POST["panelist_core$m_uid"])){
				$panelist_core = $_POST["panelist_core$m_uid"];
				$panelist_core_old = $_POST["old_panelist_core$m_uid"];
				if($panelist_core!=$panelist_core_old){
					if($set!='') $set .= ', ';
					$set .= "`panelist_core`='".$DRW->real_escape_string($_POST["panelist_core$m_uid"])."'";
				}
			}
			if($set!=''){
				$query = "UPDATE `cscan_email$hy` SET $set WHERE `muid`='".$DRW->real_escape_string($m_uid)."'";
				$DRW->query($query,$DRW_main);
				//uncomment to log user actions
				//$query = "REPLACE INTO cscan_email_check (muid,check_date,check_user,check_query) VALUES ('".$DRW->real_escape_string($m_uid)."',NOW(),{$AUTH_DATA['userID']},'".$DRW->real_escape_string($set." ($panelist_score_old=>$panelist_score)")."')";
				//$DRW->query($query,$DRW_main);
			}
		}
	}
	ob_end_clean();
	header("Location: {$_SERVER['PHP_SELF']}?updated=1");
	exit;
}

if(isset($_REQUEST['upp'])){
	exec("/usr/bin/php sugar_transfer.php > /dev/null 2>&1 &");
	sleep(3);
	ob_end_clean();
	header("Location: {$_SERVER['PHP_SELF']}");
	exit;
}
if(isset($_REQUEST['checkmail'])){
	exec("/usr/bin/php imap_back.php > /dev/null 2>&1 &");
	sleep(3);
	ob_end_clean();
	header("Location: {$_SERVER['PHP_SELF']}");
	exit;
}

if(checkGroup(20)){
	print "<div style=\"margin-bottom:4px;\"><form action=\"{$_SERVER['PHP_SELF']}\" style=\"display:inline;\" method=\"get\"><input class=\"button\" type=\"submit\" name=\"checkmailsub\" value=\"Check Mail\" /> &nbsp; <input class=\"button\" type=\"submit\" name=\"report\" value=\"Get Report\" onclick=\"document.location.href='panelist_report_month.php'; return false;\" /> &nbsp; <input class=\"button\" type=\"submit\" name=\"upp\" value=\"Update Panelists\" onclick=\"document.location.href='{$_SERVER['PHP_SELF']}?upp=1'; return false;\" />";
	if($ctype==2){
		print " &nbsp; <input class=\"button\" type=\"submit\" name=\"upp\" value=\"Last Panelist ID\" onclick=\"winPopScore('consumer_inc.php'); return false;\" />";
	}
	print "<input type=\"hidden\" name=\"checkmail\" value=\"1\" /></form>";
	if(isset($_GET['updated'])){
		print ' &nbsp; &nbsp; <span class="error">Updated</span>';
	}
	print "</div>";
}

echo '<div><div style="float:left;">';
$n = 0;
$cwhere = '';
if(isset($_SESSION['e_assigned_admin_userID']) && $_SESSION['e_assigned_admin_userID']!=0) {
	$e_assigned_admin_userID = $_SESSION['e_assigned_admin_userID'];
	
	$cwhere .= " AND e_assigned_admin_userID=".$_SESSION['e_assigned_admin_userID'];
}
else {
	$e_assigned_admin_userID = 0;
}
$last  = count($contactType) - 1;
foreach($contactType as $contact_type_m_c=>$contact_type_m_cTitle){
	if(checkGroup(20) || $n==2){
		if($ctype==$n){
			if($n>0) $cwhere .= " AND `contact_type_m_c`='{$contact_type_m_c}'";
			print "<span class=\"headings\">$contact_type_m_cTitle</span>";
		}
		else print "<a href=\"{$_SERVER['PHP_SELF']}?ctype=$n\" class=\"bluelink\">$contact_type_m_cTitle</a>";
		if($n!=$last) print ' &nbsp; | &nbsp; ';
	}
	$n++;
}
echo '</div>';

$searchid = 0;
$searchsubj = 0;
$searchsender = 0;
$searchbody = 0;
$searchtext = '';
$searchtext_id = '';
$searchtext_subj = '';
$searchtext_body = '';
$searchtext_sender = '';
$searchtext_sender_email = '';

$searchstate = 0;
$panelist_ids = '';
$searchownbiz = -1;
$mtypes = array(0,1);
$noread = 0;
if($ctype==1) {
	$monthago = mktime(0,0,0,(int)date('n')-1,(int)date('j'),(int)date('Y'));
	$start_m = date('m',$monthago);
	$start_d = date('d',$monthago);
	$start_y = date('Y',$monthago);
	$end_m = date('m');
	$end_d = date('d');
	$end_y = date('Y');
}
else{
	$start_m = '00';
	$start_d = '00';
	$start_y = '0000';
	$end_m = '00';
	$end_d = '00';
	$end_y = '0000';
}
$readTypes = array(0=>'Unread',1=>'Read');
$panelist_core_options = array('C'=>'C','EN'=>'EN','N'=>'N','RL'=>'RL','TC'=>'TC','TL'=>'TL');
$panelist_core_search = array();

if(checkGroup(20) && isset($_POST['sendsearch'])){
	$DRW->query("DELETE FROM cscan_search_email WHERE uid={$AUTH_DATA['userID']}",$DRW_main);
	if(!isset($_POST['clear'])){
		$searchtext_id = trim($_POST['searchtext_id']);
		$searchtext_subj = trim($_POST['searchtext_subj']);
		$searchtext_body = trim($_POST['searchtext_body']);
		$searchtext_sender = trim($_POST['searchtext_sender']);
                $searchtext_sender_email = trim($_POST['searchtext_sender_email']);
		$searchstate = (int)$_POST['searchstate'];
		$panelist_ids = trim($_POST['panelist_ids']);
		$searchownbiz = (int)$_POST['searchownbiz'];
		if(isset($_POST['mtypes'])) $mtypes = $_POST['mtypes'];
		if(isset($_POST['noread'])) $noread = (int)$_POST['noread'];
		foreach($panelist_core_options as $val=>$option){
			if(isset($_POST[$val])){
				$panelist_core_search[] = $val;
			}
		}
		$start_m = $_POST['start_m'];
		$start_d = $_POST['start_d'];
		$start_y = $_POST['start_y'];
		$end_m = $_POST['end_m'];
		$end_d = $_POST['end_d'];
		$end_y = $_POST['end_y'];
		$hy = $_POST['hy'];
	}
	$_SESSION['searchtext_id'] = $searchtext_id;
	$_SESSION['searchtext_subj'] = $searchtext_subj;
	$_SESSION['searchtext_body'] = $searchtext_body;
	$_SESSION['searchtext_sender'] = $searchtext_sender;
        $_SESSION['searchtext_sender_email'] = $searchtext_sender_email;
	$_SESSION['searchstate'] = $searchstate;
	$_SESSION['panelist_ids'] = $panelist_ids;
	$_SESSION['searchownbiz'] = $searchownbiz;
	$_SESSION['mtypes'] = $mtypes;
	$_SESSION['noread'] = $noread;
	$_SESSION['panelist_core_search'] = $panelist_core_search;
	$_SESSION['start_m'] = $start_m;
	$_SESSION['start_d'] = $start_d;
	$_SESSION['start_y'] = $start_y;
	$_SESSION['end_m'] = $end_m;
	$_SESSION['end_d'] = $end_d;
	$_SESSION['end_y'] = $end_y;
	$_SESSION['hy'] = $hy;
	
	setcookie('competiscan_esearch3',implode(';',$mtypes).",$noread,".implode(';',$panelist_core_search),time()+(86400*364),$COOKIEPATH,$COOKIEDOMAIN);
}
else{
	if(isset($_COOKIE['competiscan_esearch3'])) {
		$temp_cookie = explode(',', $_COOKIE['competiscan_esearch3']);
		list($mtypes_tmp,$noread,$panelist_core_search_tmp) = $temp_cookie;
		if(!empty($mtypes_tmp)){
			$mtypes = explode(';',$mtypes_tmp);
		}
		if(!empty($panelist_core_search_tmp)){
			$panelist_core_search = explode(';',$panelist_core_search_tmp);
		}
	}
	if(isset($_SESSION['searchtext_id'])) $searchtext_id = $_SESSION['searchtext_id'];
	if(isset($_SESSION['searchtext_subj'])) $searchtext_subj = $_SESSION['searchtext_subj'];
	if(isset($_SESSION['searchtext_body'])) $searchtext_body = $_SESSION['searchtext_body'];
	if(isset($_SESSION['searchtext_sender'])) $searchtext_sender = $_SESSION['searchtext_sender'];
	if(isset($_SESSION['searchtext_sender_email'])) $searchtext_sender_email = $_SESSION['searchtext_sender_email'];
        if(isset($_SESSION['searchstate'])) $searchstate = $_SESSION['searchstate'];
	if(isset($_SESSION['panelist_ids'])) $panelist_ids = $_SESSION['panelist_ids'];
	if(isset($_SESSION['searchownbiz'])) $searchownbiz = $_SESSION['searchownbiz'];
	if(isset($_SESSION['mtypes'])) $mtypes = $_SESSION['mtypes'];
	if(isset($_SESSION['noread'])) $noread = $_SESSION['noread'];
	if(isset($_SESSION['panelist_core_search'])) $panelist_core_search = $_SESSION['panelist_core_search'];
	if(isset($_SESSION['start_m'])) $start_m = $_SESSION['start_m'];
	if(isset($_SESSION['start_d'])) $start_d = $_SESSION['start_d'];
	if(isset($_SESSION['start_y'])) $start_y = $_SESSION['start_y'];
	if(isset($_SESSION['end_m'])) $end_m = $_SESSION['end_m'];
	if(isset($_SESSION['end_d'])) $end_d = $_SESSION['end_d'];
	if(isset($_SESSION['end_y'])) $end_y = $_SESSION['end_y'];
	if(isset($_SESSION['hy'])) $hy = $_SESSION['hy'];
}

$used = '';
if(checkGroup(20)){
	$dels = array();
	foreach($mtypes as $m){
		$dels[] = '`deleted`='.$m;
	}
	if(count($dels)>0){
		$used .= ' AND ('.implode(' OR ',$dels).')';
	}
	if($noread) {
		$used .= ' AND `email_read`<>1';
	}
	if(count($panelist_core_search)>0){
		$used .= " AND (panelist_core='".implode("' OR panelist_core='",$panelist_core_search)."')";
	}
}
else{
	$used .= ' AND `deleted`=0 AND `panelist_score`>0';
}

$join = '';
if($searchtext_id!='' || $searchtext_sender!='' || $searchtext_sender_email!='' || $searchtext_body!='' || $searchtext_subj!=''){
	//$searchtext_like = mysqlLike($searchtext);
	$swhere = '';
	if($searchtext_id!=''){
		if($swhere!='') $swhere .= ' AND ';
		$swhere .= "ce.`muid`='".$DRW->real_escape_string($searchtext_id)."'";
	}
	//if($searchsubj){
	//	if($swhere!='') $swhere .= ' OR ';
		//$swhere .= "MATCH (`email_subject`) AGAINST ('".$DRW->real_escape_string($searchtext_like)."')";
	//	$swhere .= "`email_subject` LIKE '%".$searchtext_like."%'";
	//}
	if($searchtext_sender!=''){
		//if($swhere!='') $swhere .= ' OR ';
		//$swhere .= "MATCH (`email_from`) AGAINST ('".$DRW->real_escape_string($searchtext_like)."')";
		//$swhere .= "`email_from_one` LIKE '%".$searchtext_like."%'";
		$vs = explode(',',$searchtext_sender);
		$ors = array();
		foreach($vs as $v){
			$v = trim($v);
			if(!empty($v)){
				if($swhere!='') $swhere .= ' AND ';
				$v = mysqlLike($v);
				$swhere .= "`email_from_one` LIKE '".$v."%'";
			}
		}
	} 
        //###############Add Sender Email Field##################
        if($searchtext_sender_email!=''){
            $vs_email = explode(',',$searchtext_sender_email);
            $ors_email = array();
            foreach($vs_email as $v_email){
                    $v_email = trim($v_email);
                    if(!empty($v_email)){
                            if($swhere!='') $swhere .= ' AND ';
                            $v_email = mysqlLike($v_email);
                            $swhere .= "`from_sent_email_address` LIKE '".$v_email."%'";
                    }
            }
	}
        
	if($searchtext_body!='' || $searchtext_subj!=''){
		//if($swhere!='') $swhere .= ' OR ';
		//$swhere .= "MATCH (`cettext`) AGAINST ('".$DRW->real_escape_string($searchtext_like)."')";
		//$swhere .= "`cettext` LIKE '%".$searchtext_like."%'";
		//$join = ' LEFT JOIN `cscan_email_text` ct ON(ce.`muid`=ct.`muid`)';
		$search_id = session_id();
		$count_save_sql = "SELECT SQL_NO_CACHE COUNT(*) FROM cscan_search_email WHERE sid='$search_id' AND uid={$AUTH_DATA['userID']}";
		$rs = $DRW->query($count_save_sql,$DRW_read);
		$data = $DRW->fetch_row($rs);
		$numrow = (int) $data[0];
		if($numrow==0){
			$s = startSphinx('cetactive');
			if(strpos(__FILE__,'demo')!==false){
				$inds = 'base_index_demo_e'.$hy;
			}
			else{
				if(!empty($hy)){
					$inds = 'base_index_prod_e'.$hy;
				}
				else{
					$inds = 'base_index_prod_e,delta_index_prod_e';
				}
			}
			$ps_body = trim(parseSphinx($s,$searchtext_body));
			$ps_subj = trim(parseSphinx($s,$searchtext_subj));
			if($ps_body!='' || $ps_subj!=''){
				$currcount = 0;
				$step = $total = 1000;
				$s->setLimits(0,1,1);
				$ps = '';
				if($ps_body!=''){
					$ps .= ' @cettext '.$ps_body;
				}
				elseif($ps_body==''){
					$ps .= ' @email_subject '.$ps_subj;
				}
				$result = $s->query($ps,$inds);
				if(isset($result['matches'])){
					$total = (float)$result['total_found'];
					$count = 0;
					$minID = 0;
					$count_save_sql = "SELECT MAX(cetid) FROM cscan_email_text$hy";
					$rs = $DRW->query($count_save_sql,$DRW_read);
					$data = $DRW->fetch_row($rs);
					$maxID = $data[0];
					for($offset=0;$offset<=$maxID;$offset+=$step){
						$s = startSphinx('cetactive');
						$s->setLimits(0,$step,$step);
						$s->setIDRange($minID+1,$maxID);
						$result = $s->query($ps,$inds);
						if(isset($result['matches'])){
							foreach($result['matches'] as $dts_id=>$match){
								$query = "REPLACE INTO cscan_search_email (uid,sid,muid) VALUES ({$AUTH_DATA['userID']},'$search_id',{$match['attrs']['muid']})";
								$DRW->query($query,$DRW_main);
								
								$minID = $dts_id;
								$currcount++;
							}
							if($currcount>=$total){
								break;
							}
						}
						$err = $s->getLastError();
						$war = $s->getLastWarning();
						if(!empty($err) || !empty($war)){
							//echo "$err | $war"; exit;
							break;
						}
						// note that total_found using setLimits(0,1,1) is not always the same as without limits (bug in Sphinx?)
						if(!isset($result['matches'])){
							break;
						}
					}
				}
			}
		}
		$join .= ' JOIN cscan_search_email se ON(se.sid=\''.$search_id.'\' AND se.uid='.$AUTH_DATA['userID'].' AND ce.muid=se.muid)';
	}
	if($swhere!='') $cwhere .= " AND ($swhere)";
}
if($searchstate!=0 || $searchownbiz!=-1 || !empty($panelist_ids)){
	$join .= ' JOIN cscan_panelists pp ON(ce.panelist_id=pp.panelist_id)';
	if($searchstate!=0){
		$cwhere .= " AND stateID=$searchstate";
	}
	if($searchownbiz!=-1){
		$cwhere .= " AND ownbiz=$searchownbiz";
	}
	if(!empty($panelist_ids)){
		$vs = explode(',',$panelist_ids);
		$ors = array();
		foreach($vs as $v){
			$v = trim($v);
			if(!empty($v)){
				$v = $DRW->real_escape_string($v);
				$ors[] = "(competi_id LIKE '".$v."%')";
			}
		}
		$cwhere .= " AND (".implode(' OR ',$ors).")";
	}
}
if($start_y!='0000'){
	$cwhere .= " AND `email_date`>='".$DRW->real_escape_string("$start_y-$start_m-$start_d")." 00:00:00'";
	$cwhere .= " AND `email_date`<='".$DRW->real_escape_string("$end_y-$end_m-$end_d")." 23:59:59'";
}

?>
<div style="float:right;"><form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="changeadmin" style="display:inline;"><strong class="bodytext">Assigned User:</strong> <select class="combo_box" name="e_assigned_admin_userID" onchange="document.changeadmin.submit();"><option value="0">All</option>
<?php 
$useroptions = array();
$sql = "select userID,userName,is_email_assign_queue,is_email_assign_queue2 from cscan_admin_users WHERE user_status=1 ORDER BY userName";
$rs = $DRW->query($sql,$DRW_read);
while($row = $DRW->fetch_row($rs)) {
	print "<option value = \"$row[0]\"";
	if($row[0]==$e_assigned_admin_userID) print " selected=\"selected\"";
	print ">";
	if($row[2]) print '(p) ';
	if($row[3]) print '(c) ';
	print "$row[1]</option>";
	$useroptions[$row[0]] = $row[1];
}
?></select>
</form>
</div>
</div>
<div style="clear:both;height:5px;">&nbsp;</div>
<?php
print "<form name=\"masser\" method=\"post\" action=\"{$_SERVER['PHP_SELF']}\" onsubmit=\"do_mupdate(); return false;\">
<div style=\"margin:0px;padding:0px;border:solid 1px #0055E3;\">
<table width=\"100%\" border=\"0\" cellpadding=\"4\" cellspacing=\"0\" class=\"likeresults\">";
print "<tr><td style=\"background:#0055E3;color:#ffffff;\" class=\"text\" valign=\"bottom\">";
if($sort!=7) print " <a href=\"{$_SERVER['PHP_SELF']}?sort=7\" class=\"topLinks\">ID</a>";
else {
	print '<span style="text-decoration:underline;" class="topLinks">ID</span>';
	$orderby = ' ORDER BY `muid` DESC';
}
print "</td>";
print "<td style=\"background:#0055E3;color:#ffffff;\" class=\"text\" valign=\"bottom\"><span class=\"topLinks\">Status</span></td><td style=\"background:#0055E3;color:#ffffff;\" class=\"text\" valign=\"bottom\">";
if($sort!=2) print " <a href=\"{$_SERVER['PHP_SELF']}?sort=2\" class=\"topLinks\">Subject</a>";
else {
	print '<span style="text-decoration:underline;" class="topLinks">Subject</span>';
	$orderby = ' ORDER BY `email_subject` ASC,`email_date` DESC';
}
print '</td><td style="background:#0055E3;color:#ffffff;" class="text" valign="bottom"><span class="topLinks">Files</span></td><td style="background:#0055E3;color:#ffffff;" class="text" valign="bottom">';
if($sort!=3) print " <a href=\"{$_SERVER['PHP_SELF']}?sort=3\" class=\"topLinks\">Panelist Email</a>";
else {
	print '<span style="text-decoration:underline;" class="topLinks">Panelist Email</span>';
	$orderby = ' ORDER BY `email_from_one` ASC,`email_date` DESC';
}
print "</td><td style=\"background:#0055E3;color:#ffffff;\" class=\"text\" valign=\"bottom\">";
if($sort!=9) print " <a href=\"{$_SERVER['PHP_SELF']}?sort=9\" class=\"topLinks\">Flag</a>";
else {
	print '<span style="text-decoration:underline;" class="topLinks">Flag</span>';
	$orderby = ' ORDER BY `panelist_core` DESC,`email_date` DESC';
}
print "</td><td style=\"background:#0055E3;color:#ffffff;\" class=\"text\" valign=\"bottom\">";
if($sort!=4) print " <a href=\"{$_SERVER['PHP_SELF']}?sort=4\" class=\"topLinks\">Points</a>";
else {
	print '<span style="text-decoration:underline;" class="topLinks">Points</span>';
	$orderby = ' ORDER BY `panelist_score` DESC,`email_date` DESC';
}
print "</td><td style=\"background:#0055E3;color:#ffffff;\" class=\"text\" valign=\"bottom\">";
if($sort!=5) print " <a href=\"{$_SERVER['PHP_SELF']}?sort=5\" class=\"topLinks\">Date</a>";
else {
	print '<span style="text-decoration:underline;" class="topLinks">Date</span>';
	$orderby = ' ORDER BY `email_date` DESC';
}
print "</td><td style=\"background:#0055E3;color:#ffffff;\" class=\"text\" valign=\"bottom\">";
if($sort!=8) print " <a href=\"{$_SERVER['PHP_SELF']}?sort=8\" class=\"topLinks\">Mark</a>";
else {
	print '<span style="text-decoration:underline;" class="topLinks">Mark</span>';
	$orderby = ' ORDER BY `email_read` ASC,`email_date` DESC';
}
print '</td></tr>';

$q = " FROM `cscan_email$hy` ce$join WHERE 1=1$used$cwhere";

$count_result = $DRW->query("SELECT SQL_NO_CACHE COUNT(DISTINCT ce.`muid`) $q",$DRW_read);
$data = $DRW->fetch_row($count_result);
$rows = $data[0];
list($limittext,$pagingtext) = showPaging($_SERVER['PHP_SELF'],$rows,$page,$limshow);

if($export){
	@ob_end_clean();
	header("Content-Type: text/plain");//application/excel,application/vnd.ms-excel
	header("Content-Disposition: attachment; filename=Competiscan_Email_Export_".date('Y-m-d').".csv");
	header("Pragma: no-cache");
	header("Expires: 0");
	echo "Link,ID,Status,Subject,Files,Panelist Email,Sender Name,Sender Email,Sender Date,Flag,Points,Date,Mark\n";
}

$muids = array();
$hiddentext = '';
if($rows>0){
	$query = "SELECT SQL_NO_CACHE DISTINCT ce.`muid`,DATE_FORMAT(`email_date`,'%m/%d/%y<br />%l:%i %p'),`email_to`,`email_from`,`email_subject`,`from_sent_name`,`from_sent_email_address`,`from_sent_date_format`,`contact_type_m_c`,`deleted`,`panelist_score`,`email_read`,`email_from_one`,ce.`panelist_id`,panelist_core,email_date $q$orderby";
	
        if(!$export){
		$query .= $limittext;
                
	}
       //echo $query;
	$query_result = $DRW->query($query,$DRW_read);
	while($data = $DRW->fetch_row($query_result)){
		$muid = $data[0];
		$email_date = $data[1];
		$email_to = $data[2];
		$email_from_txt = $email_from = $data[3];
		$email_subject = $data[4];
                
                $from_sent_name = html_entity_decode($data[5]);
		$from_sent_email_address = html_entity_decode($data[6]);
		$from_sent_date_format = $data[7];
                
		$contact_type_m_c = $data[8];
		$deleted = $data[9];
		$panelist_score = $data[10];
		$email_read = $data[11];
		$email_from_one = $data[12];
		$panelist_id = $data[13];
		$panelist_core = $data[14];
		$email_date_uf = $data[15];
		
		$result = $DRW->query("SELECT SQL_NO_CACHE COUNT(*) FROM cscan_email_file$hy WHERE muid=$muid",$DRW_read);
		$data2 = $DRW->fetch_row($result);
		$count = $data2[0];
		
		$result = $DRW->query("SELECT SQL_NO_CACHE COUNT(*) FROM cscan_product_email WHERE muid=$muid AND isTmp=0",$DRW_read);
		$data2 = $DRW->fetch_row($result);
		$countp = $data2[0];
		
		if($count>0) $attachment = 'Yes';
		else $attachment = 'No';
		
		if($panelist_id!=0){
			$result = $DRW->query("SELECT sugar_id,first_name,last_name,competi_id,email,alt_email,panelist_id 
				FROM cscan_panelists WHERE panelist_id=$panelist_id",$DRW_read);
			$data2 = $DRW->fetch_row($result);
			if($data2[0]!=''){
				$id = $data2[0];
				$first_name = $data2[1];
				$last_name = $data2[2];
				$competi_id = $data2[3];
				$email1 = trim($data2[4]);
				$email2 = trim($data2[5]);
				
				$email_from = $first_name.' '.$last_name;
				if($competi_id!='') $email_from .= ' ('.$competi_id.')';
				$email_from_txt = $email_from.' <'.$email1.'>';
				$email_from = htmlspecialchars($email_from);
				if(checkGroup(20)){
					$email1 = "<a href=\"http://crm.competiscan.com/index.php?action=DetailView&amp;module=Contacts&amp;record=$id\" class=\"bluelink\" target=\"_blank\">$email1</a>";
				}
				$email_from .= ' &lt;'.$email1.'&gt;';
			}
			elseif(!$export) $email_from = htmlspecialchars($email_from);
		}
		elseif(!$export) $email_from = htmlspecialchars($email_from);
		
		if($export){
			if($countp==0 && isset($messageTypes[$deleted])){
				$mt = $messageTypes[$deleted];
			}
			else{
				$mt = 'Used';
			}
			if(isset($panelist_core_options[$panelist_core])){
				$pc = $panelist_core_options[$panelist_core];
			}
			else{
				$pc = '';
			}
			if(isset($readTypes[$email_read])){
				$rt = $readTypes[$email_read];
			}
			else{
				$rt = '';
			}
			//showallmessage.php?muid=$muid&hy=$hy
			echo csvExcape("http://www.competiscan.com/email.php?muid=$muid&hy=$hy").','.csvExcape($muid).','.csvExcape($mt).','.csvExcape(utf8_decode($email_subject)).','.csvExcape($attachment).','.csvExcape($email_from_txt).','.csvExcape($from_sent_name).','.csvExcape($from_sent_email_address).','.csvExcape($from_sent_date_format).','.csvExcape($pc).','.csvExcape($panelist_score).','.csvExcape($email_date_uf).','.csvExcape($rt)."\n";
		}
		else{
			print "<tr><td valign=\"top\" class=\"bodytext\">$muid</td><td valign=\"top\" class=\"bodytext\" nowrap=\"nowrap\">";
			$muids[] = $muid;
			if($countp==0){
				print "<select name=\"processed$muid\" size=\"1\">";
				foreach($messageTypes as $key=>$value){
					if($value=='') $value = '&nbsp;';
					print "<option value=\"$key\"";
					if($deleted==$key) print ' selected="selected"';
					print ">$value</option>";
				}
				print "</select><a href=\"#\" onclick=\"do_mupdate(); return false;\" title=\"Update\"><img src=\"images/arrow.gif\" border=\"0\" alt=\"Update\" width=\"13\" height=\"13\" /></a>";
				$hiddentext .= "<input type=\"hidden\" name=\"old_processed$muid\" value=\"".htmlspecialchars($deleted,ENT_QUOTES)."\" />";
			}
			else print 'Used';
			print "</td><td valign=\"top\" class=\"bodytext\">";
			if(!$email_read) print '<strong>';
			print "<a href=\"email.php?muid=$muid&amp;hy=$hy\" class=\"bluelink\" name=\"muid$muid\">".htmlspecialchars($email_subject)."</a>";
			if(!$email_read) print '</strong>';
			print "&nbsp; <em>[<a href=\"#\" onclick=\"winPopMessage('showallmessage.php?muid=$muid&amp;hy=$hy'); return false;\" class=\"bluelink\">Peek</a>]</em>";
			print "</td><td valign=\"top\" class=\"bodytext\">$attachment</td><td valign=\"top\" class=\"bodytext\">$email_from</td>
			<td valign=\"top\" class=\"bodytext\" nowrap=\"nowrap\"><select name=\"panelist_core$muid\" size=\"1\"><option value=\"\">&nbsp;</option>";
			foreach($panelist_core_options as $val=>$option){
				print "<option value=\"$val\"";
				if($val==$panelist_core) print ' selected="selected"';
				print ">$option</option>";
			}
			$hiddentext .= "<input type=\"hidden\" name=\"old_panelist_core$muid\" value=\"".$panelist_core."\" />";
			print "</select><a href=\"#\" onclick=\"do_mupdate(); return false;\" title=\"Update\"><img src=\"images/arrow.gif\" border=\"0\" alt=\"Update\" width=\"13\" height=\"13\" /></a></td>
			<td valign=\"top\" class=\"bodytext\" nowrap=\"nowrap\"><select name=\"panelist_score$muid\" size=\"1\" onchange=\"mark_points_changed();\">";
			//also change in panelist_score.php
			if($contact_type_m_c=='cons_panelist') $options = array('0'=>'0','2'=>'2','5'=>'5','10'=>'10','50'=>'50');
			else $options = array('0'=>'0','1'=>'1');
			foreach($options as $val=>$option){
				print "<option value=\"$val\"";
				if($val==$panelist_score) print ' selected="selected"';
				print ">$option</option>";
			}
			$hiddentext .= "<input type=\"hidden\" name=\"old_panelist_score$muid\" value=\"".intval($panelist_score)."\" /><input type=\"hidden\" name=\"panelist_id$muid\" value=\"".$panelist_id."\" />";
			print "</select><a href=\"#\" onclick=\"do_mupdate(); return false;\" title=\"Update\"><img src=\"images/arrow.gif\" border=\"0\" alt=\"Update\" width=\"13\" height=\"13\" /></a></td><td valign=\"top\" class=\"bodytext\">$email_date</td>";
			print "<td valign=\"top\" class=\"bodytext\" nowrap=\"nowrap\">";
			print "<select name=\"read$muid\" size=\"1\">";
			foreach($readTypes as $key=>$value){
				print "<option value=\"$key\"";
				if($email_read==$key) print ' selected="selected"';
				print ">$value</option>";
			}
			print "</select><a href=\"#\" onclick=\"do_mupdate(); return false;\" title=\"Update\"><img src=\"images/arrow.gif\" border=\"0\" alt=\"Update\" width=\"13\" height=\"13\" /></a></td>";
			$hiddentext .= "<input type=\"hidden\" name=\"old_read$muid\" value=\"".htmlspecialchars($email_read,ENT_QUOTES)."\" />";
			print "</tr>";
		}
	}
}
elseif(!$export) print "<tr><td colspan=\"8\" class=\"bodytext\"><i>None</i></td></tr>";
if($export){
	exit;
}
print "</table>
</div>";
if(count($muids)>0) print "<div style=\"margin-top:4px;\"><input class=\"button\" type=\"submit\" name=\"update\" value=\"Update\" /> &nbsp; <input class=\"button\" type=\"submit\" name=\"export_b\" value=\"Export All to CSV\" onclick=\"document.location.href='{$_SERVER['PHP_SELF']}?export=1';return false;\" /></div>"; 
print "$hiddentext<input type=\"hidden\" name=\"sendmass\" value=\"1\" /><input type=\"hidden\" name=\"muids\" value=\"".implode(',',$muids)."\" /></form>";

print "<div style=\"margin-top:4px;\">$pagingtext</div>";

if(checkGroup(20)){
	print "<div style=\"margin-top:4px;padding:2px;background-color:#E8E8FF;\">";
	print "<form name=\"searcher\" method=\"post\" action=\"{$_SERVER['PHP_SELF']}?page=0\"><table border=\"0\" cellpadding=\"4\" cellspacing=\"2\">";
	
	
	print "<tr><td class=\"bodytext\"><strong>Search</strong></td><td><input type=\"text\" name=\"searchtext\" value=\"".htmlspecialchars($searchtext,ENT_QUOTES)."\" size=\"60\" /> <input class=\"button\" type=\"submit\" name=\"search\" value=\"Search\" /> <input class=\"button\" type=\"submit\" name=\"clear\" value=\"Clear\" /></td></tr>";
	print "<tr><td>&nbsp;</td><td class=\"bodytext\"><label><input type=\"checkbox\" name=\"searchid\" value=\"1\"";
	if($searchid) print ' checked="checked"';
	print " />ID</label> &nbsp; <label><input type=\"checkbox\" name=\"searchsubj\" value=\"1\"";
	if($searchsubj) print ' checked="checked"';
	print " />Subject</label> &nbsp; <label><input type=\"checkbox\" name=\"searchbody\" value=\"1\"";
	if($searchbody) print ' checked="checked"';
	print " />Body</label> &nbsp; <label><input type=\"checkbox\" name=\"searchsender\" value=\"1\"";
	if($searchsender) print ' checked="checked"';
	print " />Panelist Email</label></td></tr>";
	
	
	print "<tr><td class=\"bodytext\"><strong>ID</strong></td><td><input type=\"text\" name=\"searchtext_id\" value=\"".htmlspecialchars($searchtext_id,ENT_QUOTES)."\" size=\"60\" /></td></tr>";
	print "<tr><td class=\"bodytext\"><strong>Subject</strong></td><td><input type=\"text\" name=\"searchtext_subj\" value=\"".htmlspecialchars($searchtext_subj,ENT_QUOTES)."\" size=\"60\" /></td></tr>";
	print "<tr><td class=\"bodytext\"><strong>Body</strong></td><td><input type=\"text\" name=\"searchtext_body\" value=\"".htmlspecialchars($searchtext_body,ENT_QUOTES)."\" size=\"60\" /></td></tr>";
	print "<tr><td class=\"bodytext\"><strong>Panelist Email</strong></td><td><input type=\"text\" name=\"searchtext_sender\" value=\"".htmlspecialchars($searchtext_sender,ENT_QUOTES)."\" size=\"60\" /></td></tr>";
	//#################### Add Sender Email Field##################
        print "<tr><td class=\"bodytext\"><strong>Sender Email</strong></td><td><input type=\"text\" name=\"searchtext_sender_email\" value=\"".htmlspecialchars($searchtext_sender_email,ENT_QUOTES)."\" size=\"60\" /></td></tr>";
       
        print "<tr><td>&nbsp;</td><td class=\"bodytext\"><table border=\"0\" cellpadding=\"4\" cellspacing=\"2\">";
	print "<tr><td class=\"bodytext\">From</td><td><select name=\"start_m\" size=\"1\" class=\"textinput\" onchange=\"checkStart();\"><option value=\"00\">&nbsp;</option>";
	$month_name = array('01'=>"January",'02'=>"February",'03'=>"March",'04'=>"April",'05'=>"May",'06'=>"June",'07'=>"July",'08'=>"August",'09'=>"September",'10'=>"October",'11'=>"November",'12'=>"December");
	foreach($month_name as $key=>$value){
		print "<option value=\"$key\"";
		if($key==$start_m) print " selected=\"selected\"";
		print ">$value ($key)</option>";
	}
	print "</select> <select name=\"start_d\" size=\"1\" class=\"textinput\" onchange=\"checkStart();\"><option value=\"00\">&nbsp;</option>";
	for($i=1;$i<=31;$i++){
		$day = str_pad($i,2,'0',STR_PAD_LEFT);
		print "<option value=\"$day\"";
		if($day==$start_d) print " selected=\"selected\"";
		print ">$day</option>";
	}
	$start_year = 2007;
	$to_year = (int)date('Y');
	print "</select> <select name=\"start_y\" size=\"1\" class=\"textinput\" onchange=\"checkStart();\"><option value=\"0000\">&nbsp;</option>";
	for($i=$start_year;$i<=$to_year;$i++){
		print "<option value=\"$i\"";
		if($i==$start_y) print " selected=\"selected\"";
		print ">$i</option>";
	}
	print "</select></td></tr>
	<tr><td class=\"bodytext\">To</td><td><select name=\"end_m\" size=\"1\" class=\"textinput\" onchange=\"checkStart();\"><option value=\"00\">&nbsp;</option>";
	foreach($month_name as $key=>$value){
		print "<option value=\"$key\"";
		if($key==$end_m) print " selected=\"selected\"";
		print ">$value ($key)</option>";
	}
	print "</select> <select name=\"end_d\" size=\"1\" class=\"textinput\" onchange=\"checkStart();\"><option value=\"00\">&nbsp;</option>";
	for($i=1;$i<=31;$i++){
		$day = str_pad($i,2,'0',STR_PAD_LEFT);
		print "<option value=\"$day\"";
		if($day==$end_d) print " selected=\"selected\"";
		print ">$day</option>";
	}
	print "</select> <select name=\"end_y\" size=\"1\" class=\"textinput\" onchange=\"checkStart();\"><option value=\"0000\">&nbsp;</option>";
	for($i=$start_year;$i<=$to_year;$i++){
		print "<option value=\"$i\"";
		if($i==$end_y) print " selected=\"selected\"";
		print ">$i</option>";
	}
	print "</select></td></tr>";
	print "</table></td></tr>";
	print "<tr><td>&nbsp;</td><td class=\"bodytext\">";
	foreach($messageTypes as $k=>$v)	{
		if($v==''){
			$v = 'Blank';
		}
		print "<label><input type=\"checkbox\" name=\"mtypes[]\" value=\"$k\" ";
		if(in_array($k,$mtypes)) print ' checked="checked"';
		print " />Show $v</label> &nbsp; ";
	}
	print "</td></tr>";
	print "<tr><td>&nbsp;</td><td class=\"bodytext\"><label><input type=\"checkbox\" name=\"noread\" value=\"1\" ";
	if($noread) print ' checked="checked"';
	print " />Hide Marked Read</label></td></tr>";
	print "<tr><td>&nbsp;</td><td class=\"bodytext\">Flag:";
	foreach($panelist_core_options as $val=>$option){
		print " &nbsp; <label><input type=\"checkbox\" name=\"$val\" value=\"1\" ";
		if(in_array($val,$panelist_core_search)) {
			print ' checked="checked"';
		}
		print " />$option</label>";
	}
	print "</td></tr>";
	print '<tr><td>&nbsp;</td><td class="bodytext">Panelists <input type="text" name="panelist_ids" size="70" value="'.htmlspecialchars($panelist_ids,ENT_COMPAT).'" /></td></tr>';
	print "<tr><td>&nbsp;</td><td class=\"bodytext\"><select name=\"searchstate\"><option value=\"0\">State</option>";
	$sql = "SELECT stateID, stateCode FROM cscan_state ORDER BY stateCode";
	$rs = $DRW->query( $sql,$DRW_read );
	while($row = $DRW->fetch_array($rs)){
		echo "<option value=\"".$row[0]."\"";
		if($row[0]==$searchstate) echo " selected=\"selected\"";
		echo ">".$row[1]."</option>";
	}	
	print '</select> &nbsp; <label><input type="radio" name="searchownbiz" value="1"';
	if($searchownbiz==1) {
		echo " checked=\"checked\"";
	}
	print ' />Business Owner</label> <label><input type="radio" name="searchownbiz" value="0"';
	if($searchownbiz==0) {
		echo " checked=\"checked\"";
	}
	print ' />Non-Business Owner</label> <label><input type="radio" name="searchownbiz" value="-1"';
	if($searchownbiz==-1) {
		echo " checked=\"checked\"";
	}
	print ' />Both</label></td></tr>';
	print "<tr><td>&nbsp;</td><td class=\"bodytext\">Partition:";
	$cy = (int)date('Y');
	for($y=$cy;$y>=2007;$y--){
		if($y==$cy){
			$v = '';
		}
		else{
			$v = $y;
		}
		print " &nbsp; <label><input type=\"radio\" name=\"hy\" value=\"$v\" ";
		if($v==$hy) {
			print ' checked="checked"';
		}
		print " />";
		if(empty($v)){
			echo 'Current';
		}
		else{
			echo $v;
		}
		print "</label>";
	}
	print "</td></tr>";
	print "</table>
	<input type=\"hidden\" name=\"sendsearch\" value=\"1\" /></form>
	</div>";
	
	print "<div style=\"margin-top:4px;\" class=\"bodytext\"><a href=\"filter.php\" class=\"bluelink\">Edit Filter Rules</a></div>";
}
print "<script type=\"text/JavaScript\">
<!--
function checkStart(){
	var startindex_d = document.searcher.start_d.selectedIndex;
	var startindex_m = document.searcher.start_m.selectedIndex;
	var startindex_y = document.searcher.start_y.selectedIndex;
	var endindex_d = document.searcher.end_d.selectedIndex;
	var endindex_m = document.searcher.end_m.selectedIndex;
	var endindex_y = document.searcher.end_y.selectedIndex;

	if(startindex_y>endindex_y){
		document.searcher.end_y.selectedIndex = startindex_y;
		endindex_y = startindex_y;
	}
	if(startindex_m>endindex_m && startindex_y==endindex_y){
		document.searcher.end_m.selectedIndex = startindex_m;
		endindex_m = startindex_m;
	}
	if(startindex_d>endindex_d && startindex_m==endindex_m && startindex_y==endindex_y){
		document.searcher.end_d.selectedIndex = startindex_d;
	}
}
function doWinSize(){
	var wintext = '';
	var screenH = 0;
	var screenW = 0;
	
	
	if (screen){
		if (screen.width) {
			screenW = screen.width;
		}
		if (screen.height) {
			screenH = screen.height;
		}
	}
	if(screenH>0 && screenW>0){
		screenW = screenW - 40;
		screenH = (screenH*.6) - 40;
		wintext = ', width='+screenW+', height='+screenH;	
	}
	return wintext;
}
function winPopScore(winloc) {
	var wind = window.open(winloc,'winpop4','left=20, top=20, scrollbars=yes, resizable=yes, width=500, height=250,toolbar=no,location=no,menubar=no,status=no');
	wind.focus();
}
function winPopMessage(winloc) {
	var addtext = doWinSize();
	var wind = window.open(winloc,'winpop2','left=0, top=0, scrollbars=yes, resizable=yes, toolbar=yes,location=yes,menubar=yes'+addtext);
	wind.focus();
}
function mark_points_changed(){
	window.onbeforeunload = function () {
		return 'Continue without updating?';
	}
}
function do_mupdate(){
	window.onbeforeunload = null;
	document.masser.submit();
}
//-->
</script>";

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
		if($rowcnt>10) {
			$paging .= "[ ";
			for($k=10;$k<=50;$k+=10){
				if($rowcnt>($k-10)){
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

function getEAssignment($assign_queue=''){
	global $DRW,$DRW_read,$DRW_main,$DRW_crm;
	if($assign_queue==2){
		$ct = " AND contact_type_m_c='cons_panelist' AND deleted=0";
	}
	else{
		$ct = " AND contact_type_m_c='prod_panelist' AND deleted=0";
	}
	$sql2 = "SELECT SQL_NO_CACHE userID,COUNT(e_assigned_admin_userID) AS emails FROM 
		cscan_admin_users LEFT JOIN 
		(SELECT e_assigned_admin_userID FROM cscan_email pd WHERE e_assigned_admin_userID<>0$ct) AS cpd 
		ON(userID=e_assigned_admin_userID) 
		WHERE is_email_assign_queue$assign_queue=1 AND user_status=1 GROUP BY userID order by emails,RAND() LIMIT 1";
	$rs2 = $DRW->query($sql2,$DRW_read);
	$row2 = $DRW->fetch_row($rs2);
	$assigned_admin_userID = (int)$row2[0];
		
	return $assigned_admin_userID;
}

function csvExcape($in,$delim = ','){
	$out = $in;
	if(strpos($out, $delim)!==false || strpos($out, '"')!==false || strpos($out, "\r\n")!==false || strpos($out, "\n")!==false || strpos($out, "\r")!==false || preg_match('/^0+\\d+$/',$out)>0){
		$out = '"'.str_replace('"', '""', $out).'"';
	}
	return $out;
}

require_once('panelist_bottom.php');
?>

