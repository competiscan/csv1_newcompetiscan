<?php
require_once('includes/globalSession.php');
require_once('includes/checklogin.php');
require_once('includes/paginator.php');       //paginator class. 
require_once('includes/paginator_html.php');  //paginator_html class.

if(isset($_REQUEST['file_choice'])) $file_choice = (int) $_REQUEST['file_choice'];
else $file_choice = 1;
if(isset($_REQUEST['bid'])) $bid = (int)$_REQUEST['bid'];
else $bid = -1;
if(isset($_REQUEST['ssid'])) $ssid = (int)$_REQUEST['ssid'];
else $ssid = 0;
if(isset($_REQUEST['sort'])) $sort = (int)$_REQUEST['sort'];
else $sort = -3;

if($_SESSION['sess_plevel']>0 && ($ssid>0 || $bid>=0)) {
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
		
		if(isset($_REQUEST['topCompany']) && $_REQUEST['topCompany']>0){
			$limit2 = (int)$_REQUEST['topCompany'];
		}
		
		$sql .= " Limit $limit1 , $limit2";
	}
	elseif(isset($_REQUEST['topCompany']) && $_REQUEST['topCompany']>0){
		$sql .= " Limit 0,".(int)$_REQUEST['topCompany'];
	}
	
	$root = dirname(__FILE__);
	$yearpath = date('Y/');
	$monthpath = date('m/');
	$pathpart = $root.'/PDF/';
	$datepath = $yearpath.$monthpath;
	$imagePath = "$pathpart$datepath";
	if(!is_dir($pathpart.$yearpath)){
		mkdir($pathpart.$yearpath,0775);
	}
	if(!is_dir($imagePath)){
		mkdir($imagePath,0775);
	}
	$dzfile = 'CompetiScan_Zip_'.date('Y-m-d').'.zip';
	$dzloc = 'PDF/'.$datepath.$dzfile;
	$dzname = $imagePath.$dzfile;
	$zip = new ZipArchive;
	$res = $zip->open($dzname, ZIPARCHIVE::OVERWRITE);
	
	if($res === TRUE) {
		$result = $DRW->query($sql,$DRW_read);
		while($row = $DRW->fetch_assoc($result)) {
			$productID = $row['theproductID'];
			
			$query2 = "SELECT document_id,document_filename,document_content_type,document_size_byte,UNIX_TIMESTAMP(document_createddate),document_path FROM cscan_document WHERE productID=$productID ORDER BY document_createddate DESC LIMIT 1";
			$query_result2 = $DRW->query($query2,$DRW_read);
			$data2 = $DRW->fetch_row($query_result2);
			$document_id = (float)$data2[0];
			$document_filename = $data2[1];
			$document_content_type = $data2[2];
			$document_size_byte = $data2[3];
			$document_createddate = $data2[4];
			$document_path = $data2[5];
			$DRW->free_result($query_result2);
			
			$pdf_src = dirname(__FILE__)."/PDF/$document_path$productID/$document_filename";
			
			if($document_filename!='' && is_file($pdf_src)){
				$zip->addFile($pdf_src, "CompetiScanProduct_$productID.pdf");
			}
		}
		@$DRW->free_result($result);
		
		$zip->close();
		
		@ob_end_clean();
		header("Location: $dzloc");
		exit;
	}
}

function safeZipName($in){
	return preg_replace('/[^a-zA-Z0-9_\\.\\-\\s\\&\\,\'"]/',' ', $in);
}
?>