<?php 
include('includes/globalSession.php');
include('includes/checklogin.php');
include('includes/paginator.php');       //paginator class. 
include('includes/paginator_html.php');  //paginator_html class.

if(isset($_POST['file_choice'])) $file_choice = (int) $_POST['file_choice'];
else $file_choice = 1;
if(isset($_POST['bid'])) $bid = (int)$_POST['bid'];
else $bid = -1;
if(isset($_POST['ssid'])) $ssid = (int)$_POST['ssid'];
else $ssid = 0;
if(isset($_POST['sort'])) $sort = (int)$_POST['sort'];
else $sort = -3;

if(isset($_POST['more'])) $more = (int)$_POST['more'];
else $more = 0;

$savedQ = "SELECT mPanelID FROM cscan_search WHERE ID=$ssid";
$rs = $DRW->query($savedQ,$DRW_read);
$data = $DRW->fetch_row($rs);
$mPanelID = $data[0];
@$DRW->free_result($rs);

$mPanelIDArray = explode(',',$mPanelID);
if(in_array(1,$mPanelIDArray) || in_array(2,$mPanelIDArray) || $bid>=0) $consumer = true;
else $consumer = false;

$heading = array();
$heading["company"] = 'Company';
$heading["secondCompany"] = 'Second Company';
$heading["sectorID"] = 'Sector';
$heading["categoryID"] = 'Category';
$heading["subCategoryID"] = 'Sub Category';
$heading["entryID"] = 'EntryID';
$heading["productHeadline"] = 'Headline';
$heading["agentCommunicationID"] = 'Communications Type';
$heading["mChannelID"] = 'Media Channel';
$heading["mPanelID"] = 'Audience';
$heading["state"] = 'State';
if($consumer){
	$heading["age"] = 'Age';
	$heading["ficos"] = 'Risk Score';
	$heading["gender"] = 'Gender';
	$heading["income"] = 'Income';
}
$heading["compaignLanguage"] = 'Campaign Language';
$heading["mTypeID"] = 'Mailing Type';
$heading["affinityAssociation"] = 'Affinity/Association';
$heading["AffinityCategoryID"] = 'Affinity/Association Category';
$heading["firstSeen"] = 'First Seen';
$heading["lastSeen"] = 'Last Seen';
$heading["productName"] = 'Product';
//$heading["incentive"] = 'Incentive';

$headingIXI = array();
$headingIXI['ppdate'] = 'Month';
$headingIXI['competi_id'] = 'Panelist ID';
$headingIXI['invitationID'] = 'Invitation ID';
$headingIXI['ATP'] = 'ATP';
$headingIXI['Income360'] = 'Income360';
$headingIXI['DSDollar'] = 'DSDollar';
$headingIXI['DSI'] = 'DSI';
$headingIXI['ECohort_Code'] = 'ECohort_Code';
$headingIXI['ECohort_Desc'] = 'ECohort_Desc';
$headingIXI['ECohort_Flag'] = 'ECohort_Flag';

$showheading = array(
	"company"=>false,
	"secondCompany"=>false,
	"categoryID" =>false,
	"compaignLanguage" => false,
	"entryID" =>false, 
	"firstSeen"=>false,
	//"incentive" =>false,
	"lastSeen" =>false,
	"mChannelID"=>false,
	"mPanelID"=>false,
	"mTypeID"=>false,
	"productName"=>false,
	"productHeadline" =>false,
	"sectorID" =>false,
	"subCategoryID" =>false,
	"state"=>false,
	'agentCommunicationID'=>false,
	'mTypeID'=>false,
	'affinityAssociation'=>false,
	'AffinityCategoryID'=>false
);
if($consumer){
	$showheading['age'] = false;
	$showheading['ficos'] = false;
	$showheading['gender'] = false;
	$showheading['income'] = false;
}
$showheading['ppdate'] = false;
$showheading['competi_id'] = false;
$showheading['invitationID'] = false;
$showheading['ATP'] = false;
$showheading['Income360'] = false;
$showheading['DSDollar'] = false;
$showheading['DSI'] = false;
$showheading['ECohort_Code'] = false;
$showheading['ECohort_Desc'] = false;
$showheading['ECohort_Flag'] = false;

if(isset($_POST['field'])){
	foreach($_POST['field'] as $value){
		$showheading[$value] = true;
	}	
}
else {
	foreach($showheading as $key=>$value){
		$showheading[$key] = true;
	}
}

if(isset($_POST['units'])){
	$units = $_POST['units'];
}
else{
	$units = array();
}

if($file_choice==1){
	require_once 'Spreadsheet/Excel/Writer.php';
	// Creating a workbook
	$workbook = new Spreadsheet_Excel_Writer();
	$workbook->setVersion(8);
	// Creating a worksheet
	$worksheet =& $workbook->addWorksheet('Competiscan');
	
	$format_head =& $workbook->addFormat();
	$format_head->setBold();
	$format_head->setUnderline(1);
	
	//$format_wrap =& $workbook->addFormat();
	//$format_wrap->setTextWrap();
}

$erow = 0;
$ecol = 0;

foreach( $heading as $k=>$h ) {
	if($showheading[$k]) {
		if($file_choice==1){
			$worksheet->writeString($erow, $ecol++, $h, $format_head);
			if($k=='affinityAssociation'){
				$worksheet->writeString($erow, $ecol++, 'Affinity/Association Name', $format_head);
			}
		}
		else{
			$header .= csvExcape($h).",";
			if($k=='affinityAssociation'){
				$header .= csvExcape('Affinity/Association Name'). ",";
			}
		}
	}
}
if(isset($showheading['incentive'])){
	if($file_choice==1){
		$worksheet->writeString($erow, $ecol++, 'Sign-on Incentive', $format_head);
	}
	else{
		$header .= "Sign-on Incentive,";
	}
}
if(isset($showheading['incentive_ongoing'])){
	if($file_choice==1){
		$worksheet->writeString($erow, $ecol++, 'Ongoing Incentive', $format_head);
	}
	else{
		$header .= "Ongoing Incentive,";
	}
}
if(isset($showheading['fa'])){
	if($file_choice==1){
		$worksheet->writeString($erow, $ecol++, 'Face Amount', $format_head);
	}
	else{
		$header .= "Face Amount,";
	}
}
if(isset($showheading['tl'])){
	if($file_choice==1){
		$worksheet->writeString($erow, $ecol++, 'Term Length', $format_head);
	}
	else{
		$header .= "Term Length,";
	}
}

require_once('admin/additionalDetails.php');
foreach($addlArray as $o){
	if($o->id==178 || $o->id==179 || $o->id==87){
		while($o->getNext()){
			$field = $o->getField();
			if($field!='' && isset($showheading[$field.'_'.$o->id])){
				if($file_choice==1){
					$worksheet->writeString($erow, $ecol++, $o->label.' - '.$o->getTitle(), $format_head);
				}
				else{
					$header .= csvExcape($o->label.' - '.$o->getTitle()). ",";
				}
			}
		}
		$o->doReset();
	}
}

foreach( $headingIXI as $k=>$h ) {
	if($showheading[$k]) {
		if($file_choice==1){
			$worksheet->writeString($erow, $ecol++, $h, $format_head);
		}
		else{
			$header .= csvExcape($h).",";
		}
	}
}

if($file_choice==1){
	if(in_array(1,$units)){
		$worksheet->writeString($erow, $ecol++, 'Mail Pieces', $format_head);
	}
	if(in_array(2,$units)){
		$worksheet->writeString($erow, $ecol++, 'Estimated Mail Volume', $format_head);
	}
	if(in_array(3,$units)){
		$worksheet->writeString($erow, $ecol++, 'Estimated Mail Spend', $format_head);
	}
}
else{
	$header .= "Mail Pieces,Estimated Mail Volume,Estimated Mail Spend";
}

$erow++;

if($ssid>0 || $bid>=0) {
	list($orderby,$dorelev,$doexpans) = doQuerySort($sort);
	if($bid>=0) {
		list($sql) = doQuery(0, false, '', false, $bid);
	}
	else{
		//pd.productID as theproductID,mChannelID,mPanelID,productHeadline,sectorID,categoryID,subCategoryID,entryID,addedToDatabase,company,productName,incentive,compaignLanguage,firstSeen,lastSeen,mTypeID,state,agentCommunicationID
		list($sql) = doQuery($ssid,false,'',false,-1,$dorelev,$doexpans);
	}
	
	$sql .= $orderby;
	
	/**********************
	Paginator _html object
	***********************/  
	if(isset($_REQUEST['page']) && $_REQUEST['page']>0) {
		$page = (int)$_REQUEST['page'];
		if($bid>=0) {
			list($countQuery) = doQuery(0, true, '', false, $bid);
		}
		else{
			list($countQuery) = doQuery($ssid,true);
		}
		$count_result = $DRW->query($countQuery,$DRW_read);
		$count = $DRW->fetch_row($count_result);
		$search_num_of_rows = $count[0];
		@$DRW->free_result($count_result);
		
		$a = new Paginator_html($page,$search_num_of_rows);
		
		#set limit on the current page.
		$a->set_Limit(30);
		
		$limit1 = $a->getRange1(); 
		#Get the number of items displayed on page.
		$limit2 = $a->getRange2(); 
		
		if(isset($_POST['topCompany']) && $_POST['topCompany']>0){
			$limit2 = (int)$_POST['topCompany'];
		}
		
		$sql .= " Limit $limit1 , $limit2";
	}
	elseif(isset($_POST['topCompany']) && $_POST['topCompany']>0){
		$sql .= " Limit 0,".(int)$_POST['topCompany'];
	}
	elseif($file_choice==1){
		//excel limit is below
		//$sql .= " Limit 0,65534";
	}
	
	@ob_end_clean();
	if($file_choice!=1){
		header("Content-Type: text/plain");//application/excel,application/vnd.ms-excel
		header("Content-Disposition: attachment; filename=Competiscan_Report_".date('Y-m-d').".csv");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $header;
	}
	
	$query = "CREATE TEMPORARY TABLE `product_grouper` (
		groupID int(10) unsigned NOT NULL auto_increment,
		mail_pieces int(10) NOT NULL DEFAULT '0',
		mail_volume int(10) NOT NULL DEFAULT '0',
		spend int(10) NOT NULL DEFAULT '0',
		groupCol text,
		PRIMARY KEY (groupID)
	)";
	
	$DRW->query($query,$DRW_main);
	
	$sep = "\t|\t";
	
	$result = $DRW->query($sql,$DRW_read);
	while($row = $DRW->fetch_assoc($result)) {
		$mult = array();
		$sql_P = "SELECT ppmv,DATE_FORMAT(ppdate,'%m/%Y'),competi_id,invitationID,ATP,Income360,DSDollar,DSI,ECohort_Code,ECohort_Desc,ECohort_Flag FROM cscan_panelists_product cp, cscan_income_report ci, cscan_state cs, cscan_panelists pp WHERE productID={$row['theproductID']} AND cp.ir_ID=ci.ir_ID AND cp.ppstateID=cs.stateID AND ppmv>0 AND cp.panelist_id=pp.panelist_id";
		$result_P = $DRW->query( $sql_P,$DRW_read );
		while($row_P = $DRW->fetch_row( $result_P )){
			$mult[] = array('ppmv'=>$row_P[0],'ppdate'=>$row_P[1],'competi_id'=>$row_P[2],'invitationID'=>$row_P[3],'ATP'=>$row_P[4],'Income360'=>$row_P[5],'DSDollar'=>$row_P[6],'DSI'=>$row_P[7],'ECohort_Code'=>$row_P[8],'ECohort_Desc'=>$row_P[9],'ECohort_Flag'=>$row_P[10]);
		}
		@$DRW->free_result($result_P);
		
		foreach($mult as $pdata){
			$ecol = 0;
			$line  = '';
			$mail_volume_tot = $pdata['ppmv'];
			$mail_pieces = 1;
			$dmspend = 0;
			
			if(in_array(3,$units) && $mail_volume_tot>0){
				$query2 = "SELECT document_size_byte FROM cscan_document WHERE productID={$row['theproductID']} AND document_id=1";
				$query_result2 = $DRW->query($query2,$DRW_read);
				$data2 = $DRW->fetch_row($query_result2);
				$document_size_byte = (int)$data2[0];
				@$DRW->free_result($query_result2);
				$dmspend = doSpend($mail_volume_tot,$document_size_byte);
			}
			$groupCol = '';
			foreach( $heading as $k=>$h ) {
				if($showheading[$k]) {
					switch ($k) {
						case 'productName': 
							$groupCol .= $row['productName'].$sep;
							break;
						case 'company':
							$groupCol .= $row['company'].$sep;
							break;
						case 'secondCompany':
							$secondCompany = '';
							$resultC = $DRW->query("SELECT companyName FROM cscan_company pa,cscan_company_product pp 
								WHERE pa.companyID=pp.companyID AND pp.productID={$row['theproductID']} AND primary_co<>1 ORDER BY primary_co ASC,companyName ASC",$DRW_read);
							while($dataC = $DRW->fetch_row($resultC)){
								if($secondCompany!=''){
									$secondCompany .= $sep;
								}
								$secondCompany .= $dataC[0];
							}
							@$DRW->free_result($resultC);
							if($secondCompany=="") {
								$secondCompany = 'N/A';
							}
							$groupCol .= $secondCompany.$sep;
							break;
						case 'productHeadline':
							$groupCol .= preg_replace('/\\s+/',' ',$row['productHeadline']).$sep;
							break;
						case 'entryID':
							//$url = 'http://www.competiscan.com/index.php?product='.$row['theproductID'];
							$groupCol .= $row['entryID'].$sep;
							break;
						case 'sectorID':
							$groupCol .= sectorName($row['sectorID']).$sep;
							break;
						case 'categoryID':
							$groupCol .= categoryName($row['categoryID']).$sep;
							break;
						case 'subCategoryID':
							$groupCol .= subCategoryName($row['subCategoryID']).$sep;
							break;
						case 'compaignLanguage':
							$groupCol .= languageName($row['compaignLanguage']).$sep;
							break;
						case 'firstSeen':
							$groupCol .= $row['firstSeen'].$sep;
							break;
						case 'lastSeen':
							$groupCol .= $row['lastSeen'].$sep;
							break;
						case 'mChannelID':
							$groupCol .= mediaChannelName($row['mChannelID']).$sep;
							break;
						case 'mPanelID':
							$groupCol .= mediaPanelName($row['mPanelID']).$sep;
							break;
						case 'state':
							$groupCol .= stateName($row['state']).$sep;
							break;
						case 'agentCommunicationID':
							$agname = agentName($row['agentCommunicationID']);
							if($agname== 'NA') $agname = '';
							$groupCol .= $agname.$sep;
							break;
						case 'mTypeID':
							$name = mediaType($row['mTypeID']);
							if($name== 'Any') $name = '';
							$groupCol .= $name.$sep;
							break;
						case 'affinityAssociation':
						case 'AffinityCategoryID':
							$aff_ids = '';
							$aff_cids = array();
							$resultC = $DRW->query("SELECT pa.affinityID,affinityName FROM cscan_affinity pa,cscan_affinity_product pp 
								WHERE pa.affinityID=pp.affinityID AND pp.productID={$row['theproductID']} ORDER BY affinityName",$DRW_read);
							while($dataC = $DRW->fetch_row($resultC)){
								if($aff_ids!='') {
									$aff_ids .= ', ';
								}
								$aff_ids .= $dataC[1];
								$resultC2 = $DRW->query("SELECT AffinityCategoryID FROM cscan_aff_cat WHERE affinityID=$dataC[0]",$DRW_read);
								while($dataC2 = $DRW->fetch_row($resultC2)){
									if(!in_array($dataC2[0],$aff_cids) && !empty($dataC2[0])){
										$aff_cids[] = $dataC2[0];
									}
								}
							}
							@$DRW->free_result($resultC);
							if($k=='AffinityCategoryID'){
								$groupCol .= getAffinityCategoryName(implode(',',$aff_cids)).$sep;
							}
							else{
								if($row['affinityAssociation']==1) $groupCol .= 'Yes'.$sep;
								else $groupCol .= 'No'.$sep;
								$groupCol .= $aff_ids.$sep;
							}
							break;
						case 'age':
							$groupCol .= getAgeName($row['age']).$sep;
							break;
						case 'gender':
							if($row['gender']=='M') $name = 'Male';
							elseif($row['gender']=='F') $name = 'Female';
							else $name = 'Male, Female';
							$groupCol .= $name.$sep;
							break;
						case 'income':
							$groupCol .= getIncomeName($row['incomeID']).$sep;
							break;
						case 'ficos':
							$sql_P = "SELECT AVG(ppfico_score),MAX(ppfico_score),MIN(ppfico_score) as ficos FROM cscan_panelists_product WHERE productID={$row['theproductID']} AND ppfico_score>0";
							$result_P = $DRW->query( $sql_P,$DRW_read );
							$rowMV = $DRW->fetch_row( $result_P );
							$fico_average = round($rowMV[0]);
							$fico_max = round($rowMV[1]);
							$fico_min = round($rowMV[2]);
							@$DRW->free_result($result_P);
							if($fico_average==0){
								$name = '';
							}
							elseif($fico_max==$fico_average){
								$name = "AVG: $fico_average";
							}
							else{
								$name = "MIN: $fico_min, MAX: $fico_max, AVG: $fico_average";
							}
							$groupCol .= $name.$sep;
							break;
					}
				}
			}
			$incentive = $row['incentive'];	
			if($incentive == '') $incentive = 'N/A';
			$incentive_ongoing = $row['incentive_ongoing'];	
			if($incentive_ongoing == '') $incentive_ongoing = 'N/A';
			$fa = getFaceAmountName($row['fa_ids']);
			$tl = getTermLengthName($row['tl_ids']);
			if(isset($showheading['incentive'])){
				$groupCol .= $incentive.$sep;
			}
			if(isset($showheading['incentive_ongoing'])){
				$groupCol .= $incentive_ongoing.$sep;
			}
			if(isset($showheading['fa'])){
				$groupCol .= $fa.$sep;
			}
			if(isset($showheading['tl'])){
				$groupCol .= $tl.$sep;
			}
			
			foreach($addlArray as $o){
				if($o->id==178 || $o->id==179 || $o->id==87){
					$sqlA = "SELECT * FROM ".$o->table." WHERE productID=".$row['theproductID'];
					$resultA = $DRW->query( $sqlA,$DRW_read );
					if( $DRW->num_rows( $resultA ) > 0 ) {
						$dataAssoc = $DRW->fetch_assoc($resultA);
					}
					else{
						$dataAssoc = array();
					}
					while($o->getNext()){
						$field = $o->getField();
						if($field!='' && isset($showheading[$field.'_'.$o->id])){
							if(isset($dataAssoc[$field])){
								$val = $o->doProcess($dataAssoc[$field]);
							}
							else {
								$val = '';
							}
							$groupCol .= $val.$sep;
						}
					}
					@$DRW->free_result($resultA);
					$o->doReset();
				}
			}

			foreach( $headingIXI as $k=>$h ) {
				if($showheading[$k]) {
					$groupCol .= $pdata[$k].$sep;
				}
			}
			
			$query = "INSERT INTO `product_grouper` (mail_pieces,mail_volume,spend,groupCol) VALUES (".$mail_pieces.",".round($mail_volume_tot).",".round($dmspend).",'".$DRW->real_escape_string(substr($groupCol,0,-1*strlen($sep)))."')";
			$DRW->query($query,$DRW_main);
		}
	}
	@$DRW->free_result($result);
	
	$result = $DRW->query("SELECT SUM(mail_pieces),SUM(mail_volume),SUM(spend),groupCol FROM product_grouper GROUP BY groupCol Limit 0,65534",$DRW_read);
	while($row = $DRW->fetch_row($result)) {
		$mail_pieces = $row[0];
		$mail_volume = $row[1];
		$spend = $row[2];
		$groupCols = explode($sep,$row[3]);
		$ecol = 0;
		
		if($file_choice==1){
			foreach($groupCols as $groupCol){
				$worksheet->writeString($erow, $ecol++, $groupCol);
			}
			if(in_array(1,$units)){
				$worksheet->write($erow, $ecol++, $mail_pieces);
			}
			if(in_array(2,$units)){
				$worksheet->write($erow, $ecol++, $mail_volume);
			}
			if(in_array(3,$units)){
				$worksheet->write($erow, $ecol++, $spend);
			}
			$erow++;
		}
		else{
			foreach($groupCols as $groupCol){
				echo csvExcape($groupCol).",";
			}
			echo csvExcape($mail_pieces).",".csvExcape($mail_volume).",".csvExcape($spend)."\n";
		}
	}
	@$DRW->free_result($result);
	
	if($file_choice==1){
		// sending HTTP headers
		$workbook->send("Competiscan_Report_".date('Y-m-d').".xls");
		// Let's send the file
		$workbook->close();
	}
}

function csvExcape($in,$delim = ','){
	$out = $in;
	if(strpos($out, $delim)!==false || strpos($out, '"')!==false || strpos($out, "\r\n")!==false || strpos($out, "\n")!==false || strpos($out, "\r")!==false || preg_match('/^0+\\d+$/',$out)>0){
		$out = '"'.str_replace('"', '""', $out).'"';
	}
	return $out;
}
?>