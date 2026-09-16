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
require_once "Mail.php";
require_once "Mail/mime.php";
//echo 'Start: '.date("Y-m-d H:i:s").'</br></br>';
########################################
function pr($str){
    echo '<pre>';print_r($str);
}
#########################################
$backday = (!empty($_REQUEST['p1']))?trim($_REQUEST['p1']):0;
$action = (!empty($_REQUEST['p2']))?trim($_REQUEST['p2']):'';
if(isset($_SERVER['argc']) && $_SERVER['argc']>0) {
    $backday = (!empty($_SERVER['argv'][1]))?trim($_SERVER['argv'][1]):$backday;
    $action = !empty($_SERVER['argv'][2])?trim($_SERVER['argv'][2]):$action;
}

$day = (int)$backday;
//if(empty($day))$day=1;
if(!empty($day)){
    $csvdate = date("Y-m-d", strtotime(" -$day day"));
}else{
    $csvdate = date("Y-m-d");
}

if($action=='search'){
    ######################################
    ////////update search_input.csv///////
    ######################################
    $arrLog = array();
    $arrLog[2] = date("Y-m-d H:i:s");
    ######################## Search.csv ############################################
    $x = 1;
    $prev_searchData = $search_inputData = $search_outputData = $new_search_outputData = array();
    $iopen = dirname(__FILE__)."/damaxmailcsv/".$csvdate."/".$csvdate."_search_input.csv";
    if(file_exists($iopen)){
        @chmod($iopen,0777);
        @chown($iopen,'apache');
    }
    if (($search_input = fopen($iopen, "r")) !== FALSE) {
        while (($search_data = fgetcsv($search_input, 1000, ",")) !== FALSE) {
            if($x > 1){
                if($search_data[2] == 0){
                    $search_inputData[] = $search_data;
                }else{
                    $prev_searchData[] = $search_data;
                }

            }
            $x++;
        }
        fclose($search_input);
    }else{
        echo "Unable to open file: ".$iopen;
    }
    $y =1;
    $oopen = dirname(__FILE__)."/damaxmailcsv/".$csvdate."/".$csvdate."_search_output.csv";
    if(file_exists($oopen)){
        @chmod($oopen,0777);
        @chown($oopen,'apache');
    }
    if (($handle = fopen($oopen, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {        
            if($y > 1){
                $search_outputData[] = $data;
            }
            $y++;
        }
        fclose($handle);
    }else{
        echo "Unable to open file: ".$oopen;
    }
    //pr($search_inputData);pr($search_outputData);die;
    if(!empty($search_inputData) && !empty($search_outputData)){
        $arrAppend = [];
        $i = 0;
        foreach($search_inputData as $idata){
            foreach($search_outputData as $odata){            
                if($idata[0]==$odata[0]){
                    $idata[2] = 1;
                    $arrAppend[$i] = $idata;
                    $odata[5] = $idata[4];
                    $j = $i+1;
                    $new_search_outputData[$j] = $odata;
                }else{
                    $arrAppend[$i] = $idata;
                }
            }
            $i++;
        }
        //pr($new_search_outputData);die;
        //pr($arrAppend);
        if(!empty($arrAppend)){
            $prev = dirname(__FILE__)."/damaxmailcsv/".$csvdate."/".$csvdate."_search_input.csv";
            $new = dirname(__FILE__)."/damaxmailcsv/".$csvdate."/".date("Y-m-d H:i:s")."_".$csvdate."_search_input.csv";

            $arrSearch = array_merge($prev_searchData, $arrAppend);
            if(rename($prev,$new)){
                @chmod($new,02755);
                @chown($new,'apache');
                damaxmailUpdateCsv($arrSearch,$prev);
                echo 'done2';
                $arrLog[0] = 'SearchUpdate(c)'; 
                $arrLog[1] = $csvdate."_search_input.csv";
                $arrLog[3] = date("Y-m-d H:i:s");            
                $arrLog[4] = 'Success';
            }else{
                $arrLog[0] = 'SearchUpdate(c)'; 
                $arrLog[1] = $csvdate."_search_input.csv";
                $arrLog[3] = date("Y-m-d H:i:s");            
                $arrLog[4] = 'Failed';
            }
            //output
            $prevo = dirname(__FILE__)."/damaxmailcsv/".$csvdate."/".$csvdate."_search_output.csv";
            $newo = dirname(__FILE__)."/damaxmailcsv/".$csvdate."/".date("Y-m-d H:i:s")."_".$csvdate."_search_output.csv";
            //pr($new_search_outputData);die;
            if(rename($prevo,$newo)){
                @chmod($new,02755);
                @chown($new,'apache');
                damaxmailUpdateCsv($new_search_outputData,$prevo);
                echo 'done2';
                $arrLog[0] = 'SearchUpdate(c)';   
                $arrLog[1] = $csvdate."_search_output.csv";
                $arrLog[3] = date("Y-m-d H:i:s");
                $arrLog[4] = 'Success';
            }else{
                $arrLog[0] = 'SearchUpdate(c)';   
                $arrLog[1] = $csvdate."_search_output.csv";
                $arrLog[3] = date("Y-m-d H:i:s");
                $arrLog[4] = 'Failed';
            }

        }
    }
    if(!empty($arrLog[0])){
        ksort($arrLog);
        $localLog = dirname(__FILE__)."/damaxmailcsv/".$csvdate."/log.csv";
        if (!file_exists($localLog)){ 
            $df = fopen($localLog, 'w+');
            fclose($df);
        }
        updateLog($arrLog,$localLog);

        $globalLog = dirname(__FILE__)."/damaxmailcsv/".date('Y-m',strtotime($csvdate))."_daLog.csv";
        if (!file_exists($globalLog)){  
            $df = fopen($globalLog, 'w+');
            fclose($df);
        }
        updateLog($arrLog,$globalLog);
    }
    //import search_output.csv into competiscan CITI FTP 
    if(!empty($arrLog[4]) && $arrLog[4]=='Success'){
        //sleep(60);
        //exec("/usr/bin/php ".dirname(__FILE__)."/cron_damaxmail_import.php $day> /dev/null 2>/dev/null &");
        $cmd = "/usr/bin/php ".dirname(__FILE__)."/cron_damaxmail_import.php $day";
        $sql = "INSERT IGNORE INTO da_maxmail_bg_scripts SET command = '".$cmd."'";                        
        if($DRW->query($sql,$DRW_main)){
            //$output = exec($cmd . " > /dev/null &");// do not wait
            $output = exec($cmd . " 2>&1", $output);//wait for the response
        }
        echo 'good';//die;
    }else{
        $sql = "INSERT IGNORE INTO da_maxmail_bg_scripts SET command = 'search_output.csv not generated'";                        
        if($DRW->query($sql,$DRW_main)){
            //$output = exec($cmd . " 2>&1", $output);//wait for the response
        }
    }
}else{
    //initialization
    $arrLog = array();
    $arrLog[2] = date("Y-m-d H:i:s");
    #######################################
    ///////update index_input.csv//////////
    #######################################
    $a = 1;
    $prev_inputData = $index_inputData = $index_outputData = array();
    if (($index_input = fopen(dirname(__FILE__)."/damaxmailcsv/".$csvdate."/".$csvdate."_index_input.csv", "r")) !== FALSE) {
        while (($index_data = fgetcsv($index_input, 1000, ",")) !== FALSE) {
            //excude headlines first & grab other rows
            if($a > 1){
                if($index_data[2] == 0){
                    $index_inputData[] = $index_data;
                }else{
                    $prev_inputData[] =   $index_data;
                }        
            }
            $a++;
        }
        fclose($index_input);
    }
    $b =1;
    if (($handle = fopen(dirname(__FILE__)."/damaxmailcsv/".$csvdate."/".$csvdate."_index_output.csv", "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            //excude headlines first & grab other rows
            if($b > 1){
                $index_outputData[] = $data;
            }
            $b++;
        }
        fclose($handle);
    }
    //pr($index_inputData);pr($index_outputData);
    if(!empty($index_inputData) && !empty($index_outputData)){
        $arrAppend = [];
        $j = 0;
        foreach($index_inputData as $idata){
            foreach($index_outputData as $odata){
                if($idata[0]==$odata[0]){
                    $idata[2] = 1;
                    $arrAppend[$j] = $idata;
                }else{
                    $arrAppend[$j] = $idata;
                }
            }
            $j++;
        }//pr($arrAppend);
        if(!empty($arrAppend)){
            $prev = dirname(__FILE__)."/damaxmailcsv/".$csvdate."/".$csvdate."_index_input.csv";
            $new = dirname(__FILE__)."/damaxmailcsv/".$csvdate."/".date("Y-m-d H:i:s")."_".$csvdate."_index_input.csv";
            //damaxmailUpdateCsv($arrAppend, $prev);
            $arrInput = array_merge($prev_inputData, $arrAppend);
            if(rename($prev,$new)){
                @chmod($new,02755);
                @chown($new,'apache');
                damaxmailUpdateCsv($arrInput, $prev);
                echo 'done1';
                $arrLog[0] = 'IndexUpdate(c)'; 
                $arrLog[1] = $csvdate."_index_input.csv";
                $arrLog[3] = date("Y-m-d H:i:s");            
                $arrLog[4] = 'Success';
            }else{
                $arrLog[0] = 'IndexUpdate(c)'; 
                $arrLog[1] = $csvdate."_index_input.csv";
                $arrLog[3] = date("Y-m-d H:i:s");            
                $arrLog[4] = 'Failed';
            }
        }

    }else{
    //    $arrLog[0] = 'IndexUpdate';    
    //    $arrLog[2] = date("Y-m-d H:i:s");
    //    $arrLog[3] = $csvdate."_index_input.csv";
    //    $arrLog[4] = 'NA';
    }
    if(!empty($arrLog[0])){
        ksort($arrLog);
        $localLog = dirname(__FILE__)."/damaxmailcsv/".$csvdate."/log.csv";
        if (!file_exists($localLog)){ 
            $df = fopen($localLog, 'w+');
            fclose($df);
        }
        updateLog($arrLog,$localLog);

        $globalLog = dirname(__FILE__)."/damaxmailcsv/".date('Y-m',strtotime($csvdate))."_daLog.csv";
        if (!file_exists($globalLog)){  
            $df = fopen($globalLog, 'w+');
            fclose($df);
        }
        updateLog($arrLog,$globalLog);
    }
}
die;