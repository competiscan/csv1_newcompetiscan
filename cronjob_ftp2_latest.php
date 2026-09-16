#!/usr/bin/php
<?php
require_once("includes/ehLog_set.php");
$ehL->start(__FILE__);

require_once ("includes/dbcon.php");
$DRW->databaseReadWrite_die = 1;
include_once 'includes/functions.php';
include_once 'includes/thumb.php';

$ftp_server = "competiscan2.chicagorecords.com"; //competiscan.chicagorecords.com, ftp.chicagorecords.com
$ftp_user_name = "output2"; // output | competiscan
$ftp_user_pass = "c0mpet1sc@n"; //GoBears!


//$ftp_server = "competiscan2.chicagorecords.com"; //competiscan.chicagorecords.com, ftp.chicagorecords.com
//$ftp_user_name = "output"; //competiscan
//$ftp_user_pass = "c0mpet1sc@n"; //GoBears!


ini_set('memory_limit', '-1');
$AUTH_DATA = array();
$AUTH_DATA['userID'] = 0;

$yearpath = date('Y/');
$monthpath = date('m/');
$daypath = date('d/');
$pathpart = dirname(__FILE__)."/PDF/crm/";

if(!is_dir($pathpart.$yearpath)){
	mkdir($pathpart.$yearpath,02755);
	@chmod($pathpart.$yearpath,02755);
	@chown($pathpart.$yearpath,'apache');
	//@chgrp($pathpart.$yearpath,'competiscan_web');
}
if(!is_dir($pathpart.$yearpath.$monthpath)){
	mkdir($pathpart.$yearpath.$monthpath,02755);
	@chmod($pathpart.$yearpath.$monthpath,02755);
	@chown($pathpart.$yearpath.$monthpath,'apache');
	//@chgrp($pathpart.$yearpath.$monthpath,'competiscan_web');
}
if(!is_dir($pathpart.$yearpath.$monthpath.$daypath)){
	mkdir($pathpart.$yearpath.$monthpath.$daypath,02755);
	@chmod($pathpart.$yearpath.$monthpath.$daypath,02755);
	@chown($pathpart.$yearpath.$monthpath.$daypath,'apache');
	//@chgrp($pathpart.$yearpath.$monthpath,'competiscan_web');
}

$local_dir = $pathpart.$yearpath.$monthpath.$daypath;

$root_dirs = array('./test');
$processed_time = time()-345600;

//$query = $DRW->query("SELECT SQL_NO_CACHE UNIX_TIMESTAMP(MAX(last_modified)) FROM chicagorecords",$DRW_read);
//$rows = $DRW->fetch_row($query);
$max_last_modified_ts = 0;//$rows[0];
//$max = 10;
//$curr = 0;

$parr = array();
$sql = "SELECT age_pID,age_pmin FROM cscan_age_product ORDER BY age_psort";
$result = $DRW->query($sql,$DRW_read);
while( $row = $DRW->fetch_row( $result ) ){
	$parr[$row[0]] = $row[1];
}
@$DRW->free_result($result);

$tries = 10;
$try = 0;
do{
	$connected = false;
	$conn_id = connectFTP($ftp_server, $ftp_user_name, $ftp_user_pass);
	if($conn_id!==false){
		$connected = true;
		
		$files = array();
		$filesDate = array();
		$filesRename = array();
		
		foreach($root_dirs as $rd){
			getFiles($rd);
		}
		
		$proc = './PROCESSED';
		if(!ftp_chdir($conn_id, $proc)){
			ftp_mkdir($conn_id, $proc);
		}
		ftp_chdir($conn_id, '/');
		foreach($filesRename as $fr){
			if(!ftp_rename($conn_id,'.'.$fr, $proc.$fr)){
				$ehL->write("Could not rename: ".$fr.' to '.$proc.$fr);
			}
		}
		
		if($conn_id!==false){
			ftp_close($conn_id);
		}
	}
	if(!$connected){
		sleep(300);
	}
	$try++;
} while(!$connected && $try<$tries);

if($try==$tries){
	$ehL->write("Could not log in.");
}

$ehL->stop();

function connectFTP($ftp_server, $ftp_user_name, $ftp_user_pass){
	$conn_id = ftp_connect($ftp_server,21,90);// or echo("Couldn't connect to $ftp_server\n");
	if($conn_id){
		$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);// or echo("You do not have access to this ftp server\n");
		if($login_result){
			ftp_pasv($conn_id, true);
			return $conn_id;
		}
	}
	return false;
}
function checkFTPConnection(){
	if($GLOBALS['conn_id']!==false){
		$check = ftp_raw($GLOBALS['conn_id'], "NOOP");
		if(!is_array($check) || !in_array('200 OK',$check)){
			ftp_close($GLOBALS['conn_id']);
			$GLOBALS['conn_id'] = connectFTP($GLOBALS['ftp_server'], $GLOBALS['ftp_user_name'], $GLOBALS['ftp_user_pass']);
			if($GLOBALS['conn_id']===false){
				$GLOBALS['connected'] = false;
			}
		}
	}
}
function getFiles($dir='.'){
	global $DRW,$DRW_main,$DRW_read;
	checkFTPConnection();
	if($GLOBALS['conn_id']!==false){
		$contents = ftp_nlist($GLOBALS['conn_id'], $dir);
		
                
		if($contents!==false){
			foreach ($contents as $file) {
				if($GLOBALS['conn_id']!==false){
					if(strpos($file,'/PROCESSED')===false){
						if(ftp_size($GLOBALS['conn_id'], $file)==-1){
							getFiles($file);
						}
						else{
							$mod_date = ftp_mdtm($GLOBALS['conn_id'], $file);
                                                        
                                                      //  echo $mod_date.'======'.$GLOBALS['max_last_modified_ts'];
							if($mod_date>=$GLOBALS['max_last_modified_ts'] && preg_match('/\\.pdf$/i',$file)){ //get zip?
								$file = preg_replace('/^\\.+/','',$file);
								$query = $DRW->query("SELECT SQL_NO_CACHE COUNT(*) FROM chicagorecords WHERE filename='".$DRW->real_escape_string($file)."'",$DRW_read);

								$rows = $DRW->fetch_row($query);
								 $numrows = $rows[0];
                                                               // echo"num";
								@$DRW->free_result($query);
								if($numrows==0) {
									$GLOBALS['files'][] = $file;
									$GLOBALS['filesDate'][] = $mod_date;
									
									if($GLOBALS['conn_id']!==false){
										$last_mod = date("Y-m-d H:i:s", $mod_date);
										
										$sqlc = "INSERT INTO chicagorecords SET filename='".$DRW->real_escape_string($file)."',last_modified='".$last_mod."',local_dir='".$GLOBALS['local_dir']."'";
										$DRW->query($sqlc,$DRW_main);
										$id = $DRW->insert_id($DRW_main);
										
										$local_file = "{$GLOBALS['local_dir']}pdf_{$id}.pdf";
										
										if(!ftp_get($GLOBALS['conn_id'], $local_file, $file, FTP_BINARY, 0)) {
											echo "There was a problem transfering file $file to $local_file\n";
											$sqlc = "DELETE FROM chicagorecords WHERE id=$id";
											$DRW->query($sqlc,$DRW_main);
											checkFTPConnection();
										}
										else{
											chmod($local_file,0644);
											processCRM();
										}
									}
                                                                        //echo $file;
                                                                        if (ftp_delete($GLOBALS['conn_id'], $file))
                                                                            {
                                                                            echo "$file deleted";
                                                                            }
                                                                          else
                                                                            {
                                                                            echo "Could not delete $file";
                                                                            }
//                                                                        
                                                                        
                                                                        
                                                                        
								}
								else{
									if($mod_date<$GLOBALS['processed_time']){
										if(preg_match('/(\\/[^\\/]+\\/[^\\/]+)\\//',$file,$matches)){
											if(!in_array($matches[1],$GLOBALS['filesRename'])){
												$GLOBALS['filesRename'][] = $matches[1];
											}
										}
									}
								}
							}
						}
					}
				}
                               // echo"success";exit;
			}
		}
	}
}
function processCRM(){
	global $DRW,$DRW_main,$DRW_read;
	$query = $DRW->query("SELECT SQL_NO_CACHE id,filename,last_modified,local_dir FROM chicagorecords WHERE productID=0",$DRW_read);
	while($row = $DRW->fetch_row($query)) {
		$id = $row[0];
		$ofilename = $row[1];
		$last_modified = $row[2];
		$local_dir = $row[3];
		$local_filename = "{$local_dir}pdf_{$id}.pdf";
		
		error_log("CRONJOB_FTP2: processCRM $local_filename");
		if(is_file($local_filename)){
			if(preg_match('/\\/(\\d+\\-\\d+\\-\\d+)\\/([^\\/]+)\\/([^\\/]+)\\//',$ofilename,$matches)){
				$scan_date = $matches[1];
				$DMSource = $matches[2];
				$operator = $matches[3];
			}
			elseif(preg_match('/\\/([^\\/]+)$/',dirname($ofilename),$matches)){
				$DMSource = $matches[1];
			}
			else {
				$DMSource = basename($ofilename);
			}
			
			$sectorID = '';
			$offerOrigin = '2';
			$lofilename = strtolower($ofilename);
			if(strpos($lofilename,'telecom')!==false || strpos($lofilename,'_tc_')!==false){
				$productStatus = 9;
				$sectorID = '9';
				$offerOrigin = '3';
			}
			elseif(strpos($lofilename,'_tl_')!==false){
				$productStatus = 219;
				$sectorID = '219';
				$offerOrigin = '3';
			}
			elseif(strpos($lofilename,'retail')!==false || strpos($lofilename,'_rl_')!==false){
				$productStatus = 266;
				$sectorID = '266';
				$offerOrigin = '3';
			}
			elseif(strpos($lofilename,'energy')!==false || strpos($lofilename,'_en_')!==false){
				$productStatus = 315;
				$sectorID = '315';
				$offerOrigin = '3';
			}
			elseif(strpos($lofilename,'noncore')!==false|| strpos($lofilename,'_nc_')!==false){
				$productStatus = 6;
				$offerOrigin = '3';
			}
			else {
				$productStatus = 5;
			}
			if(strpos($lofilename,'highpriority')!==false || strpos($lofilename,'_hp_')!==false){
				$product_priority = 1;
			}
			else {
				$product_priority = 0;
			}
			if(strpos($lofilename,'affinion')!==false || strpos($lofilename,'_af_')!==false){
				$is_affinion = 1;
				$productStatus = 6;
				$offerOrigin = '3';
			}
			else{
				$is_affinion = 0;
			}
			if(strpos($lofilename,'_sh_')!==false){
				$special_handling = 1;
			}
			else {
				$special_handling = 0;
			}
			if(strpos($lofilename,'_ci_')!==false || strpos($lofilename,'consumer_insights')!==false){
				$consumer_insights = 1;
			}
			else {
				$consumer_insights = 0;
			}
			if(strpos($lofilename,'citi')!==false || strpos($lofilename,'_cp_')!==false){
				$is_citi = 1;
				$productStatus = 11;
				$offerOrigin = '3';
			}
			else{
				$is_citi = 0;
			}
			
			$p_id = 0;
			$is_subp = 0;
			$pprimary = 1;
			
			$ppage = 0;
			$ppstateID = 0;
			$pgender = 'N';
			$homeownershipID = 0;
			$pincomeID = 0;
			$ppageID = 0;
			$ppfico_score = 0;
			$ownbiz = 0;
			$pppostalcode = '';
			$mChannelID = 1;
			$mPanelID = 1;
			$delmethid = 1;
			
			if(preg_match('/^(\\d+)_(\\d+)_(\\d+)/',$DMSource,$matches)){
				$competi_id = $matches[1].'-'.$matches[2].'-'.$matches[3];
				$defs = "SELECT panelist_id,parent_panelist_id FROM cscan_panelists WHERE competi_id='$competi_id'";
				if(preg_match('/^\\d{3}\\-/',$competi_id)){
					$defs .= " OR competi_id='0$competi_id'";
				}
				$defs .= " ORDER BY competi_id ASC,active DESC LIMIT 1";
				$resultD = $DRW->query($defs,$DRW_read);
				$dataD = $DRW->fetch_row($resultD);
				$p_id = (int)$dataD[0];
				$parent_panelist_id = (int)$dataD[1];
				if($parent_panelist_id>0){
					$is_subp = 1;
					$pprimary = 0;
				}
				@$DRW->free_result($resultD);
				
				if($p_id!=0){
					$defs = "SELECT DATEDIFF(CURDATE(),birthdate) as agedays,stateID,gender,homeownershipID,incomeID,fico_score,contactTypeID,ownbiz,postalcode
						FROM cscan_panelists WHERE panelist_id=".(float)$p_id;
					$resultD = $DRW->query($defs,$DRW_read);
					$dataD = $DRW->fetch_row($resultD);
					$ppage = floor($dataD[0]/365);
					$ppstateID = $dataD[1];
					$pgender = strtoupper(substr(trim($dataD[2]),0,1));
					if($pgender=='') $pgender = 'N';
					$homeownershipID = $dataD[3];
					$pincomeID = $dataD[4];
					$ppfico_score = $dataD[5];
					$contactTypeID = $dataD[6];
					$ownbiz = (int)$dataD[7];
					$pppostalcode = trim($dataD[8]);
					if($contactTypeID==1){
						$mPanelID = 4;
					}
					elseif($contactTypeID==2){
						$mPanelID = 1;
					}
					
					foreach($GLOBALS['parr'] as $pid=>$min){
						if($ppage>=$min){
							$ppageID = $pid;
						}
						else{
							break;
						}
					}
					@$DRW->free_result($resultD);
				}
			}
			
			$pdffile_arr=array();
			if(preg_match('/\\.zip$/i',$ofilename)){  // only of its a zip file..extract it
				$zip = new ZipArchive;
				if ($zip->open($local_filename) === TRUE) {
					for ($i=0; $i<$zip->numFiles;$i++) {
						$filename=$zip->getNameIndex($i);
						if(preg_match('/\.pdf$/i',$filename)){ // only if its a pdf
							$pdffile_arr[]=$filename;
						}
					}
					$zip->extractTo($local_dir,$pdffile_arr);
					$zip->close();
					if(file_exists($local_filename)) {
						//unlink($local_filename);       // delete the zip file after extracting it
					}
				}
				else {
					echo "Failed to open file $local_file \n";
				}
			}
			else{
				$pdffile_arr[] = $local_filename;
			}
			foreach ($pdffile_arr as $filename) {
				$entryId = '';//generate_entryID(true,$last_modified);
				$sqlc = "INSERT INTO cscan_product_detail SET productStatus=$productStatus,firstSeen='".substr($last_modified,0,10)."',lastSeen='".substr($last_modified,0,10)."',actual_addedToDatabase=NOW(),addedToDatabase=NOW(),mChannelID=$mChannelID,mPanelID=$mPanelID,state='$ppstateID',gender='$pgender',incomeID='$pincomeID',age='$ppageID',entryID='".$entryId."',DMSource='".$DRW->real_escape_string($DMSource)."',product_priority=$product_priority,delmethid=$delmethid,sectorID='$sectorID',is_affinion=$is_affinion,offerOrigin='$offerOrigin',special_handling=$special_handling,consumer_insights=$consumer_insights,is_citi=$is_citi,is_subp=$is_subp";
				$DRW->query($sqlc,$DRW_main);
				$pdtID = $DRW->insert_id($DRW_main);
				
				if(!empty($sectorID)){
					$sid = $sectorID;
				}
				else{
					$sid = '0';
				}
				$sqlU = "INSERT IGNORE INTO cscan_scsc_product (productID,scsc_sectorID,scsc_categoryID,scsc_subCategoryID) VALUES ($pdtID,$sid,0,0)";
				$DRW->query($sqlU,$DRW_main);
				
				$sqlp = "UPDATE chicagorecords SET productID=$pdtID WHERE id=$id";
				$DRW->query($sqlp,$DRW_main);
				
				$path = dirname($filename);
				if(!preg_match('/\\/$/',$path)){
					$path .= '/';
				}
				$fname = basename($filename);
				
				$newpath = $path.$pdtID.'/';
				if(!is_dir($newpath)){
					mkdir($newpath,02755);
					@chmod($newpath,02755);
					@chown($newpath,'apache');
					//@chgrp($newpath,'competiscan_web');
				}
				if(rename($path.$fname,$newpath.$fname)){
					$filename = $newpath.$fname;
					@chmod($filename,02755);
					@chown($filename,'apache');
					//@chgrp($filename,'competiscan_web');
				}
				else{
					$newpath = $path;
				}
				
				$pdfPath = '';
				$pos = strpos($path,'/PDF/crm/');
				if($pos!==false){
					$pdfPath = substr($path,$pos);
				}
				
				createPreviewJPG($newpath,$fname,$pdtID);
				$document_id = savePDFData($pdtID,$newpath,$fname,'',$pdfPath.$pdtID.'/',true);
				
				if($p_id!=0){
					$sqlU = "INSERT IGNORE INTO cscan_panelists_product (productID,panelist_id,ppdate,ppage,ppstateID,pgender,homeownershipID,pincomeID,ppageID,ppfico_score,isBiz,pppostalcode,ppaddeddate,pprimary) 
						VALUES ($pdtID,".(float)$p_id.",'$last_modified',$ppage,$ppstateID,'$pgender',$homeownershipID,$pincomeID,$ppageID,$ppfico_score,$ownbiz,'".$DRW->real_escape_string($pppostalcode)."',NOW(),$pprimary)";
					$DRW->query($sqlU,$DRW_main);
				}
				updateStateLookup($pdtID);
				
				$sql = "INSERT IGNORE INTO `cscan_admin_log` SET userID=0,logDate=NOW(),productID=$pdtID";
				$DRW->query($sql,$DRW_main);
			}
		}
	}
	@$DRW->free_result($query);
}
?>
