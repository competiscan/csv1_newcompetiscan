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

$sql = "SELECT id,file_path,file_name FROM cscan_digital_files where status=0 ORDER BY id";
$result = $DRW->query($sql,$DRW_read2);
if($DRW->num_rows($result)>0){
    while( $row = $DRW->fetch_row($result)){
        $file_id = $row[0];
        $file_path=$row[1];
        $file_name=$row[2];
        $num =1;
        $file_open = dirname(__FILE__)."/".$file_path."/".$file_name;
        if(file_exists($file_open)){
            @chmod($file_open,0777);
            @chown($file_open,'apache');
        }        
        
        $coltotal = 12;
        if (($handle = fopen($file_open, "r")) !== FALSE) {
            while (!feof($handle)) {
                $line = trim(fgets($handle, 4096));
                if($line!=''){
                    $lineArray = str_getcsv(trim($line), '"');
                   
                    $colcount = count($lineArray);
                    array_walk($lineArray, 'trim_value');
                    if($colcount>$coltotal){
                            $lineArray = array_slice($lineArray, 0, $coltotal);
                    }
                    elseif($colcount<$coltotal){
                            $lineArray = array_pad($lineArray, $coltotal, '');
                    }
                    foreach($lineArray as $key=>$value){
                            $lineArray[$key] = preg_replace('/^"(.+)"$/s','$1',$lineArray[$key]);
                            $lineArray[$key] = preg_replace('/""/','"',$lineArray[$key]);
                    }                    
                    
                    if($num==1){                        
                        if(strtolower(trim($lineArray[0]))!="creation_date" && strtolower(trim($lineArray[1]))!="location" && strtolower(trim($lineArray[2]))!="channel" && strtolower(trim($lineArray[3]))!="advertiser_name" && strtolower(trim($lineArray[4]))!="advertiser_domain" && strtolower(trim($lineArray[5]))!="campaign_title"
                           && strtolower(trim($lineArray[6]))!="campaign_landing_page" && strtolower(trim($lineArray[7]))!="creative_wrapper" && strtolower(trim($lineArray[8]))!="publisher" && strtolower(trim($lineArray[9]))!="monitored_page" && strtolower(trim($lineArray[10]))!="impressions" && strtolower(trim($lineArray[11]))!="spend"
                          ){                               
                            fclose($handle);            
                            #################################### Start upload on S3 bucket ###########################################
                            $result = $s3->putObject([
                                'Bucket' => $bucket_name,
                                'Key'    => $file_path."/".$file_name,
                                'SourceFile' => $file_open,
                                'ACL'    => 'public-read',
                                'ContentType'   => 'text/csv',
                                'Metadata'      => array(
                                    'string'        => 'string'
                                    )
                            ]);

                            if(isset($result['@metadata']['statusCode']) && $result['@metadata']['statusCode'] == 200){
                                            
                                if (file_exists($file_open) ){ 
                                    unlink($file_open);
                                }
                                $sql_updt = "update cscan_digital_files set status=1 where id='".$file_id."'";
                                $result_updt = $DRW->query($sql_updt,$DRW_main);
                                break;
                            }
                            #################################### End upload on S3 bucket ###########################################
                            


                            
                        }
                    }else if($num>1 && $lineArray[0]!='' && $lineArray[1]!='' && $lineArray[2]!='' && $lineArray[5]!='' && $lineArray[7]!=''){
                        $creation_date  = trim($lineArray[0]);
                        $location       = trim($lineArray[1]);
                        $channel        = trim($lineArray[2]);
                        $advertiser_name = trim($lineArray[3]);
                        $advertiser_domain= trim($lineArray[4]);
                        $campaign_title   = trim($lineArray[5]);
                        $campaign_landing_page=trim($lineArray[6]);
                        $creative_wrapper = trim($lineArray[7]);
                        $publisher       = trim($lineArray[8]);
                        $monitored_page  = trim($lineArray[9]);
                        $impressions     = trim($lineArray[10]);
                        $spend           = trim($lineArray[11]);
                        
                        //$sql_chk = "SELECT id FROM cscan_digital_records where location='".$location."' AND impressions='".$impressions."' AND spend='".$spend."' AND creative_wrapper='".$DRW->real_escape_string($creative_wrapper)."'";
                        //$result_chk = $DRW->query($sql_chk,$DRW_read2);
                        //if($DRW->num_rows($result_chk)<=0){
                            $sql_ins = "Insert into cscan_digital_records (file_id,creation_date,location,channel,advertiser_name,advertiser_domain,compaign_title,campaign_landing_page,creative_wrapper,publisher,monitored_page,impressions,spend) 
                                        values('".$file_id."','".$creation_date."','".$DRW->real_escape_string($location)."','".$DRW->real_escape_string($channel)."','".$DRW->real_escape_string($advertiser_name)."','".$DRW->real_escape_string($advertiser_domain)."','".$DRW->real_escape_string($campaign_title)."','".$DRW->real_escape_string($campaign_landing_page)."','".$DRW->real_escape_string($creative_wrapper)."','".$DRW->real_escape_string($publisher)."','".$DRW->real_escape_string($monitored_page)."','".$DRW->real_escape_string($impressions)."','".$DRW->real_escape_string($spend)."')";
                            $result_ins = $DRW->query($sql_ins,$DRW_main);
                        //}
                    }
                }
                
                $num++;
            }                    
            fclose($handle);
            
            #################################### Start upload on S3 bucket ###########################################
			$result = $s3->putObject([
	            'Bucket' => $bucket_name,
	            'Key'    => $file_path."/".$file_name,
	            'SourceFile' => $file_open,
	            'ACL'    => 'public-read',
	            'ContentType'   => 'text/csv',
	            'Metadata'      => array(
                    'string'        => 'string'
                    )
	        ]);

        	if(isset($result['@metadata']['statusCode']) && $result['@metadata']['statusCode'] == 200){
				               
                if (file_exists($file_open) ){ 
                    unlink($file_open);
                }
				$sql_updt = "update cscan_digital_files set status=2 where id='".$file_id."'";
                $result_updt = $DRW->query($sql_updt,$DRW_main);
			}
			#################################### End upload on S3 bucket ###########################################
                      
            
        }else{
            fclose($handle);            
            #################################### Start upload on S3 bucket ###########################################
            $result = $s3->putObject([
                'Bucket' => $bucket_name,
                'Key'    => $file_path."/".$file_name,
                'SourceFile' => $file_open,
                'ACL'    => 'public-read',
                'ContentType'   => 'text/csv',
                'Metadata'      => array(
                    'string'        => 'string'
                    )
            ]);

            if(isset($result['@metadata']['statusCode']) && $result['@metadata']['statusCode'] == 200){
                            
                if (file_exists($file_open) ){ 
                    unlink($file_open);
                }
                $sql_updt = "update cscan_digital_files set status=1 where id='".$file_id."'";
                $result_updt = $DRW->query($sql_updt,$DRW_main);                
            }
            #################################### End upload on S3 bucket ###########################################
            
            echo "Unable to open file: ".$file_open;
        }
        echo $num.' file records completed';
        die;
    }
}

echo 'Completed...';
die;
?>
