<?php
error_reporting( E_ALL ^ E_DEPRECATED );
ini_set('display_errors',1);
require_once('panelist_top.php');
error_reporting( E_ALL ^ E_DEPRECATED );
ini_set('display_errors',1);
assert_options(ASSERT_ACTIVE, 0); 
//$SPHINX_server='172.19.40.118';
//require("vendor/autoload.php");
$test=true;
$test=false;
if($test){
$sphinxClient	= new SphinxClient();
$sphinxClient->getLastError();
$sphinxClient->setServer($SPHINX_server,$SPHINX_port);
//$result=$sphinxClient->query('Health','productidetail');
$sphinxClient->setLimits(0,5);
    //$q  =   'deltaindex201501';
    $q='base_index_prod,delta_index_prod,base_index_prod_digital';
if(!empty($_GET['q'])){
    $q  =   'deltaindex201501 base_index_prod201501';
}
//print_r($sphinxClient);
//$sphinxClient->setSortMode(SPH_SORT_EXTENDED,'entryid_sort1 DESC');
//$sphinxClient->SetMatchMode ( SPH_MATCH_EXTENDED );
//$sphinxClient->SetMatchMode(SPH_MATCH_ANY);
//$sphinxClient->SetMatchMode(SPH_MATCH_ALL);
$sphinxClient->SetMatchMode(SPH_MATCH_EXTENDED2);
//$sphinxClient->SetSortMode (SPH_SORT_EXTENDED,"email_date DESC" );
$sphinxClient->SetArrayResult(true);
$searchcountry='US';
if(!empty($searchcountry)){
	$countryStates = '';
	$sqlc = "SELECT stateID FROM cscan_state WHERE countryCode='".$DRW->real_escape_string($searchcountry)."'";
	$rsc = $DRW->query( $sqlc,$DRW_read );
        ########### convert or clause itno in clause #############    
	while($rowc = $DRW->fetch_row($rsc) ) {
		$stateArray[]   =   $rowc[0];
	}   
}
//$ids[]=510;
//$ss='TL OR RL';
//$str='@panelist_core TL|RL';
//$e_assigned_admin_userID=325;
//$sphinxClient->setFilter('e_assigned_admin_userID',array($e_assigned_admin_userID));
//print_r($stateArray);
//$sphinxClient->setFilter('muid',$ids);
//$sphinxClient->setFilter('email_stateID',$stateArray);
//$sphinxClient->SetFilterRange ('email_date', 1536085800, 1536258600, $exclude=false );
//$t= 'TL';
$str='When the steel mill shut down and left behind';
$results = $sphinxClient->Query($str, $q); //deltaindex base_index_prod
echo"<pre>";
print_r($results);exit;
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
if(isset($_SESSION['hy'])) 
	$hy = $_SESSION['hy'];
else 
	$hy = '';
$searchid 		= 	0;
$searchsubj 	= 	0;
$searchsender 	= 	0;
$searchbody 	= 	0;
$searchtext 	= 	'';
$searchstate 	= 	0;
$searchcountry 	= 	'US';
$panelist_ids 	= 	'';
$searchownbiz 	= 	-1;
$mtypes 		= 	array(0,1);
$noread 		= 	0;
$stateArray 	=  $panelistArray	= array();
$limittext	=	$pagingtext	=	'';
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
$readTypes 	=	array(0=>'Unread',1=>'Read');
$panelist_core_options 	= 	array('C'=>'C','EN'=>'EN','N'=>'N','RL'=>'RL','TC'=>'TC','TL'=>'TL');
if($ctype==1) {
	$panelist_core_options 	=	 array('ID'=>'ID','PT'=>'PT','PN'=>'PN');
}
if(isset($_GET['sort'])) 
	$_SESSION['sort'] = $sort = (int)$_GET['sort'];
else {
	if(isset($_SESSION['sort'])) 
		$sort = $_SESSION['sort'];
	else 
		$_SESSION['sort'] = $sort = 5;
}
$panelist_core_search = array();
if(checkGroup(20) && isset($_POST['sendsearch'])){
	$DRW->query("DELETE FROM cscan_search_email WHERE uid={$AUTH_DATA['userID']}",$DRW_main);
	if(!isset($_POST['clear'])){
		if(isset($_POST['searchid'])) $searchid = (int)$_POST['searchid'];
		if(isset($_POST['searchsubj'])) $searchsubj = (int)$_POST['searchsubj'];
		if(isset($_POST['searchsender'])) $searchsender = (int)$_POST['searchsender'];
		if(isset($_POST['searchbody'])) $searchbody = (int)$_POST['searchbody'];
		$searchtext = trim($_POST['searchtext']);
		$searchstate = (int)$_POST['searchstate'];
		$searchcountry = $_POST['searchcountry'];
		$panelist_ids = trim($_POST['panelist_ids']);
		$searchownbiz = (int)$_POST['searchownbiz'];
		if($searchtext!='' && !$searchsubj && !$searchsender && !$searchbody && !$searchid){
			$searchsubj = 1;
			$searchbody = 1;
		}
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
	$_SESSION['searchid'] = $searchid;
	$_SESSION['searchsubj'] = $searchsubj;
	$_SESSION['searchsender'] = $searchsender;
	$_SESSION['searchbody'] = $searchbody;
	$_SESSION['searchtext'] = $searchtext;
	$_SESSION['searchstate'] = $searchstate;
	$_SESSION['searchcountry'] = $searchcountry;
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
	if(isset($_SESSION['searchid'])) $searchid = $_SESSION['searchid'];
	if(isset($_SESSION['searchsubj'])) $searchsubj = $_SESSION['searchsubj'];
	if(isset($_SESSION['searchsender'])) $searchsender = $_SESSION['searchsender'];
	if(isset($_SESSION['searchbody'])) $searchbody = $_SESSION['searchbody'];
	if(isset($_SESSION['searchtext'])) $searchtext = $_SESSION['searchtext'];
	if(isset($_SESSION['searchstate'])) $searchstate = $_SESSION['searchstate'];
	if(isset($_SESSION['searchcountry'])) $searchcountry = $_SESSION['searchcountry'];
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
if(isset($_SESSION['e_assigned_admin_userID']) && $_SESSION['e_assigned_admin_userID']!=0) {
	$e_assigned_admin_userID = $_SESSION['e_assigned_admin_userID'];
}
else {
	$e_assigned_admin_userID = 0;
}
$last  = count($contactType) - 1;
foreach($contactType as $contact_type_m_c=>$contact_type_m_cTitle){
	if(checkGroup(20) || $n==2){
		if($ctype==$n){
			print "<span class=\"headings\">$contact_type_m_cTitle</span>";
		}
		else print "<a href=\"{$_SERVER['PHP_SELF']}?ctype=$n\" class=\"bluelink\">$contact_type_m_cTitle</a>";
		if($n!=$last) print ' &nbsp; | &nbsp; ';
	}
	$n++;
}
echo '</div>';
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
print "<tr><td style=\"background:#0055E3;color:#ffffff;\" class=\"text\" valign=\"bottom\"><input type=\"checkbox\" name=\"mark_all\" value=\"1\" onclick=\"mark_all_click();\" /></td><td style=\"background:#0055E3;color:#ffffff;\" class=\"text\" valign=\"bottom\">";
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
if($sort!=3) print " <a href=\"{$_SERVER['PHP_SELF']}?sort=3\" class=\"topLinks\">Sender Email</a>";
else {echo $sphinxClient->getLastError();
	print '<span style="text-decoration:underline;" class="topLinks">Sender Email</span>';
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
############ sphinx setting #####################
$sphinxClient	= new SphinxClient();
$sphinxClient->getLastError();
$sphinxClient->setServer('localhost',9312);
$sphinxClient->setLimits($page,$limshow,1000000);
$sphinxClient->SetMatchMode(SPH_MATCH_EXTENDED2);
$sphinxClient->SetArrayResult(true);
$searchedquery='';



if(!empty($ctype)){
	switch ($ctype){
		case 1:
			$ctypesearch	=	'prod_panelist';
		break;
		case 2:
			$ctypesearch	=	'cons_panelist';
		break;
		case 3:
			$ctypesearch	=	'brok_panelist';
		break;
		default :
			$ctypesearch	=	'cons_panelist';
	}
	
	//$searchedquery.="@contact_type_m_c $ctypesearch";
        $searchedquery.="@@@@ $ctypesearch";
}
############  for country search #####################
if(!empty($searchcountry)){
	$countryStates = '';
	$sqlc = "SELECT stateID FROM cscan_state WHERE countryCode='".$DRW->real_escape_string($searchcountry)."'";
	$rsc = $DRW->query( $sqlc,$DRW_read );
        ########### convert or clause itno in clause #############    
	while($rowc = $DRW->fetch_row($rsc) ) {
		$stateArray[]   =   $rowc[0];
	}   
}
############ end for country search #####################
############ for state id search #####################
if($searchstate>0){
	$stateArray		=	array();
	$searchcountry	=	'';
	$stateArray[]   =   $searchstate;
}
if(!empty($stateArray)){
	//print_r($stateArray);
	$sphinxClient->setFilter('email_stateid',$stateArray);
}
if(!empty($mtypes )){
	$sphinxClient->setFilter('deleted',$mtypes);
}
############ end for state id search #####################
############ for paneldate range search #####################
if(($start_d>0) || ($start_m>0)|| ($start_y>0)){
	$mindate	=	strtotime($start_y.'-'.$start_m.'-'.$start_d);
	$maxdate	=	strtotime($end_y.'-'.$end_m.'-'.$end_d);
	$sphinxClient->SetFilterRange ('email_date', $mindate, $maxdate, $exclude=false );
}
############ end for paneldate range search #####################
############ for panelist search #####################
if(!empty($panelist_ids)){
	$sqlc = "SELECT panelist_id FROM cscan_panelists WHERE competi_id in(".trim($panelist_ids).")";
	$rsc = $DRW->query( $sqlc,$DRW_read );
        ########### convert or clause itno in clause #############    
	while($rowc = $DRW->fetch_row($rsc) ) {
		$panelistArray[]   =   $rowc[0];
	}   
}
if(!empty($panelistArray)){
	$sphinxClient->setFilter('panelist_id',$panelistArray);
}
############ end for panelist search #####################

if($searchid>0 && !empty($searchtext)){
	$searchesids	=array();
	$searchesids	=	explode(",",$searchtext);
       // print_r($searchesids);
	$sphinxClient->setFilter('muid',$searchesids);
}

############  for deleted search #####################
if( !empty($mtypes)){
   // print_r($mtypes);
   	//$sphinxClient->setFilter('deleted',$mtypes);
}
############ end for deleted search #####################
############  for marked search #####################
if($noread>0){
	$email_read=array($noread);
	$sphinxClient->setFilter('email_read',$email_read);
}
############ end for marked search #####################


$sphinxtype='';
if(!empty($ctypesearch)){
    $sphinxtype = " @contact_type_m_c ".$ctypesearch;
}
$sphinxsearchstring=$newsphinxsearchstring='';
if(!empty($searchtext)){
    $sphinxsearchstring     =   parseSphinx($sphinxClient,$searchtext);
}
if($searchbody & !$searchid>0 & !empty($sphinxsearchstring)){
        $ps = ' '.$searchedquery;
        $newsphinxsearchstring =  " @cettext ".$sphinxsearchstring;
}
if(!$searchbody && $searchsubj){
        //$ps = '@email_subject '.$searchedquery;
        $newsphinxsearchstring =  " @email_subject ".$sphinxsearchstring;
}
############# for the sender email ################
$issendersearch ='';
if(isset($_POST['sendsearch'])){
    $issendersearch     =   $_POST['sendsearch'];
        
}
if(!$searchbody && $issendersearch=='1' && !empty($sphinxsearchstring) && !$searchsubj &$searchid<0){
        //$ps = '@email_subject '.$searchedquery;
        $newsphinxsearchstring =  " @email_from ".str_replace('@','\@',$searchtext);
}
############# end for sender email #################


$searchinOCR_string= $sphinxtype.$newsphinxsearchstring;
//echo $searchbody;

print_r($_POST);
echo $searchinOCR_string;
//echo $hy;
$sphinxClient->SetSortMode ( SPH_SORT_EXTENDED , "email_date DESC" );
//echo"<br><br>";
$results = $sphinxClient->Query($searchinOCR_string,'base_index_prod'.$hy);
//echo "<br>".$searchedquery."<br>";
$sphinxClient->getLastError(); 
//echo"<pre>";
//print_r($results);exit;
//echo"</pre>";
$totalData		=	 $results['total'];
$muids = array();
$hiddentext = '';
if($totalData>0){
	list($limittext,$pagingtext) = showPaging($_SERVER['PHP_SELF'],$totalData,$page,$limshow);
	$data	=	$results['matches'];
	for($i=0;$i<count($data);$i++){
		$muid		 		= $data[$i]['attrs']['muid'];
		$email_date 		= date('m/d/y h:i A',$data[$i]['attrs']['email_date']);
		$deleted			= $data[$i]['attrs']['deleted'];
        $remainingfields	= $DRW->query("SELECT email_subject, contact_type_m_c,email_to,panelist_core,email_from_one,email_from,panelist_score FROM cscan_email$hy WHERE muid=$muid",$DRW_read);
		$remainingData		= $DRW->fetch_assoc($remainingfields);
        //print_r($remainingData);


		$email_subject 		= $remainingData['email_subject'];
		$contact_type_m_c 	= $remainingData['contact_type_m_c'];
		$email_to 			= $remainingData['email_to'];
		$email_from 		= $remainingData['email_from'];
		$panelist_core 		= $remainingData['panelist_core'];
		$panelist_score 	= $remainingData['panelist_score'];
		$email_read 		= $data[$i]['attrs']['email_read'];
		$email_from_one 	= $remainingData['email_from_one'];
		$panelist_id 		= $data[$i]['attrs']['panelist_id'];
		
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
				if(checkGroup(20)){
					$email1 = "<a href=\"http://crm.competiscan.com/index.php?action=DetailView&amp;module=Contacts&amp;record=$id\" class=\"bluelink\" target=\"_blank\">$email1</a>";
				}
				$email_from .= ' &lt;'.$email1.'&gt;';
			}
			else $email_from = htmlspecialchars($email_from);
		}
		else $email_from = htmlspecialchars($email_from);
		print "<tr><td valign=\"top\" class=\"bodytext\">";
		if($deleted!=2 && $deleted!=4){
			print "<input type=\"checkbox\" name=\"marked[]\" value=\"$muid\" />";
		}
		else{
			print '&nbsp;';
		}
		print "</td><td valign=\"top\" class=\"bodytext\">";
		if($deleted==2){
			print "<a href=\"#\" onclick=\"document.forms.masser.copies_id.value='$muid'; return false;\" class=\"bluelink\">$muid</a>";
		}
		else {
			print $muid;
		}
		print "</td><td valign=\"top\" class=\"bodytext\" nowrap=\"nowrap\">";
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
		print "</select></td>
		<td valign=\"top\" class=\"bodytext\" nowrap=\"nowrap\"><select name=\"panelist_score$muid\" size=\"1\" onchange=\"mark_points_changed();\">";
		//also change in panelist_score.php, panelist_report_iframe_month.phpand alter table cscan_crm_contacts_data
		if($contact_type_m_c=='cons_panelist') $options = array('0'=>'0','2'=>'2','3'=>'3','5'=>'5','10'=>'10','50'=>'50');
		else $options = array('0'=>'0','1'=>'1');
		foreach($options as $val=>$option){
			print "<option value=\"$val\"";
			if($val==$panelist_score) print ' selected="selected"';
			print ">$option</option>";
		}
		$hiddentext .= "<input type=\"hidden\" name=\"old_panelist_score$muid\" value=\"".intval($panelist_score)."\" /><input type=\"hidden\" name=\"panelist_id$muid\" value=\"".$panelist_id."\" />";
		print "</select></td><td valign=\"top\" class=\"bodytext\">$email_date</td>";
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
else print "<tr><td colspan=\"10\" class=\"bodytext\"><i>None</i></td></tr>";
print "</table>
</div>";
if(count($muids)>0) print "<div style=\"margin-top:4px;\" class=\"bodytext\">Assign Copies ID<input type=\"text\" name=\"copies_id\" id=\"copies_id\" size=\"10\" /> &nbsp; <input class=\"button\" type=\"submit\" name=\"update\" value=\"Update\" /></div>"; 
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
	print " />Sender Email</label></td></tr>";
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
	print "<tr><td>&nbsp;</td><td class=\"bodytext\"><select name=\"searchstate\"><option value=\"0\">State/Province</option>";
	getstates($searchstate);
	print '</select> &nbsp; <label><input type="radio" name="searchcountry" value=""';
	if(empty($searchcountry)) {
		echo " checked=\"checked\"";
	}
	print ' />All</label>';
	$sql = "SELECT DISTINCT countryCode,country FROM cscan_state JOIN ISO31661_alpha2code ON (code=countryCode) ORDER BY country";
	$rs = $DRW->query( $sql,$DRW_read );
	while($row = $DRW->fetch_row($rs) ) {
		print ' <label><input type="radio" name="searchcountry" value="'.$row[0].'"';
		if($searchcountry==$row[0]) {
			echo " checked=\"checked\"";
		}
		print ' />'.htmlspecialchars($row[1]).'</label>';
	}
	print '</td></tr>';
	print '<tr><td>&nbsp;</td><td class="bodytext"><label><input type="radio" name="searchownbiz" value="1"';
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
    $current_year = (int)date('Y');
    $first_half_year_partition = date('Y01');
    $into_second_half_of_year_where_first_half_partition_should_have_taken_place = (date("n") > 6) ? true : false;
    for ($y = $current_year; $y >= 2007; $y--) {
        $moreArray = array();
        if ($y == $current_year) {
            $moreArray[] = '';
            if ($into_second_half_of_year_where_first_half_partition_should_have_taken_place) {
                $moreArray[] = intval($y.'01');
            }
        } else {
            if ($y >= 2013) {
                $moreArray[] = intval($y.'07');
                $moreArray[] = intval($y.'01');
        } else {
            $moreArray[] = $y;
        }
    }
		foreach($moreArray as $v){
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
function mark_all_click(){
	var chex = false;
	if(document.masser.mark_all.checked){
		chex = true;
	}
	for(var i=0;i<document.masser['marked[]'].length;i++){
		document.masser['marked[]'][i].checked = chex;
	}
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
require_once('panelist_bottom.php');
?>
