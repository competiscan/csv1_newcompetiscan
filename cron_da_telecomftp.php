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

echo 'Start: '.date("Y-m-d H:i:s").'</br></br>';
function pr($str){
    echo '<pre>';print_r($str);
}
##############################################################
die("One Time Cron");
die;
$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT productID,DMSource FROM cscan_product_detail
        WHERE productStatus=9 
        AND ( ( (CONCAT(',',sectorID,',') REGEXP ',0,' )
        OR (CONCAT(',',sectorID,',') REGEXP ',,' ) )
        OR (CONCAT(',',sectorID,',') REGEXP ',4,')
        OR (CONCAT(',',sectorID,',') REGEXP ',5,')
        OR (CONCAT(',',sectorID,',') REGEXP ',6,')
        OR (CONCAT(',',sectorID,',') REGEXP ',9,')
        OR (CONCAT(',',sectorID,',') REGEXP ',87,')
        OR (CONCAT(',',sectorID,',') REGEXP ',90,')
        OR (CONCAT(',',sectorID,',') REGEXP ',219,')
        OR (CONCAT(',',sectorID,',') REGEXP ',262,')
        OR (CONCAT(',',sectorID,',') REGEXP ',266,')
        OR (CONCAT(',',sectorID,',') REGEXP ',315,')
        OR (CONCAT(',',sectorID,',') REGEXP ',372,'))
        AND mChannelID<>5 AND mChannelID<>10
        AND mChannelID<>9
        AND is_citi<>1
        AND consumer_insights<>1
        ORDER BY product_priority DESC,special_handling DESC,actual_addedToDatabase DESC";

$query = $DRW->query($sql,$DRW_read);
$num = $DRW->num_rows( $query );
if($num > 0){
    $inputFiles = array();
    $n = 0;
    $fileNotAvailable = $fileAlreadyAvailable = array();
    while( $row = $DRW->fetch_assoc( $query ) ){
        $n++;
        $arrCsv = array();
        $productID = $row['productID'];
        $DMSource = $row['DMSource'];
        //$productID = 2050446;
        if($productID){
            $sql1 = "SELECT document_filename, document_createddate, document_path FROM cscan_document WHERE productID = '".$productID."'";
            //echo $sql1;die;
            $query1 = $DRW->query($sql1,$DRW_read);
            if($DRW->num_rows( $query1 ) > 0){
                $row1 = $DRW->fetch_assoc( $query1 );
                $pdfName = $row1['document_filename'];
                $pdfDate = date("Y-m-d", strtotime($row1['document_createddate']));
                $pdfPath = $row1['document_path'];
                
                $absPath = dirname(__FILE__).$pdfPath.$pdfName;
                //echo $absPath;die;
                $moveFrom = $moveTo = '';
                if(file_exists($absPath)){
                    $moveFrom = $absPath;
                    
                    $sql2 = "SELECT filename, local_dir, crm_import_date FROM chicagorecords WHERE productID = '".$productID."'";
                    $query2 = $DRW->query($sql2,$DRW_read);
                    $ftp_date = $copyname = '';
                    if($DRW->num_rows( $query2 ) > 0){
                        $row2 = $DRW->fetch_assoc( $query2 );
                        $ftp_date = $row2['crm_import_date'];
                        $chicago_file = str_replace('output', 'dachicagorecordsftp', $row2['filename']);
                        //echo $chicago_file;die;
                        if(preg_match('/\\/(\\d+\\-\\d+\\-\\d+)\\/([^\\/]+)\\/([^\\/]+)\\//',$chicago_file,$matches)){
                            $DMSource = $matches[2];
                        }elseif(preg_match('/\\/([^\\/]+)$/',dirname($chicago_file),$matches)){
                            $DMSource = $matches[1];
			}else {
                            $DMSource = basename($chicago_file);
			}
                        //echo $DMSource;die;                        
                        if(preg_match('/^(\\d+)_(\\d+)_(\\d+)/',$DMSource,$matches)){
                            $competi_id = $matches[1].'-'.$matches[2].'-'.$matches[3];
                            $defs = "SELECT panelist_id FROM cscan_panelists WHERE competi_id='".$competi_id."'";
                            if(preg_match('/^\\d{3}\\-/',$competi_id)){
                                $defs .= " OR competi_id='0$competi_id'";
                            }
                            $defs .= " ORDER BY competi_id ASC,active DESC LIMIT 1";
                            $resultD = $DRW->query($defs,$DRW_read);
                            if($DRW->num_rows( $resultD ) > 0){
                                //$dataD = $DRW->fetch_assoc($resultD);
                                //$chicago_file = '/dachicagorecordsftp/04-06-16/14606_12_51_022316telecomX86515/KM/14606_12_51_022316telecomX86515_00000606.PDF';
                                $pos = strpos(basename($chicago_file), $DMSource);
                                if ($pos === false) {//echo $DMSource;die;
                                    //$copyname = $DMSource.'_'.str_replace('pdf_', $productID, $pdfName);
                                    $copyname = $DMSource.'_'.$productID.'.pdf';
                                    $copyname = preg_replace('/\s+/', '_', $copyname);
                                }else{
                                    $arrBase = explode(".", basename($chicago_file));
                                    $ext = end($arrBase);
                                    array_pop($arrBase);
                                    //array_push($arrBase,str_replace('pdf_', $productID, $pdfName));
                                    array_push($arrBase,$productID.'.pdf');
                                    $pname = implode("_", $arrBase);
                                    $copyname = preg_replace('/\s+/', '_', $pname);
                                }
                            }
                            $DRW->free_result($resultD);
                        }else{      
                            $arrBase = explode(".", basename($chicago_file));
                            $ext = end($arrBase);
                            array_pop($arrBase);
                            //array_push($arrBase,str_replace('pdf_', $productID, $pdfName));
                            array_push($arrBase,$productID.'.pdf');
                            $pname = implode("_", $arrBase);
                            $copyname = preg_replace('/\s+/', '_', $pname);                        
                        }
                    }
                    if(!empty($copyname)){
                        $moveTo = dirname(__FILE__).'/dachicagorecordsftp/telecomftp/'.$copyname;
                    }
                    //echo $moveTo;die;
//                    else{
//                        $ftp_date = $pdfDate;
//                        $newFileName = $DMSource.'_'.date("mdy",strtotime($ftp_date)).'_'.$pdfName;
//                        $moveTo =  dirname(__FILE__).'/dachicagorecordsftp/'.$newFileName;
//                    }
                    if(!empty($moveFrom) && !empty($moveTo)){
                        //echo $moveFrom.' </br> '.$moveTo.'</br>';//die;                        
                        if(file_exists($moveTo)){                            
                            $dupfilePath = dirname(__FILE__).'/dachicagorecordsftp/telecomftp_dup/';
                            $moveTo = $dupfilePath.basename($chicago_file);
                            if(!is_dir($dupfilePath)){
                                if(mkdir($dupfilePath,0777,true)){
                                }else{
                                    echo $dupfilePath;die;
                                }
                                @chmod($dupfilePath,0777);
                                @chown($dupfilePath,'apache');
                            }
                        
                            $nname = basename($moveTo); 
                            $arrName = explode(".", $nname);
                            $ext = end($arrName);
                            array_pop($arrName);
                            array_push($arrName,$productID);
                            $cname = implode("_", $arrName).'.'.$ext;
                            $moveTo = str_replace($nname,$cname,$moveTo);
                            if(file_exists($moveTo)){
                                $fileAlreadyAvailable[$n]['filepath'] = $absPath;
                                $fileAlreadyAvailable[$n]['productID'] = $productID;
                                $fileAlreadyAvailable[$n]['date'] = $pdfDate;
                                //if file still available
                                continue;
                            }
                            if(copy($moveFrom, $moveTo)){
                            //if(1==1){
                                $arrCsv = array();
                                $i = 0;
                                $fnm = basename($moveTo);
                                $csvfile = "z:\\dachicagorecordsftp\\telecomftp_dup\\".$fnm;
                                $arrCsv[$i]['filepath'] = $csvfile;
                                $arrCsv[$i]['date'] = $ftp_date;
                                $arrCsv[$i]['status'] = 0;
                                $arrCsv[$i]['match'] = 0;   
                                if(count($arrCsv)>0){
                                    $csvName = date('Y-m-d').'_search_telecom_dup_input.csv';
                                    chicagoftpCsv($arrCsv, $csvName);
                                    $arrCsv = array();
                                    //echo 'done';
                                }
                            }else{
                                continue;
                            }
                            $moveFrom = $moveTo = '';
//                            array_push($arrName, time());
//                            array_push($arrName, rand());
//                            $cname = implode("_", $arrName).'.pdf';
//                            $moveTo = str_replace($nname,$cname,$moveTo);
                        }else{
                            //echo $moveFrom.' </br> '.$moveTo.'</br>';die;
                            $filePath = dirname(__FILE__).'/dachicagorecordsftp/telecomftp/';
                            //echo $filePath;die;
                            if(!is_dir($filePath)){
                                if(mkdir($filePath,0777,true)){
                                }else{
                                    echo $filePath;die;
                                }
                                @chmod($filePath,0777);
                                @chown($filePath,'apache');
                            }
                            if(copy($moveFrom, $moveTo)){
                            //if(1==1){
                                $arrCsv = array();
                                $i = 0;
                                $fnm = basename($moveTo);
                                $csvfile = "z:\\dachicagorecordsftp\\telecomftp\\".$fnm;
                                $arrCsv[$i]['filepath'] = $csvfile;
                                $arrCsv[$i]['date'] = $ftp_date;
                                $arrCsv[$i]['status'] = 0;
                                $arrCsv[$i]['match'] = 0;   
                                if(count($arrCsv)>0){
                                    $csvName = date('Y-m-d').'_search_telecom_input.csv';
                                    chicagoftpCsv($arrCsv, $csvName);
                                    $arrCsv = array();
                                    //echo 'done';
                                }
                            }else{
                                //continue;
                            }
                            $moveFrom = $moveTo = '';
                        }                        
                    }
                }else{
                    //continue;
                    $fileNotAvailable[$n]['filepath'] = $absPath;
                    $fileNotAvailable[$n]['productID'] = $productID;
                    $fileNotAvailable[$n]['date'] = $pdfDate;
                }
                
            }
        }
        //pr($arrCsv);
//        if(count($arrCsv)>0){
//            array_push($inputFiles, $arrCsv);
//        }   
        //pr($fileNotAvailable);die;
        
        if($num == $n){
            //echo 'done';die;
        }
        
    }  
    if(count($fileNotAvailable)>0){
        $csvName2 = date('Y-m-d').'_search_telecom_file_not_available.csv';
        chicagoftpCsv($fileNotAvailable, $csvName2);
    }
    if(count($fileAlreadyAvailable)>0){
        $csvName3 = date('Y-m-d').'_search_telecom_file_already_available.csv';
        chicagoftpCsv($fileAlreadyAvailable, $csvName3);
    }
}
//echo count($inputFiles);//die;
//if(count($inputFiles)>0){
//    chicagoftpCsv($inputFiles);
//    echo 'done';
//}
//function chicagoftpCsv($array = array(), $csvName){
//    //echo "CSV Rows => ".count($array);
//    if (count($array) == 0) {
//      return null;
//    }
//    //ob_start();
//    $root = dirname(__FILE__);
//    $filename= '';
//    if(!empty($csvName)){
//        $filename = $root.'/dacsv/'.$csvName;
//    }
//    //unlink($filename);
//    
//    if (!file_exists($filename)){      
//        $df = fopen($filename, 'w');
//        //fputcsv($df, ['Input File', 'Date', 'Status', 'Matched(%)', 'Matched File']);
//        my_fputcsv($df, ['Input File', 'Date', 'Status', 'Matched(%)', 'Matched File']);
//        fclose($df);
//    }   
//    $df = fopen($filename, 'a');
//    foreach ($array as $row) {
//       //fputcsv($df, $row);
//       my_fputcsv($df, $row);
//    }
//    fclose($df);
//    //die;
//    //return ob_get_clean();
//}
//function my_fputcsv($handle, $fieldsarray, $delimiter = ",", $enclosure ='"'){
//    $glue = $enclosure . $delimiter . $enclosure;
//    return fwrite($handle, $enclosure . implode($glue,$fieldsarray) . $enclosure."\r\n");
//}

echo '</br></br>End: '.date("Y-m-d H:i:s");