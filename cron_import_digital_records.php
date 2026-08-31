<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set("memory_limit", "-1");
set_time_limit(0);

require_once("includes/ehLog_set.php");
$ehL->start(__FILE__);

require_once ("includes/dbcon.php");
$DRW->databaseReadWrite_die = 1;
include_once 'includes/functions.php';
include_once 'includes/thumb.php';
require_once "Mail.php";
require_once "Mail/mime.php";

echo 'Start: '.date("Y-m-d H:i:s").'</br></br>';
if(!defined('ENV')){
    define('ENV',getenv('SERVER_NAME'));
}

$img_arr=array('png','jpg','gif','jpeg');
$vid_arr=array('mp4','mov','avi','mkv','webm');

$sql = "SELECT id,creation_date,location,channel,advertiser_name,compaign_title,creative_wrapper,publisher,impressions,spend,monitored_page FROM cscan_digital_records where status=0 ORDER BY id limit 5000 ";
$result = $DRW->query($sql,$DRW_read2);
if($DRW->num_rows($result)>0){
    while( $row = $DRW->fetch_row($result)){
        $id              = trim($row[0]);
        $creation_date   = date('Y-m-d h:i:s',strtotime(trim($row[1])));
        $location        = trim($row[2]);
        $channel         = trim($row[3]);
        $advertiser_name = trim($row[4]);
        $compaign_title  = trim($row[5]);
        $creative_wrapper= trim($row[6]);
        $publisher       = trim($row[7]);
        $impressions     = trim($row[8]);
        $spend           = trim($row[9]);
        $monitored_page  = trim($row[10]);
        $state_code='';
        $country='';
        $updt=0;
        if($location!=''){
            if(strstr($location,',')){
                 $state_code=trim(end(explode(',',$location)));
                 //echo 'state code: '.$state_code;
                 //echo '<br>';
            }else{
                $country=$location;
               // echo 'country: '.$country; die;
            }           
        }
        
        ## check for duplicate records
        $sqlChkDup = "SELECT id,productID FROM cscan_digital_records where status=1 AND LOWER(creative_wrapper)='".$DRW->real_escape_string($creative_wrapper)."'";
        $resultDup = $DRW->query($sqlChkDup,$DRW_read2);
        if($DRW->num_rows($resultDup)>0){
            
            $updt=1;
            $dataDup = $DRW->fetch_row($resultDup);
            $dupid=$dataDup[0];
            $dupPid=$dataDup[1];
            updateStateCountry($state_code,$country,$dupPid,$updt);
            //Assign panelist id with product
            
            assignPanelistWithProduct($state_code,$country,$dupPid,$location,$updt,$creation_date);
            
            //Spend Impression
            $qs = "SELECT id,spend,impression FROM cscan_digital_spend_impression WHERE productID=".$dupPid;
            $resultS = $DRW->query($qs, $DRW_read2);
            if($DRW->num_rows($resultS)>0){
                $dataSp = $DRW->fetch_row($resultS);
                $sid=$dataSp[0]; 
                $spend_old=$dataSp[1];
                $imp_old=$dataSp[2];
                $new_spend=$spend_old+$spend;
                $new_imp=$imp_old+$impressions;
                
                $sqlU = "UPDATE cscan_digital_spend_impression set spend='".$new_spend."',impression='".$new_imp."' where id='".$sid."'";
                $DRW->query($sqlU, $DRW_main);
                
            }else{
                $sqlU = "INSERT INTO cscan_digital_spend_impression (productID,spend,impression) 
                         VALUES ('".$dupPid."','".$spend."','".$impressions."')";
                $DRW->query($sqlU, $DRW_main);
            }         
           
            $sqlR = "Update cscan_digital_records set status=2,productID='".$dupPid."' where id='".$id."'";
            $DRW->query($sqlR, $DRW_main); 
            continue;
            
        }
       
        $ismobile=1;
        //Find Media channel
        $ext= pathinfo($creative_wrapper, PATHINFO_EXTENSION);
        if(in_array(strtolower($ext),$img_arr)){
            $mChannelID = 5;
        }elseif(in_array(strtolower($ext),$vid_arr)){
            $mChannelID = 10;
        }else{
            $mChannelID=0;
        }
        
        if(strstr(strtolower($channel),'mobile')){
            $ismobile=2;
        }else if(strstr(strtolower($channel),'in app android')){
            $ismobile=3;
        }else if(strstr(strtolower($channel),'in app ios')){
            $ismobile=4;
        }else if(strstr(strtolower($channel),'social')){
            $ismobile=5;
        }
        
        $entryId=generate_entryID(true,$creation_date);
        $entryID_sort1=str_replace('-','',substr($creation_date,0,10));
        $entryID_sort2=(int)end(explode('-',$entryId));
        $productStatus=12; 
        $is_digital=1;
        $mPanelID=1;
        $ppstateID='';
        $companyID='';
        $companyName='';
        if($advertiser_name!=''){
            $qcp = "SELECT competiscan_company from cscan_digital_company where LOWER(advertiser_name)='".strtolower($advertiser_name)."' limit 1";
            $resultCP = $DRW->query($qcp, $DRW_read2);
            if($DRW->num_rows($resultCP)>0){
                $dataCP = $DRW->fetch_row($resultCP);
                $cmpName=$dataCP[0];            
            }else{
                $cmpName=$advertiser_name;
            }     
            $qc = "SELECT companyID,companyName FROM cscan_company WHERE LOWER(companyName)='".$DRW->real_escape_string(strtolower($cmpName))."' limit 1";
            $resultC = $DRW->query($qc, $DRW_read2);
            if($DRW->num_rows($resultC)>0){
                $dataC = $DRW->fetch_row($resultC);
                $companyID=$dataC[0]; 
                $companyName=$dataC[1];
            } 
        }
        
        $sqlp = "INSERT INTO cscan_product_detail
                    SET productStatus='".$productStatus."',
                    firstSeen='".substr($creation_date,0,10)."',
                    lastSeen='".substr($creation_date,0,10)."',
                    actual_addedToDatabase=NOW(),
                    addedToDatabase=NOW(),
                    mChannelID='".$mChannelID."',
                    mPanelID='".$mPanelID."',
                    state='".$ppstateID."',                    
                    entryID='".$entryId."',
                    entryID_sort1='".$entryID_sort1."',
                    entryID_sort2='".$entryID_sort2."',     
                    productHeadline='".$DRW->real_escape_string($compaign_title)."',
                    company='".$DRW->real_escape_string($companyName)."',
                    traffic_sources='".$DRW->real_escape_string($monitored_page)."',
                    simple_domain='".$DRW->real_escape_string(remove_http($publisher))."',
                    is_digital='".$is_digital."',
                    is_mobile='".$ismobile."'";
                        
        //die;          
        if($DRW->query($sqlp,$DRW_main)){
           echo $pdtID = $DRW->insert_id($DRW_main);
            // Migrate Company
            if($companyID!=''){
                $sqlU = "INSERT IGNORE INTO cscan_company_product (productID,companyID,primary_co) 
                         VALUES ($pdtID," . (float) $companyID . ",1)";
                $DRW->query($sqlU, $DRW_main);
                $img_content_type = "image/$ext";
                if($mChannelID==10){
                    $img_content_type ="video/$ext";
                }
                $qchk_img = "SELECT companyID FROM cscan_img_company WHERE companyID='".$companyID."' limit 1";
                $resultChkImg = $DRW->query($qchk_img, $DRW_read2);
                
                if($DRW->num_rows($resultChkImg)>0){
                    
                    $sqlImg = "REPLACE INTO cscan_img (productID,img_id,img_createddate,img_companyID)
                            VALUES ('".$pdtID."',1,NOW(),'".$companyID."')";
                    $DRW->query($sqlImg, $DRW_main);
                }
                
                //saveImageData($pdtID, '', '', '', $companyID);
            }
            
            //Migrate ad product image
            downloadimg_digital($pdtID,$creative_wrapper,$ext,$mChannelID);            
            
            //Migrate state and country
            updateStateCountry($state_code,$country,$pdtID,$updt);
            
            // Asign Panelist with product
            assignPanelistWithProduct($state_code,$country,$pdtID,$location,$updt,$creation_date);
            
            //Spend Impression
            $qs = "SELECT id FROM cscan_digital_spend_impression WHERE productID='".$pdtID."'";
            $resultS = $DRW->query($qs, $DRW_read2);
            if($DRW->num_rows($resultS)<=0){
                $sqlU = "INSERT INTO cscan_digital_spend_impression (productID,spend,impression) 
                         VALUES ('".$pdtID."','".$spend."','".$impressions."')";
                $DRW->query($sqlU, $DRW_main);
                
            }
            
            $sqlR = "Update cscan_digital_records set status=1,productID='".$pdtID."' where id='".$id."'";
            $DRW->query($sqlR, $DRW_main);           
                
        }
        //echo 'completed: ';        
        //die;
        
    }
    
    echo 'completed: ';
    echo 'End: '.date("Y-m-d H:i:s").'</br></br>';
    die;
}

function remove_http($url) {
   $disallowed = array('http://', 'https://', 'www.');
   foreach($disallowed as $d) {
      if(strpos($url, $d) === 0) {
         return str_replace($d, '', $url);
      }
   }
   return $url;
}

function downloadimg_digital($productID,$source_path,$ext,$mChannelID) {
    global $DRW, $DRW_read, $DRW_main, $DRW_digital;
    $AUTH_DATA = $GLOBALS['AUTH_DATA'];
    $message=0;            
    if ($source_path!= '') {        
        $file_type= strtolower(trim($dataC[1]));        
        $yearpath = date('Y/');
        $monthpath = date('m/');
        $datepath = $yearpath . $monthpath;

        $root = dirname(__FILE__);    
        $imgpart = $root . '/PDF/';
        $imgPath = $imgpart.$datepath.$productID.'/';
        $savedbpath="/PDF/$datepath$productID/";

        $pathpart = $root . '/PDF/';

        if (!is_dir($pathpart . $yearpath)) {
            mkdir($pathpart . $yearpath, 02755);
        }
        if (!is_dir($pathpart . $datepath)) {
            mkdir($pathpart . $datepath, 02755);
        }
        if (!is_dir($imgPath)) {
            mkdir($imgPath, 02755);
        }       

        $saveto=$imgPath . '' . $productID . '.'.$ext;        

        if($ext=='html'){
            $content = file_get_contents($source_path);
            preg_match('/src="([^"]+)"/', $content, $match);
            $source_path = $match[1];
            $savefilename='';
            $saveimgtype='html';
            $savesize='';
            $savedbpath=$source_path;

        }else{

            $find_ext= grab_ad_image($source_path,$saveto); 
            if($find_ext!='' AND $mChannelID!='10'){
                $ext=$find_ext;
            }
            $savefilename=$productID.'.'.$ext; 
            $saveimgtype='image/'.$ext;
            if($mChannelID=='10'){
                $saveimgtype='video/'.$ext;
            }

            $imagesizes = get_headers($source_path, 1);
            $savesize = $imagesizes["Content-Length"]; 
        }   
        $sql = "REPLACE INTO cscan_img_document
            (productID, document_id, img_document_sort, img_document_filename, img_document_createddate,img_document_content_type, img_document_size_byte, img_document_createdby, img_document_default, img_document_path)
            VALUES
            ('$productID', '1', '1', '$savefilename', NOW(), '$saveimgtype', '$savesize', '0', '1', '$savedbpath')";
        $DRW->query($sql, $DRW_main);

        $sql = "REPLACE INTO cscan_document
           (productID, document_id, document_filename, document_createddate, document_createdby, document_content_type, document_size_byte, document_path, document_placement)
           VALUES
           ('$productID', '1', '$savefilename', NOW(), '0', '$saveimgtype', '$savesize', '$savedbpath','')";
        $DRW->query($sql, $DRW_main);       

        $message = 1;
    }    
    
    return $message;
}

function grab_ad_image($source_url,$saveto){
    global $s3,$bucket_name;
    $mod_ext='';
    $ch = curl_init ($source_url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
    $raw=curl_exec($ch);            
    curl_close ($ch);
    if(strstr(strtolower($raw),'png')){
        $saveto=str_replace('.jpg','.png',$saveto);
        $mod_ext='png';
        $content_type = "image/png";
    }
    if(strstr(strtolower($raw),'jpeg')){
        $saveto=str_replace('.jpg','.jpeg',$saveto);
        $mod_ext='jpeg';
        $content_type = "image/jpeg";
    }if(strstr(strtolower($raw),'gif')){
        $saveto=str_replace('.jpg','.gif',$saveto);
        $mod_ext='gif';
        $content_type = "image/gif";
    }         


    if(file_exists($saveto)){
        unlink($saveto);
    }

    $fp = fopen($saveto,'x');
    fwrite($fp, $raw);
    fclose($fp);
    $keyname=strstr($saveto, 'PDF');            
    $result = $s3->putObject([
            'Bucket' => $bucket_name,
            'Key'    => $keyname,
            'SourceFile'  => $saveto,
            'ACL'         => 'public-read',
            'ContentType' => $content_type,
            'Metadata'    => array(
               'string'   => 'string'
             )
        ]);
    //unlink($saveto);            
    return $mod_ext;
}
function updateStateCountry($state_code,$country='',$pdtID,$updt=0){
    global $DRW, $DRW_read, $DRW_main, $DRW_digital;
    $AUTH_DATA = $GLOBALS['AUTH_DATA'];
    if($state_code!=''){
        $sqlS = "SELECT DISTINCT stateID,countryCode FROM cscan_state JOIN ISO31661_alpha2code ON (code=countryCode) WHERE LOWER(stateCode)='".strtolower($state_code)."' limit 1";
        $resS = $DRW->query($sqlS, $DRW_read2);
        if($DRW->num_rows($resS)>0){
            $dataS = $DRW->fetch_row($resS);
            $stateID=$dataS[0]; 
            $countryCode=$dataS[1];
            if($updt==1){
                $sqlchk = "SELECT productID from cscan_product_detail_state where stateID='".$stateID."' and productID='".$pdtID."'";                     
                $resChk = $DRW->query($sqlchk, $DRW_read2);
                if($DRW->num_rows($resChk)<=0){
                    
                    $sqlchkP = "SELECT state from cscan_product_detail where productID='".$pdtID."'";                     
                    $resChkP = $DRW->query($sqlchkP, $DRW_read2);
                    if($DRW->num_rows($resChkP)>0){
                        $dataP = $DRW->fetch_row($resChkP);
                        $stateID_db=$dataP[0];
                        $update_state=$stateID;
                        if(!empty($stateID_db)){
                            $arr_state= explode(",",$stateID_db);
                            if(!in_array($stateID, $arr_state)){
                                $arr_state[]=$stateID;
                                $update_state=implode(',',$arr_state);                                
                            }
                        }
                        $sqlP = "Update cscan_product_detail set state='".$update_state."',primary_country='".$countryCode."' where productID='".$pdtID."'";
                        $DRW->query($sqlP, $DRW_main);
                        
                    }
                    $sqlU = "INSERT INTO cscan_product_detail_state (productID,stateID,is_panelist,countryCode_copy) 
                     VALUES ('".$pdtID."','".$stateID."',0,'".$countryCode."')";
                    $DRW->query($sqlU, $DRW_main);
                                        
                }
                
                
            }else{
                $sqlU = "INSERT INTO cscan_product_detail_state (productID,stateID,is_panelist,countryCode_copy) 
                     VALUES ('".$pdtID."','".$stateID."',0,'".$countryCode."')";
                $DRW->query($sqlU, $DRW_main);
                $sqlP = "Update cscan_product_detail set state='".$stateID."',primary_country='".$countryCode."' where productID='".$pdtID."'";
                $DRW->query($sqlP, $DRW_main);
            }
        }
    }else if($country!=''){
        $sqlChkC = "SELECT code FROM ISO31661_alpha2code WHERE LOWER(country)='".strtolower($country)."' limit 1";
        $resChkC = $DRW->query($sqlChkC, $DRW_read2);
        if($DRW->num_rows($resChkC)>0){
            $dataChkC = $DRW->fetch_row($resChkC);
            $countryCode=$dataChkC[0];
            $sqlP = "Update cscan_product_detail set primary_country='".$countryCode."' where productID='".$pdtID."'";
            $DRW->query($sqlP, $DRW_main); 
        }                
    }
}

function assignPanelistWithProduct($state_code,$country='',$pdtID,$location,$updt=0,$creation_date=''){
    global $DRW, $DRW_read, $DRW_main, $DRW_digital;
    $AUTH_DATA = $GLOBALS['AUTH_DATA'];
    $panelist_id='';
    $competi_id='';
    $sugar_id='';
    if($creation_date==''){
        $creation_date=date('Y-m-d h:i:s');
    }
    
    if($location!=''){
        $sqlchk = "SELECT competi_id from cscan_digital_panelists where LOWER(location)='".strtolower(trim($location))."'";                    
        $resChk = $DRW->query($sqlchk, $DRW_read2);
        if($DRW->num_rows($resChk)>0){
            $dataPan = $DRW->fetch_row($resChk);
            $competi_id=$dataPan[0];
        }else{
            $sugar_id = create_sugar_id();
            $sql = "INSERT INTO cscan_consumer_inc (sugarcrm_id) VALUES ('".$DRW->real_escape_string($sugar_id)."')";
            $DRW->query($sql,$DRW_main);
            $parents = $DRW->insert_id($DRW_main);
            if($state_code!=''){
                $sqlS = "SELECT DISTINCT stateID,countryCode,panelist_stateID FROM cscan_state JOIN ISO31661_alpha2code ON (code=countryCode) WHERE LOWER(stateCode)='".strtolower(trim($state_code))."' limit 1";
                $resS = $DRW->query($sqlS, $DRW_read2);
                if($DRW->num_rows($resS)>0){
                    $dataS = $DRW->fetch_row($resS);
                    $stateID=$dataS[0]; 
                    $countryCode=$dataS[1];
                    $panelist_stateID=$dataS[2];
                    if($panelist_stateID=='' OR $panelist_stateID<1){
                        $panelist_stateID=$stateID;
                    }
                    if($panelist_stateID<10){
                        $panelist_stateID = sprintf("%02d", $panelist_stateID);
                    }
                    $competi_id=$parents.'-66-'.$panelist_stateID;
                                     
                }
            }
            /*
            else if(trim($country)!=''){
                if(strtolower(trim($country))=='united states'){
                    $competi_id=$parents.'-66-01';
                }elseif(strtolower(trim($country))=='canada'){
                    $competi_id=$parents.'-66-61';
                }
            } */
        }
        
        if($competi_id!=''){            
            $sqlchk = "SELECT panelist_id from cscan_digital_panelists where competi_id='".trim($competi_id)."'";                    
            $resChk = $DRW->query($sqlchk, $DRW_read2);
            if($DRW->num_rows($resChk)<=0){
                $sqlIns = "INSERT into cscan_digital_panelists (location,competi_id) values('".$DRW->real_escape_string($location)."','".$DRW->real_escape_string($competi_id)."')";
                $DRW->query($sqlIns,$DRW_main);
            }         
            
            $sqlchk = "SELECT panelist_id from cscan_panelists where competi_id='".trim($competi_id)."'";                    
            $resChk = $DRW->query($sqlchk, $DRW_read2);
            if($DRW->num_rows($resChk)>0){
                $dataS = $DRW->fetch_row($resChk);
                $panelist_id=$dataS[0]; 
            }else{
                if($sugar_id==''){
                    $sugar_id = create_sugar_id();
                }
                $first_name='Biscience';
                $last_name='Panelist';
                $phone='1234567890';
                $contact_type='cons_panelist';
                $gender='M';
                $income='Under $25k';
                $email='biscience@gmail.com';
                $address=$location;
                if(strstr(',',$location)){
                    $loc_arr=explode(',',$location);
                    $city=($loc_arr[count($loc_arr)-2]);
                }else{
                    $city=$location;
                }                
                $state=$state_code;
                $sql_ins="Insert into cscan_panelists (sugar_id,first_name,last_name,phone,contact_type,gender,income,competi_id,email,address,city,state) values('".$sugar_id."','".$first_name."','".$last_name."','".$phone."','".$contact_type."','".$gender."','".$income."','".$competi_id."','".$email."','".$address."','".$city."','".$state."')";
                $DRW->query($sql_ins,$DRW_main);
                $panelist_id = $DRW->insert_id($DRW_main);
            }
        
        }
        if($panelist_id!=''){
            $sqlchk = "SELECT productID from cscan_panelists_product where productID='".$pdtID."' and panelist_id='".$panelist_id."'";                     
            $resChk = $DRW->query($sqlchk, $DRW_read2);
            if($DRW->num_rows($resChk)<=0){
                $stateID='';
                if($state_code!=''){
                    $sqlS = "SELECT DISTINCT stateID FROM cscan_state JOIN ISO31661_alpha2code ON (code=countryCode) WHERE LOWER(stateCode)='".strtolower($state_code)."' limit 1";
                    $resS = $DRW->query($sqlS, $DRW_read2);
                    if($DRW->num_rows($resS)>0){
                        $dataS = $DRW->fetch_row($resS);
                        $stateID=$dataS[0]; 
                    }
                }
                
                $sql_ins="Insert into cscan_panelists_product (panelist_id,productID,ppstateID,ppdate) values('".$panelist_id."','".$pdtID."','".$stateID."','".$creation_date."')";
                $DRW->query($sql_ins,$DRW_main);
            }
            $sqlUpdt = "Update cscan_digital_panelists set panelist_id='".$panelist_id."' where competi_id='".$competi_id."'";
            $DRW->query($sqlUpdt,$DRW_main); 
            
        }
    }
}

//@return String containing a sugar id in the format: aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee
function create_sugar_id()
{
    $microTime = microtime();
	list($a_dec, $a_sec) = explode(" ", $microTime);

	$dec_hex = sprintf("%x", $a_dec* 1000000);
	$sec_hex = sprintf("%x", $a_sec);

	ensure_length($dec_hex, 5);
	ensure_length($sec_hex, 6);

	$sugar_id = "";
	$sugar_id .= $dec_hex;
	$sugar_id .= create_sugar_id_section(3);
	$sugar_id .= '-';
	$sugar_id .= create_sugar_id_section(4);
	$sugar_id .= '-';
	$sugar_id .= create_sugar_id_section(4);
	$sugar_id .= '-';
	$sugar_id .= create_sugar_id_section(4);
	$sugar_id .= '-';
	$sugar_id .= $sec_hex;
	$sugar_id .= create_sugar_id_section(6);

	return $sugar_id;
}
function create_sugar_id_section($characters)
{
	$return = "";
	for($i=0; $i<$characters; $i++)
	{
		$return .= sprintf("%x", mt_rand(0,15));
	}
	return $return;
}
function ensure_length(&$string, $length)
{
	$strlen = strlen($string);
	if($strlen < $length)
	{
		$string = str_pad($string,$length,"0");
	}
	else if($strlen > $length)
	{
		$string = substr($string, 0, $length);
	}
}

echo 'Completed...';
die;
?>
